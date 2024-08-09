<?php

namespace App\Http\Controllers;

use App\Models\AssessmentTypes;
use App\Models\CATypeMarksAllocation;
use App\Models\CourseAssessment;
use App\Models\CourseAssessmentScores;
use App\Models\EduroleBasicInformation;
use App\Models\EduroleCourses;
use App\Models\EduroleStudy;
use App\Models\StudentsContinousAssessment;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CoordinatorController extends Controller
{
    public function uploadCa(Request $request,$caType, $courseIdValue,$basicInformationId){
        $delivery = $request->delivery; 
        $courseId = Crypt::decrypt($courseIdValue);
        $caType = Crypt::decrypt($caType);
        
        $basicInformationId = Crypt::decrypt($basicInformationId);
        $studyId = $request->studyId;
        $getAssessmentType = AssessmentTypes::where('id', $caType)->first();
        $assessmentType = $getAssessmentType->assesment_type_name;
        // return $assessmentType;
        // return $studyId;

        // return $courseId;

        $results = $this->getCoursesFromEdurole()            
            ->where('courses.ID', $courseId)
            ->where('study.Delivery', $delivery)
            ->where('study.ProgrammesAvailable', $basicInformationId)
            ->first();
        
            return view('coordinator.uploadCa', compact('assessmentType','studyId','results', 'caType', 'courseId', 'basicInformationId', 'delivery'))
                ->with('info', 'Kindly note that you are uploading under ' . $delivery . ' education');

    }

    public function showCaWithin(Request $request,$courseId){
        $courseId = Crypt::decrypt($courseId);
        $studyId = $request->studyId;
        $assessmentDetails = CourseAssessment::select(
            'course_assessments.basic_information_id',
            'assessment_types.assesment_type_name',
            'assessment_types.id',
            'course_assessments.delivery_mode',
            DB::raw('count(course_assessments.course_assessments_id) as total')
        )
        ->where('course_assessments.course_id', $courseId)
        ->where('course_assessments.study_id', $studyId)
        ->join('assessment_types', 'assessment_types.id', '=', 'course_assessments.ca_type')
        ->groupBy('assessment_types.id','course_assessments.basic_information_id', 'assessment_types.assesment_type_name','course_assessments.delivery_mode')
        ->get();

        $courseInfo = EduroleCourses::where('ID', $courseId)->first();
        
        // return $assessmentDetails;
    

        // return $assessmentDetails;
        return view('admin.showCaInCourse', compact('courseInfo','assessmentDetails','courseId','studyId'));

    }

    public function courseCASettings(Request $request,$courseIdValue, $basicInformationId, $delivery){ 
        $courseId = Crypt::decrypt($courseIdValue);
        $delivery = Crypt::decrypt($delivery);
        $studyId = $request->studyId;        

        // return $delivery;
        $allAssesmentTypes = AssessmentTypes::all();
        $courseAssessmenetTypes = CATypeMarksAllocation::where('course_id', $courseId)
            ->where('delivery_mode', $delivery)
            ->where('study_id', $studyId)
            ->join('assessment_types', 'assessment_types.id', '=', 'c_a_type_marks_allocations.assessment_type_id')
            ->pluck('total_marks', 'assessment_type_id')
            ->toArray();
        // return $courseAssessmenetTypes;
        $course = EduroleCourses::where('ID', $courseId)->first();
    
        $marksToDeduct = !empty($courseAssessmenetTypes) ? array_sum($courseAssessmenetTypes) : 0;
    
        return view('coordinator.courseCASettings', compact('studyId','delivery','courseAssessmenetTypes', 'allAssesmentTypes', 'course', 'marksToDeduct','basicInformationId'));
    }
    
    public function viewOnlyProgrammesWithCa(){

        $coursesWithCA = $this->getCoursesFromLMMAX();
        // return $coursesFromLMMAX;
        $results = $this->getCoursesFromEdurole()
            // ->whereIn('courses.Name', $coursesFromLMMAX)
            ->get();
        $filteredResults = $results->filter(function ($item) use ($coursesWithCA) {
            foreach ($coursesWithCA as $course) {
                if ($item->CourseName == $course['course_code'] && $item->Delivery == $course['delivery_mode'] && $item->ProgrammesAvailable != 1 && $item->ProgrammesAvailable == $course['basic_information_id']) {
                    return true;
                }
            }
            return false;
        });
    
        $results = $filteredResults;

        // return $results;
        return view('admin.viewCoursesWithCa', compact('results'));
    }

    public function viewOnlyProgrammesWithCaForCoordinator($coordinatorId){

        $coursesWithCA = $this->getCoursesFromLMMAX();   

        $results = $this->getCoursesFromEdurole()
            
            ->where('basic-information.ID', $coordinatorId)
            ->get();
        // return $results;
        $filteredResults = $results->filter(function ($item) use ($coursesWithCA) {
            foreach ($coursesWithCA as $course) {
                if ($item->CourseName == $course['course_code'] && $item->Delivery == $course['delivery_mode'] && $item->ProgrammesAvailable != 1 && $item->ProgrammesAvailable == $course['basic_information_id']) {
                    return true;
                }
            }
            return false;
        });

        $results = $filteredResults;

        // return $results[0]->ID;
        return view('admin.viewCoursesWithCa', compact('results','coordinatorId'));

    }
    

    public function updateCourseCASetings(Request $request)
    {
        DB::beginTransaction();

        try {
            // Get the course ID and other required parameters from the request
            $courseId = $request->input('courseId');
            $basicInformationId = $request->input('basicInformationId');
            $delivery = $request->input('delivery');
            $studyId = $request->input('studyId');

            // Get the array of assessment types and marks allocated from the request
            $assessmentTypes = $request->input('assessmentType');
            $marksAllocated = $request->input('marks_allocated');
            $user = auth()->user();
            $userBasicInformationId = $user->basic_information_id;

            // Get the program information for validation
            $programmeInfo = $this->getCoursesFromEdurole()
                ->where('study.Delivery', $delivery)
                ->where('study.ProgrammesAvailable', $userBasicInformationId)
                ->where('study.ID', $studyId)
                ->first();

            // Retrieve existing assessment type IDs for the course
            $existingAssessmentTypeIds = CATypeMarksAllocation::where('course_id', $courseId)
                ->where('delivery_mode', $delivery)
                ->where('study_id', $studyId)
                ->pluck('assessment_type_id')
                ->toArray();

            // Remove assessment types that are no longer checked
            foreach ($existingAssessmentTypeIds as $existingAssessmentTypeId) {
                if (!array_key_exists($existingAssessmentTypeId, $assessmentTypes ?? [])) {
                    CATypeMarksAllocation::where('course_id', $courseId)
                        ->where('delivery_mode', $delivery)
                        ->where('study_id', $studyId)
                        ->where('assessment_type_id', $existingAssessmentTypeId)
                        ->delete();
                }
            }

            // Update or create the assessment type allocations
            foreach ($assessmentTypes as $assessmentTypeId => $isChecked) {
                if ($isChecked) {
                    $marks = $marksAllocated[$assessmentTypeId];
                    CATypeMarksAllocation::updateOrCreate(
                        [
                            'course_id' => $courseId,
                            'assessment_type_id' => $assessmentTypeId,
                            'delivery_mode' => $delivery,
                            'study_id' => $studyId
                        ],
                        [
                            'user_id' => auth()->user()->id,
                            'total_marks' => $marks
                        ]
                    );
                }
            }

            // Update related CA marks for students
            $courseAssessmenetTypes = CATypeMarksAllocation::where('course_id', $courseId)
                ->where('study_id', $studyId)
                ->where('delivery_mode', $delivery)
                ->join('assessment_types', 'assessment_types.id', '=', 'c_a_type_marks_allocations.assessment_type_id')
                ->get();

            $academicYear = 2024;
            $getCoure = EduroleCourses::where('ID', $courseId)->first();
            $courseCode = $getCoure->Name;

            foreach ($courseAssessmenetTypes as $courseAssessmentType) {
                $studentsInAssessmentType = CourseAssessmentScores::where('course_code', $courseCode)
                    ->where('delivery_mode', $delivery)
                    ->where('study_id', $studyId)
                    ->get();

                foreach ($studentsInAssessmentType as $studentNumber) {
                    $this->refreshCAMark(
                        $courseId,
                        $academicYear,
                        $courseAssessmentType->assessment_type_id,
                        $studentNumber->student_id,
                        $studentNumber->course_assessment_id,
                        $delivery,
                        $studentNumber->study_id
                    );
                }
            }

            DB::commit();

            // Redirect based on user role
            if ($user->hasRole('Coordinator')) {
                return redirect()->route('pages.upload')->with('success', $courseCode . ' CA settings updated successfully');
            } else {
                return redirect()->route('admin.viewCoordinatorsCourses', $basicInformationId)->with('success', $courseCode . ' CA settings updated successfully');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while updating the course CA settings. Please try again. Error: ' . $e->getMessage());
        }
    }


    public function editCaInCourse($courseAssessmenId,$courseId, $basicInformationId){
        $courseAssessmentId = Crypt::decrypt($courseAssessmenId);
        $courseId = Crypt::decrypt($courseId);
        $basicInformationId = Crypt::decrypt($basicInformationId);
        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->select('courses.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name','study.ID as StudyID', 'courses.Name as CourseName','courses.CourseDescription','basic-information.ID as basicInformationId')
            ->where('courses.ID', $courseId)
            ->first();
        $courseAssessment = CourseAssessment::where('course_assessments_id', $courseAssessmentId)->first();
        // $delivery;
        $delivery = $courseAssessment->delivery_mode;
        return view('coordinator.editCaInCourse', compact('delivery','results', 'courseId','courseAssessmentId','basicInformationId'));
    }

    public function viewAllCaInCourse(Request $request,$statusId, $courseIdValue, $basicInformationId, $delivery){
        $courseId = Crypt::decrypt($courseIdValue);
        $statusId = Crypt::decrypt($statusId);
        $basicInformationId = Crypt::decrypt($basicInformationId);
        $delivery = Crypt::decrypt($delivery);
        if($request->studyId ){
            $studyId = $request->studyId;
        }else{
            $result = $this->getCoursesFromEdurole()            
                ->where('courses.ID', $courseId)
                ->where('study.Delivery', $delivery)
                ->where('study.ProgrammesAvailable', $basicInformationId)
                ->first();
            $studyId = $result->StudyID;
        }
        

        
        // return $result;

        $courseDetails = EduroleCourses::where('ID', $courseId)->first();

        $results = CourseAssessment::where('course_id', $courseId)
            ->where('ca_type', $statusId)
            ->where('delivery_mode', $delivery)
            ->where('study_id', $studyId)
            // ->join('course_assessment_scores', 'course_assessments.id', '=', 'course_assessment_scores.course_assessment_id')
            ->orderBy('course_assessments.course_assessments_id', 'asc')
            ->get();
        // return $results;
        $assessmentType = $this->setAssesmentType($statusId);

        return view('coordinator.viewAllCaInCourse', compact('delivery','results', 'statusId', 'courseId','courseDetails','assessmentType','basicInformationId'));
    }

    private function setAssesmentType($statusId){
        $getAssesmntType = AssessmentTypes::where('id', $statusId)->first();
        return $getAssesmntType->assesment_type_name;
    }

    public function viewSpecificCaInCourse($statusId, $courseIdValue, $assessmentNumber){
        $courseId = Crypt::decrypt($courseIdValue);
        $statusId = Crypt::decrypt($statusId);
        $assessmentNumber = Crypt::decrypt($assessmentNumber);
        // return $statusId;
        // return $courseId;
        $results = CourseAssessment::where('course_assessments.course_assessments_id', $courseId)
            // ->where('ca_type', $statusId)
            ->join('course_assessment_scores', 'course_assessments.course_assessments_id', '=', 'course_assessment_scores.course_assessment_id')
            ->orderBy('course_assessments.created_at', 'asc')
            ->get();
        
        $delivery = $results[0]->delivery_mode;
            
        $resultsArrayStudentNumbers = $results->pluck('student_id')->toArray();
        
        // return $resultsFromBasicInformation;
        $courseEduroleId = $results[0]->course_id;
        $courseDetails = EduroleCourses::where('ID', $courseEduroleId)->first();
        $resultsFromBasicInformation= EduroleBasicInformation::join('student-study-link', 'student-study-link.StudentID', '=', 'basic-information.ID')
            ->join('study', 'study.ID', '=', 'student-study-link.StudyID')            
            ->join('schools', 'schools.ID', '=', 'study.ParentID')
            ->select('basic-information.ID', 'basic-information.FirstName','basic-information.StudyType', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.Name as Programme', 'schools.Name as School')
            ->whereIn('basic-information.ID', $resultsArrayStudentNumbers)
            ->get();
        // return $resultsFromBasicInformation;
        
        // Merge results
        $results = $results->map(function ($result) use ($resultsFromBasicInformation) {
            $result->basic_information = $resultsFromBasicInformation->firstWhere('ID', $result->student_id);
            return $result;
        });

        // return $results;
    
        $assessmentType = $this->setAssesmentType($statusId) .' '. $assessmentNumber;
        return view('coordinator.viewSpecificCaInCourse', compact('delivery','results', 'courseId','assessmentType','courseDetails','statusId'));
    }

    public function viewTotalCaInCourse($statusId, $courseIdValue, $basicInformationId,$delivery){
        $courseId = Crypt::decrypt($courseIdValue);
        $caType = Crypt::decrypt($statusId);
        $basicInformationId = Crypt::decrypt($basicInformationId);
        $delivery = Crypt::decrypt($delivery);
        $courseDetails = EduroleCourses::where('ID', $courseId)->first();
        $coursesInEdurole = $this->getCoursesFromEdurole()
                ->where('courses.ID', $courseId)
                ->where('study.ProgrammesAvailable', $basicInformationId)
                ->where('study.Delivery', $delivery)
                ->first();
        // return $courseDetails;
        // if($caType != 4){            
        $results = StudentsContinousAssessment::where('students_continous_assessments.course_id', $courseId)
            ->where('students_continous_assessments.delivery_mode', $delivery)
            ->where('students_continous_assessments.study_id', $coursesInEdurole->StudyID)
            // ->whereIn('ca_type', [1,2,3]) 
            ->join('course_assessments', 'course_assessments.course_assessments_id', '=', 'students_continous_assessments.course_assessment_id')
            ->select('students_continous_assessments.student_id', DB::raw('SUM(students_continous_assessments.sca_score) as total_marks'))
            ->groupBy('students_continous_assessments.student_id')
            ->get();
            
        // }else{
        //     $results = StudentsContinousAssessment::where('students_continous_assessments.course_id', $courseId)
        //         ->where('ca_type', 4) 
        //         ->select('students_continous_assessments.student_id', DB::raw('SUM(students_continous_assessments.sca_score) as total_marks'))
        //         ->groupBy('students_continous_assessments.student_id')
        //         ->get();
        // }

        $resultsArrayStudentNumbers = $results->pluck('student_id')->toArray();
        $resultsFromBasicInformation= EduroleBasicInformation::join('student-study-link', 'student-study-link.StudentID', '=', 'basic-information.ID')
            ->join('study', 'study.ID', '=', 'student-study-link.StudyID')            
            ->join('schools', 'schools.ID', '=', 'study.ParentID')
            ->select('basic-information.ID', 'basic-information.FirstName', 'basic-information.Surname','basic-information.StudyType', 'basic-information.PrivateEmail', 'study.Name as Programme', 'schools.Name as School')
            ->whereIn('basic-information.ID', $resultsArrayStudentNumbers)
            ->get();
        // return $resultsFromBasicInformation;
        $results = $results->map(function ($result) use ($resultsFromBasicInformation) {
            $result->basic_information = $resultsFromBasicInformation->firstWhere('ID', $result->student_id);
            return $result;
        });
        // return $results;
        return view('coordinator.viewTotalCaInCourse', compact('delivery','results', 'statusId', 'courseId','courseDetails')); 
    }

    public function deleteCaInCourse(Request $request, $courseAssessmenId, $courseId){
        $courseAssessmentId = Crypt::decrypt($courseAssessmenId);
        $courseId = Crypt::decrypt($courseId);
        $courseAssessment = CourseAssessment::where('course_assessments_id', $courseAssessmentId)->first();
        $courseAssessmentsScores = CourseAssessmentScores::where('course_assessment_id', $courseAssessmentId)->pluck('student_id')->toArray();
        $delivery = $request->delivery;
        // return $courseId;
        // return $courseAssessments;
        // return $request->academicYear;
        // return $request->ca_type;
        CourseAssessment::where('course_assessments_id', $courseAssessmentId)->delete();
        foreach ($courseAssessmentsScores as $entry){
            
            $this->renewCABeforeDelete($courseId, $request->academicYear, $request->ca_type, trim($entry),$courseAssessmentId,$delivery, $courseAssessment->study_id);
        }
        // CourseAssessment::where('course_assessments_id', $courseAssessmentId)->delete();
        
        // CourseAssessmentScores::where('course_assessment_id', $courseAssessmentId)->delete();
        return redirect()->back()->with('success', 'Data deleted successfully');
    }

    public function importCAFromExcelSheet(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M'); // Adjust as needed
        
        // Validate the form data
        $request->validate([
            'excelFile' => 'required|mimes:xls,xlsx,csv',
            'academicYear' => 'required',
            'ca_type' => 'required',
            'course_id' => 'required',
            'course_code' => 'required',
            'basicInformationId' => 'required',
            'delivery' => 'required',
            'study_id' => 'required',
        ]);
        $expectedColumnCount = 2;

        try {
            if ($request->hasFile('excelFile')) {
                $file = $request->file('excelFile');
                $filePath = $file->getPathname();

                if (!is_readable($filePath)) {
                    return back()->with('error', 'The uploaded file could not be read. Please try again.');
                }

                $reader = ReaderEntityFactory::createXLSXReader();
                $reader->open($filePath);

                // Check if the workbook has only one sheet
                $sheetCount = iterator_count($reader->getSheetIterator());
                if ($sheetCount > 1) {
                    $reader->close();
                    return back()->with('error', 'The uploaded Excel workbook must contain exactly one sheet.');
                }

                $reader->close();
                $reader->open($filePath); // Re-open to reset the iterator

                $data = [];
                foreach ($reader->getSheetIterator() as $sheet) {
                    foreach ($sheet->getRowIterator() as $row) {
                        $actualColumnCount = count($row->getCells());
                        if ($actualColumnCount != $expectedColumnCount) {
                            $reader->close();
                            return back()->with('error', "The uploaded Excel sheet must contain exactly $expectedColumnCount columns.");
                        }
                        try {
                            // Clean and trim the student number
                            $studentNumber = trim($row->getCellAtIndex(0)->getValue());
                            if (!is_numeric($studentNumber) || strlen($studentNumber) < 7 || strlen($studentNumber) > 10) {
                                throw new \Exception("Student number contains special characters or is not within the valid length range.");
                            }

                            // Clean and trim the mark, then convert it to a float
                            $mark = trim($row->getCellAtIndex(1)->getValue());
                            if (!is_numeric($mark)) {
                                throw new \Exception("Mark is not a valid number.");
                            }
                            $mark = (float)$mark;
                        } catch (\Exception $e) {
                            $reader->close();
                            return back()->with('error', 'Error in row: ' . $e->getMessage());
                        }

                        $data[] = [
                            'student_number' => $studentNumber,
                            'mark' => $mark,
                        ];
                    }
                }
                $reader->close();

                DB::beginTransaction();
                try {
                    // Create a new course assessment
                    $newAssessment = CourseAssessment::create([
                        'course_id' => $request->course_id,
                        'ca_type' => $request->ca_type,
                        'description' => $request->description,
                        'academic_year' => $request->academicYear,
                        'basic_information_id' => $request->basicInformationId,
                        'delivery_mode' => $request->delivery,
                        'study_id' => $request->study_id,
                    ]);

                    foreach ($data as $entry) {
                        CourseAssessmentScores::updateOrCreate(
                            [
                                'course_assessment_id' => $newAssessment->course_assessments_id,
                                'student_id' => trim($entry['student_number']),
                                'course_code' => $request->course_code,
                                'delivery_mode' => $request->delivery,
                                'study_id' => $request->study_id,
                            ],
                            [
                                'cas_score' => $entry['mark'],
                            ]
                        );
                        $this->calculateAndSubmitCA($request->course_id, $request->academicYear, $request->ca_type, trim($entry['student_number']), $newAssessment->course_assessments_id, $request->delivery, $request->study_id);
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->with('error', 'An error occurred while importing the data. Please try again. Error: ' . $e->getMessage());
                }
            }

            $statusIdToRoute = encrypt($request->ca_type);
            $courseIdToRoute = encrypt($newAssessment->course_assessments_id);
            $assessmentNumber = encrypt(1);
            $delivery = encrypt($request->delivery);

            return redirect()->route('coordinator.viewSpecificCaInCourse', ['statusId' => $statusIdToRoute, 'courseIdValue' => $courseIdToRoute, 'assessmentNumber' => $assessmentNumber])->with('success', 'Data imported successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred during the upload process. Please try again. Error: ' . $e->getMessage());
        }
    }


    public function updateCAFromExcelSheet(Request $request)
    {
        set_time_limit(1200000);

        // Validate the form data
        $request->validate([
            'excelFile' => 'required|mimes:xls,xlsx,csv',
            'academicYear' => 'required',
            'course_assessment_id' => 'required',
            'course_id' => 'required',
            'course_code' => 'required',
            'basicInformationId' => 'required',
            'delivery' => 'required',
            'study_id' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $newAssessment = CourseAssessment::where('course_assessments_id', $request->course_assessment_id)
                ->update([
                    'academic_year' => $request->academicYear,
                    'basic_information_id' => $request->basicInformationId,
                ]);

            $getCaType = CourseAssessment::where('course_assessments_id', $request->course_assessment_id)->first();
            $caType = $getCaType->ca_type;
            $studyId = $getCaType->study_id;

            if ($request->hasFile('excelFile')) {
                $file = $request->file('excelFile');

                // Initialize the Box/Spout reader
                $reader = ReaderEntityFactory::createXLSXReader();
                $reader->open($file->getPathname());

                $data = [];
                foreach ($reader->getSheetIterator() as $sheet) {
                    foreach ($sheet->getRowIterator() as $row) {
                        try {
                            $studentNumber = $row->getCellAtIndex(0)->getValue();
                            $mark = $row->getCellAtIndex(1)->getValue();
                        } catch (\Exception $e) {
                            DB::rollBack();
                            return back()->with('error', 'Please format the excel sheet correctly.');
                        }

                        $data[] = [
                            'student_number' => $studentNumber,
                            'mark' => $mark,
                        ];
                    }
                }
                $reader->close();

                foreach ($data as $entry) {
                    CourseAssessmentScores::updateOrCreate([
                        'course_assessment_id' => $request->course_assessment_id,
                        'student_id' => trim($entry['student_number']),
                        'course_code' => $request->course_code,
                        'delivery_mode' => $request->delivery,
                        'study_id' => $studyId,
                    ],[
                        'cas_score' => $entry['mark'],
                    ]);
                    $this->calculateAndSubmitCA($request->course_id, $request->academicYear, $caType, trim($entry['student_number']), $request->course_assessment_id, $request->delivery, $studyId);
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Data imported successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to import data: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while importing the data. Please try again.');
        }
    }

    
    private function calculateScores($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery, $studyId, $excludeCurrent = false)
    {
        DB::beginTransaction();

        try {
            // Fetch CA scores
            $caScores = $this->getCourseAssessmentScores($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery, $studyId, $excludeCurrent);
            $total = $caScores->sum('mark');            
            // Fetch the count of assessments
            $count = $this->getNumberOfAssessmnets($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery, $studyId, $excludeCurrent);
            Log::info('Total score: ' . $total . ' Count: ' . $count);
            // Fetch max score
            $maxScore = $this->getMaxScore($courseId, $caType, $delivery, $studyId);
            // Calculate average and adjusted average
            $average = $count > 0 ? $total / $count : 0;
            $adjustedAverage = ($average / 100) * $maxScore;
            // Save or update the student's CA
            $this->saveOrUpdateStudentCA($studentNumber, $courseId, $academicYear, $caType, $courseAssessmentId, $adjustedAverage, $delivery, $studyId);
            // Commit the transaction if everything is successful
            DB::commit();
        } catch (\Exception $e) {
            // Rollback the transaction if any exception occurs
            DB::rollBack();
            // Log the error message
            Log::error('Failed to calculate scores: ' . $e->getMessage());
            // Optionally, you can throw the exception to handle it in the calling method
            throw $e;
        }
    }


    private function getNumberOfAssessmnets($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId,$delivery,$studyId, $excludeCurrent){
        return CourseAssessmentScores::where('course_assessments.course_id', $courseId)
            ->where('course_assessments.academic_year', $academicYear)
            ->where('course_assessments.ca_type', $caType)
            ->where('course_assessments.delivery_mode', $delivery)
            // ->where('course_assessment_scores.student_id', $studentNumber)
            ->where('course_assessment_scores.study_id', $studyId)
            ->when($excludeCurrent, function ($query) use ($courseAssessmentId) {
                return $query->where('course_assessment_scores.course_assessment_id', '!=', $courseAssessmentId);
            })
            ->join('course_assessments', 'course_assessments.course_assessments_id', '=', 'course_assessment_scores.course_assessment_id')
            ->distinct('course_assessment_scores.course_assessment_id')
            ->count('course_assessment_scores.course_assessment_id');
    }
    
    private function getCourseAssessmentScores($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId,$delivery,$studyId, $excludeCurrent){
        return CourseAssessmentScores::where('course_assessments.course_id', $courseId)
            ->where('course_assessments.academic_year', $academicYear)
            ->where('course_assessments.ca_type', $caType)
            ->where('course_assessments.delivery_mode', $delivery)
            ->where('course_assessment_scores.student_id', $studentNumber)
            ->where('course_assessment_scores.study_id', $studyId)
            ->when($excludeCurrent, function ($query) use ($courseAssessmentId) {
                return $query->where('course_assessment_scores.course_assessment_id', '!=', $courseAssessmentId);
            })
            ->select('course_assessment_scores.cas_score as mark')
            ->join('course_assessments', 'course_assessments.course_assessments_id', '=', 'course_assessment_scores.course_assessment_id')
            ->get();
    }
    
    private function getMaxScore($courseId, $caType, $delivery, $studyId){
        $courseAssessmenetTypes = CATypeMarksAllocation::where('c_a_type_marks_allocations.course_id', $courseId)
            ->where('c_a_type_marks_allocations.assessment_type_id', $caType)
            ->where('c_a_type_marks_allocations.delivery_mode', $delivery)
            ->where('c_a_type_marks_allocations.study_id', $studyId)                    
            ->select('c_a_type_marks_allocations.total_marks')
            ->first();
        return $courseAssessmenetTypes->total_marks;
    }
    
    private function saveOrUpdateStudentCA($studentNumber, $courseId, $academicYear, $caType, $courseAssessmentId, $adjustedAverage, $delivery, $studyId){
        $studentCA = StudentsContinousAssessment::firstOrNew(['student_id' => $studentNumber, 'course_id' => $courseId, 'academic_year' => $academicYear, 'ca_type' => $caType, 'delivery_mode' => $delivery, 'study_id' => $studyId]);
        $studentCA->course_assessment_id = $courseAssessmentId;
        $studentCA->sca_score = $adjustedAverage;
        $studentCA->save();
    }

    public function refreshAllStudentsMarks($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery, $studyId){
        $this->refreshCAMark($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery, $studyId);
    }
    
    private function calculateAndSubmitCA($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery, $studyId){
        $this->calculateScores($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery, $studyId, false);
    }
    
    private function renewCABeforeDelete($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery,$studyId){
        $this->calculateScores($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery, $studyId, true);
    }
    
    private function refreshCAMark($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery, $studyId){        
        $this->calculateScores($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery, $studyId, false);        
    }
}
