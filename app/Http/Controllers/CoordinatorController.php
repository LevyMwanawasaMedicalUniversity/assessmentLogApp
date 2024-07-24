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

        // return $courseId;

        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->select('courses.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName','courses.CourseDescription','basic-information.ID as basicInformationId')
            ->where('courses.ID', $courseId)
            ->first();
        
            return view('coordinator.uploadCa', compact('results', 'caType', 'courseId', 'basicInformationId', 'delivery'))
                ->with('info', 'Kindly note that you are uploading under ' . $delivery . ' education');

    }

    public function showCaWithin($courseId){
        $courseId = Crypt::decrypt($courseId);
        $assessmentDetails = CourseAssessment::select(
            'course_assessments.basic_information_id',
            'assessment_types.assesment_type_name',
            'assessment_types.id',
            DB::raw('count(course_assessments.course_assessments_id) as total')
        )
        ->where('course_assessments.course_id', $courseId)
        ->join('assessment_types', 'assessment_types.id', '=', 'course_assessments.ca_type')
        ->groupBy('assessment_types.id','course_assessments.basic_information_id', 'assessment_types.assesment_type_name')
        ->get();
    

        // return $assessmentDetails;
        return view('admin.showCaInCourse', compact('assessmentDetails','courseId'));

    }

    public function courseCASettings($courseIdValue, $basicInformationId){ 
        $courseId = Crypt::decrypt($courseIdValue);
        $allAssesmentTypes = AssessmentTypes::all();
        $courseAssessmenetTypes = CATypeMarksAllocation::where('course_id', $courseId)
            ->join('assessment_types', 'assessment_types.id', '=', 'c_a_type_marks_allocations.assessment_type_id')
            ->pluck('total_marks', 'assessment_type_id')
            ->toArray();
        $course = EduroleCourses::where('ID', $courseId)->first();
    
        $marksToDeduct = !empty($courseAssessmenetTypes) ? array_sum($courseAssessmenetTypes) : 0;
    
        return view('coordinator.courseCASettings', compact('courseAssessmenetTypes', 'allAssesmentTypes', 'course', 'marksToDeduct','basicInformationId'));
    }
    
    public function viewOnlyProgrammesWithCa(){

        $coursesFromLMMAX = $this->getCoursesFromLMMAX();
        // return $coursesFromLMMAX;

        $results = $this->getCoursesFromEdurole()
            ->whereIn('courses.Name', $coursesFromLMMAX)
            ->get();

        // return $results;
        return view('admin.viewCoursesWithCa', compact('results'));
    }

    public function viewOnlyProgrammesWithCaForCoordinator($coordinatorId){

        $coursesFromLMMAX = $this->getCoursesFromLMMAX();
        // return $coursesFromLMMAX;

        $results = $this->getCoursesFromEdurole()
            ->whereIn('courses.Name', $coursesFromLMMAX)
            ->where('basic-information.ID', $coordinatorId)
            ->get();
        // return $results;

        // return $results[0]->ID;
        return view('admin.viewCoursesWithCa', compact('results','coordinatorId'));

    }
    

    public function updateCourseCASetings(Request $request){
        // Get the course ID from the request
        $courseId = $request->input('courseId');
        $basicInformationId = $request->input('basicInformationId');
    
        // Get the array of assessment types and marks allocated from the request
        $assessmentTypes = $request->input('assessmentType');
        $marksAllocated = $request->input('marks_allocated');
        
        // Loop through the assessment types
        
        
        $existingAssessmentTypeIds = CATypeMarksAllocation::where('course_id', $courseId)
            ->pluck('assessment_type_id')
            ->toArray();
        // return $existingAssessmentTypeIds;
        $assessmentTypes = $assessmentTypes ?? [];
        foreach ($existingAssessmentTypeIds as $existingAssessmentTypeId) {
            // If the assessment type id is not in the request, delete it
            try{
                if (!array_key_exists($existingAssessmentTypeId, $assessmentTypes)) {
                    CATypeMarksAllocation::where('course_id', $courseId)
                        ->where('assessment_type_id', $existingAssessmentTypeId)
                        ->delete();
                }
            }catch(Exception $e){
                return redirect()->back()->with('error', 'An error occurred while updating the course CA settings. Please try again.');
            }
        }

        foreach ($assessmentTypes as $assessmentTypeId => $isChecked) {
            // If the checkbox for this assessment type was checked
            if ($isChecked) {
                // Get the marks allocated for this assessment type
                $marks = $marksAllocated[$assessmentTypeId];

                // Update or create a new record in the CATypeMarksAllocation model
                CATypeMarksAllocation::updateOrCreate(
                    [
                        'course_id' => $courseId,
                        'assessment_type_id' => $assessmentTypeId
                    ],
                    [
                        'user_id' => auth()->user()->id,
                        'total_marks' => $marks
                    ]
                );
            }
        }

        
        $courseAssessmenetTypes= CATypeMarksAllocation::where('course_id', $courseId)
            ->join('assessment_types', 'assessment_types.id', '=', 'c_a_type_marks_allocations.assessment_type_id')
            ->get();
        $academicYear = 2024;
        $getCoure = EduroleCourses::where('ID', $courseId)->first();
        $courseCode = $getCoure->Name;
        $studentssWithResults = CourseAssessmentScores::where('course_code', $courseCode)
            ->get();

        // return $courseAssessmenetTypes;
        
        

        // return $courseCode;

        foreach ($courseAssessmenetTypes as $courseAssessmentType){

            $studentsInAssessmentType = CourseAssessmentScores::where('course_code', $courseCode)
            // ->join('')    
            // ->where('course_assessment_id', $courseAssessmentType->assessment_type_id)
                ->get();
            // Log::info($studentsInAssessmentType);
            foreach ($studentsInAssessmentType as $studentNumber){
                $this->refreshCAMark($courseId, $academicYear, $courseAssessmentType->assessment_type_id , $studentNumber->student_id, $studentNumber->course_assessment_id);
            }
        }     

        //TO DO: ADD calculateAndSubmitCA TO THIS FUNCTION
        if(auth()->user()->hasRole('Coordinator')){
            return redirect()->route('pages.upload')->with('success', $courseCode.' CA settings updated successfully');
        }else{
            return redirect()->route('admin.viewCoordinatorsCourses',$basicInformationId)->with('success', $courseCode.' CA settings updated successfully');
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
            ->select('courses.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName','courses.CourseDescription','basic-information.ID as basicInformationId')
            ->where('courses.ID', $courseId)
            ->first();
        return view('coordinator.editCaInCourse', compact('results', 'courseId','courseAssessmentId','basicInformationId'));
    }

    public function viewAllCaInCourse($statusId, $courseIdValue, $basicInformationId, $delivery){
        $courseId = Crypt::decrypt($courseIdValue);
        $statusId = Crypt::decrypt($statusId);
        $basicInformationId = Crypt::decrypt($basicInformationId);
        $delivery = Crypt::decrypt($delivery);

        $courseDetails = EduroleCourses::where('ID', $courseId)->first();

        $results = CourseAssessment::where('course_id', $courseId)
            ->where('ca_type', $statusId)
            ->where('delivery_mode', $delivery)
            // ->join('course_assessment_scores', 'course_assessments.id', '=', 'course_assessment_scores.course_assessment_id')
            ->orderBy('course_assessments.course_assessments_id', 'asc')
            ->get();
        // return $results;
        $assessmentType = $this->setAssesmentType($statusId);

        return view('coordinator.viewAllCaInCourse', compact('results', 'statusId', 'courseId','courseDetails','assessmentType','basicInformationId'));
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
            
        $resultsArrayStudentNumbers = $results->pluck('student_id')->toArray();
        
        // return $resultsFromBasicInformation;
        $courseEduroleId = $results[0]->course_id;
        $courseDetails = EduroleCourses::where('ID', $courseEduroleId)->first();
        $resultsFromBasicInformation= EduroleBasicInformation::join('student-study-link', 'student-study-link.StudentID', '=', 'basic-information.ID')
            ->join('study', 'study.ID', '=', 'student-study-link.StudyID')            
            ->join('schools', 'schools.ID', '=', 'study.ParentID')
            ->select('basic-information.ID', 'basic-information.FirstName', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.Name as Programme', 'schools.Name as School')
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
        return view('coordinator.viewSpecificCaInCourse', compact('results', 'courseId','assessmentType','courseDetails','statusId'));
    }

    public function viewTotalCaInCourse($statusId, $courseIdValue, $basicInformationId,$delivery){
        $courseId = Crypt::decrypt($courseIdValue);
        $caType = Crypt::decrypt($statusId);
        $basicInformationId = Crypt::decrypt($basicInformationId);
        $delivery = Crypt::decrypt($delivery);
        $courseDetails = EduroleCourses::where('ID', $courseId)->first();
        // return $courseDetails;
        // if($caType != 4){            
        $results = StudentsContinousAssessment::where('students_continous_assessments.course_id', $courseId)
            // ->whereIn('ca_type', [1,2,3]) 
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
            ->select('basic-information.ID', 'basic-information.FirstName', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.Name as Programme', 'schools.Name as School')
            ->whereIn('basic-information.ID', $resultsArrayStudentNumbers)
            ->get();
        // return $resultsFromBasicInformation;
        $results = $results->map(function ($result) use ($resultsFromBasicInformation) {
            $result->basic_information = $resultsFromBasicInformation->firstWhere('ID', $result->student_id);
            return $result;
        });
        // return $results;
        return view('coordinator.viewTotalCaInCourse', compact('results', 'statusId', 'courseId','courseDetails')); 
    }

    public function deleteCaInCourse(Request $request, $courseAssessmenId, $courseId){
        $courseAssessmentId = Crypt::decrypt($courseAssessmenId);
        $courseId = Crypt::decrypt($courseId);
        $courseAssessments = CourseAssessmentScores::where('course_assessment_id', $courseAssessmentId)->pluck('student_id')->toArray();

        // return $courseId;
        // return $courseAssessments;
        // return $request->academicYear;
        // return $request->ca_type;
        CourseAssessment::where('course_assessments_id', $courseAssessmentId)->delete();
        foreach ($courseAssessments as $entry){
            
            $this->renewCABeforeDelete($courseId, $request->academicYear, $request->ca_type, trim($entry),$courseAssessmentId);
        }
        // CourseAssessment::where('course_assessments_id', $courseAssessmentId)->delete();
        
        // CourseAssessmentScores::where('course_assessment_id', $courseAssessmentId)->delete();
        return redirect()->back()->with('success', 'Data deleted successfully');
    }

    public function importCAFromExcelSheet(Request $request)
    {
        // return "Hello";
        set_time_limit(0);
        ini_set('memory_limit', '512M'); // Adjust as needed
        
        $request->validate([
            'excelFile' => 'required|mimes:xls,xlsx,csv',
            'academicYear' => 'required',
            'ca_type' => 'required',
            'course_id' => 'required',
            'course_code' => 'required',
            'basicInformationId' => 'required',
            'delivery' => 'required',
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
                // return $sheetCount;
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
                            $studentNumber = $row->getCellAtIndex(0)->getValue();
                            $mark = (float) $row->getCellAtIndex(1)->getValue();
                        } catch (\Exception $e) {
                            $reader->close();
                            return back()->with('error', 'Please format excel sheet correctly.');
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
                    $newAssessment = CourseAssessment::create([
                        'course_id' => $request->course_id,
                        'ca_type' => $request->ca_type,
                        'description' => $request->description,
                        'academic_year' => $request->academicYear,
                        'basic_information_id' => $request->basicInformationId,
                        'delivery_mode' => $request->delivery,
                    ]);

                    foreach ($data as $entry) {
                        CourseAssessmentScores::updateOrCreate(
                            [
                                'course_assessment_id' => $newAssessment->course_assessments_id,
                                'student_id' => trim($entry['student_number']),
                                'course_code' => $request->course_code,
                            ],
                            [
                                'cas_score' => $entry['mark'],
                            ]
                        );
                        $this->calculateAndSubmitCA($request->course_id, $request->academicYear, $request->ca_type, trim($entry['student_number']), $newAssessment->course_assessments_id);
                    }
                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    return back()->with('error', 'An error occurred while importing the data. Please try again. Error: ' . $e->getMessage());
                }
            }

            $statusIdToRoute = encrypt($request->ca_type);
            $courseIdToRoute = encrypt($newAssessment->course_assessments_id);
            $assessmentNumber = encrypt(1);
            $delivery = encrypt($request->delivery);

            return redirect()->route('coordinator.viewSpecificCaInCourse', ['statusId' => $statusIdToRoute, 'courseIdValue' => $courseIdToRoute, 'assessmentNumber' => $assessmentNumber])->with('success', 'Data imported successfully');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred during the upload process. Please try again. Error: ' . $e->getMessage());
        }
    }

    public function updateCAFromExcelSheet(Request $request){
        set_time_limit(1200000);
        // Validate the form data
        $request->validate([
            'excelFile' => 'required|mimes:xls,xlsx,csv',
            'academicYear' => 'required',
            'course_assessment_id' => 'required',            
            'course_id' => 'required',
            'course_code' => 'required',   
            'basicInformationId' => 'required',  
        ]);

        // return $request->basicInformationId;

        $newAssessment = CourseAssessment::where('course_assessments_id', $request->course_assessment_id)
            ->update([
                'academic_year' => $request->academicYear,
                'basic_information_id' => $request->basicInformationId,
            ]);
        $getCaType = CourseAssessment::where('course_assessments_id', $request->course_assessment_id)->first();
        // return $getCaType;
        $caType = $getCaType->ca_type;

        // return $caType;

        if ($request->hasFile('excelFile')) {
            $file = $request->file('excelFile');

            // Initialize the Box/Spout reader
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->open($file->getPathname());

        
            $data = [];
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    // Skip the header row
                    
                    try{
                        $studentNumber = $row->getCellAtIndex(0)->getValue();                    
                        $mark = $row->getCellAtIndex(1)->getValue();
                        // $courseCode = $row->getCellAtIndex(2)->getValue();
                    } catch (\Exception $e) {
                        return back()->with('error', 'Please format excel sheet correctly.');
                    }

                    $data[] = [
                        'student_number' => $studentNumber,
                        'mark' => $mark,
                    ];
                }
                
            }
            $reader->close();

            // try{
                foreach ($data as $entry){
                    CourseAssessmentScores::updateOrCreate([
                        'course_assessment_id' => $request->course_assessment_id,
                        'student_id' => trim($entry['student_number']),                        
                        'course_code' => $request->course_code,
                    ],[                        
                        'cas_score' => $entry['mark'],                      
                    ]);
                    $this->calculateAndSubmitCA($request->course_id, $request->academicYear, $caType, trim($entry['student_number']),$request->course_assessment_id);
                }
            // }catch(\Exception $e){
            //     return back()->with('error', 'An error occurred while importing the data. Please try again.');
            // }

        }

        return redirect()->back()->with('success', 'Data imported successfully');
        // return redirect()->route('coordinator.viewSpecificCaInCourse', ['statusId' => $statusIdToRoute, 'courseIdValue' => $courseIdToRoute, 'assessmentNumber' => $assessmentNumber])->with('success', 'Data imported successfully');
    }
    
    private function calculateScores($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $excludeCurrent = false){
        $caScores = $this->getCourseAssessmentScores($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $excludeCurrent);
        $total = $caScores->sum('mark');
        $count = $caScores->count();
        $maxScore = $this->getMaxScore($courseId, $caType);
        $average = $count > 0 ? $total / $count : 0;
        $adjustedAverage = ($average / 100) * $maxScore;
        $this->saveOrUpdateStudentCA($studentNumber, $courseId, $academicYear, $caType, $courseAssessmentId, $adjustedAverage);
    }
    
    private function getCourseAssessmentScores($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $excludeCurrent){
        return CourseAssessmentScores::where('course_assessments.course_id', $courseId)
            ->where('course_assessments.academic_year', $academicYear)
            ->where('course_assessments.ca_type', $caType)
            ->where('course_assessment_scores.student_id', $studentNumber)
            ->when($excludeCurrent, function ($query) use ($courseAssessmentId) {
                return $query->where('course_assessment_scores.course_assessment_id', '!=', $courseAssessmentId);
            })
            ->select('course_assessment_scores.cas_score as mark')
            ->join('course_assessments', 'course_assessments.course_assessments_id', '=', 'course_assessment_scores.course_assessment_id')
            ->get();
    }
    
    private function getMaxScore($courseId, $caType){
        $courseAssessmenetTypes = CATypeMarksAllocation::where('c_a_type_marks_allocations.course_id', $courseId)
                    ->where('c_a_type_marks_allocations.assessment_type_id', $caType)                    
                    ->select('c_a_type_marks_allocations.total_marks')
                    ->first();
        return $courseAssessmenetTypes->total_marks;
    }
    
    private function saveOrUpdateStudentCA($studentNumber, $courseId, $academicYear, $caType, $courseAssessmentId, $adjustedAverage){
        $studentCA = StudentsContinousAssessment::firstOrNew(['student_id' => $studentNumber, 'course_id' => $courseId, 'academic_year' => $academicYear, 'ca_type' => $caType]);
        $studentCA->course_assessment_id = $courseAssessmentId;
        $studentCA->sca_score = $adjustedAverage;
        $studentCA->save();
    }
    
    private function calculateAndSubmitCA($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId){
        $this->calculateScores($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId);
    }
    
    private function renewCABeforeDelete($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId){
        $this->calculateScores($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, true);
    }
    
    private function refreshCAMark($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId){        
        $this->calculateScores($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, false);        
    }
}
