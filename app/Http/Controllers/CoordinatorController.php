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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class CoordinatorController extends Controller
{
    public function uploadCa($caType, $courseIdValue){

        $courseId = Crypt::decrypt($courseIdValue);
        $caType = Crypt::decrypt($caType);

        // return $courseId;

        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->select('courses.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName','courses.CourseDescription','basic-information.ID as basicInformationId')
            ->where('courses.ID', $courseId)
            ->first();
        
        return view('coordinator.uploadCa', compact('results', 'caType','courseId'));
    }

    public function courseCASettings($courseIdValue) {
        $courseId = Crypt::decrypt($courseIdValue);
        $allAssesmentTypes = AssessmentTypes::all();
        $courseAssessmenetTypes = CATypeMarksAllocation::where('course_id', $courseId)
            ->join('assessment_types', 'assessment_types.id', '=', 'c_a_type_marks_allocations.assessment_type_id')
            ->pluck('total_marks', 'assessment_type_id')
            ->toArray();
        $course = EduroleCourses::where('ID', $courseId)->first();
    
        $marksToDeduct = !empty($courseAssessmenetTypes) ? array_sum($courseAssessmenetTypes) : 0;
    
        return view('coordinator.courseCASettings', compact('courseAssessmenetTypes', 'allAssesmentTypes', 'course', 'marksToDeduct'));
    }
    
    

    public function updateCourseCASetings(Request $request){
        // Get the course ID from the request
        $courseId = $request->input('courseId');
    
        // Get the array of assessment types and marks allocated from the request
        $assessmentTypes = $request->input('assessmentType');
        $marksAllocated = $request->input('marks_allocated');
    
        // Loop through the assessment types
        $existingAssessmentTypeIds = CATypeMarksAllocation::where('course_id', $courseId)
            ->pluck('assessment_type_id')
            ->toArray();

        foreach ($existingAssessmentTypeIds as $existingAssessmentTypeId) {
            // If the assessment type id is not in the request, delete it
            if (!array_key_exists($existingAssessmentTypeId, $assessmentTypes)) {
                CATypeMarksAllocation::where('course_id', $courseId)
                    ->where('assessment_type_id', $existingAssessmentTypeId)
                    ->delete();
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

        return redirect()->back()->with('success', 'Course CA settings updated successfully');
    }

    public function editCaInCourse($courseAssessmenId,$courseId){
        $courseAssessmentId = Crypt::decrypt($courseAssessmenId);
        $courseId = Crypt::decrypt($courseId);
        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->select('courses.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName','courses.CourseDescription','basic-information.ID as basicInformationId')
            ->where('courses.ID', $courseId)
            ->first();
        return view('coordinator.editCaInCourse', compact('results', 'courseId','courseAssessmentId'));
    }

    public function viewAllCaInCourse($statusId, $courseIdValue){
        $courseId = Crypt::decrypt($courseIdValue);
        $statusId = Crypt::decrypt($statusId);

        $courseDetails = EduroleCourses::where('ID', $courseId)->first();

        $results = CourseAssessment::where('course_id', $courseId)
            ->where('ca_type', $statusId)
            // ->join('course_assessment_scores', 'course_assessments.id', '=', 'course_assessment_scores.course_assessment_id')
            ->orderBy('course_assessments.course_assessments_id', 'asc')
            ->get();
        // return $results;
        $assessmentType = $this->setAssesmentType($statusId);

        return view('coordinator.viewAllCaInCourse', compact('results', 'statusId', 'courseId','courseDetails','assessmentType'));
    }

    private function setAssesmentType($statusId){
        if($statusId == 1){
            return 'Assignment';
        }else if($statusId == 2){
            return 'Test';
        }else if($statusId == 3){
            return 'Mock Exam';
        }else if($statusId == 4){
            return 'Practical';
        }
    }

    public function viewSpecificCaInCourse($statusId, $courseIdValue){
        $courseId = Crypt::decrypt($courseIdValue);
        $statusId = Crypt::decrypt($statusId);
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
    
        $assessmentType = $this->setAssesmentType($statusId);
        return view('coordinator.viewSpecificCaInCourse', compact('results', 'courseId','assessmentType','courseDetails','statusId'));
    }

    public function viewTotalCaInCourse($statusId, $courseIdValue){
        $courseId = Crypt::decrypt($courseIdValue);
        $caType = Crypt::decrypt($statusId);
        $courseDetails = EduroleCourses::where('ID', $courseId)->first();
        // return $courseDetails;
        if($caType != 4){            
            $results = StudentsContinousAssessment::where('students_continous_assessments.course_id', $courseId)
                ->whereIn('ca_type', [1,2,3]) 
                ->select('students_continous_assessments.student_id', DB::raw('SUM(students_continous_assessments.sca_score) as total_marks'))
                ->groupBy('students_continous_assessments.student_id')
                ->get();
            
        }else{
            $results = StudentsContinousAssessment::where('students_continous_assessments.course_id', $courseId)
                ->where('ca_type', 4) 
                ->select('students_continous_assessments.student_id', DB::raw('SUM(students_continous_assessments.sca_score) as total_marks'))
                ->groupBy('students_continous_assessments.student_id')
                ->get();
        }

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

    public function importCAFromExcelSheet(Request $request){
        set_time_limit(1200000);
        // Validate the form data
        $request->validate([
            'excelFile' => 'required|mimes:xls,xlsx,csv',
            'academicYear' => 'required',
            'ca_type' => 'required',
            'course_id' => 'required',
            'course_code' => 'required',   
            'basicInformationId' => 'required',  
        ]);

        $newAssessment =CourseAssessment::Create([
            'course_id' => $request->course_id,
            'ca_type' => $request->ca_type,
            'academic_year' => $request->academicYear,
            'basic_information_id' => $request->basicInformationId,
        ]);

        if ($request->hasFile('excelFile')) {
            $file = $request->file('excelFile');

            // Initialize the Box/Spout reader
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->open($file->getPathname());

            $isHeaderRow = false;
            $data = [];
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    // Skip the header row
                    if ($isHeaderRow) {
                        $isHeaderRow = false;
                        continue;
                    }
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
                        'course_assessment_id' => $newAssessment->course_assessments_id,
                        'student_id' => trim($entry['student_number']),                        
                        'course_code' => $request->course_code,
                    ],[                        
                        'cas_score' => $entry['mark'],                      
                    ]);
                    $this->calculateAndSubmitCA($request->course_id, $request->academicYear, $request->ca_type, trim($entry['student_number']),$newAssessment->course_assessments_id);
                }
            // }catch(\Exception $e){
            //     return back()->with('error', 'An error occurred while importing the data. Please try again.');
            // }

        }
        $statusIdToRoute = encrypt($request->ca_type);
        $courseIdToRoute = encrypt($newAssessment->course_assessments_id);

        // return redirect()->back()->with('success', 'Data imported successfully');
        return redirect()->route('coordinator.viewSpecificCaInCourse', ['statusId' => $statusIdToRoute, 'courseIdValue' => $courseIdToRoute])->with('success', 'Data imported successfully');
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
        

        $newAssessment = CourseAssessment::where('course_assessments_id', $request->course_assessment_id)
            ->update([
                'academic_year' => $request->academicYear,
                'basic_information_id' => $request->basicInformationId,
            ]);
        $getCaType = CourseAssessment::where('course_assessments_id', $request->course_assessment_id)->first();
        $caType = $getCaType->ca_type;

        // return $caType;

        if ($request->hasFile('excelFile')) {
            $file = $request->file('excelFile');

            // Initialize the Box/Spout reader
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->open($file->getPathname());

            $isHeaderRow = false;
            $data = [];
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    // Skip the header row
                    if ($isHeaderRow) {
                        $isHeaderRow = false;
                        continue;
                    }
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
        // return redirect()->route('coordinator.uploadCa', ['courseIdValue' => $request->course_id, 'statusId' => $request->status])->with('success', 'Data imported successfully');
    }

    private function refreshCAMark($courseId, $academicYear, $caType, $studentNumber){
        $caScores = CourseAssessmentScores::where('course_assessments.course_id', $courseId)
            ->where('course_assessments.academic_year', $academicYear)
            ->where('course_assessments.ca_type', $caType)
            ->where('course_assessment_scores.student_id', $studentNumber)
            ->select('course_assessment_scores.cas_score as mark')
            ->join('course_assessments', 'course_assessments.course_assessments_id', '=', 'course_assessment_scores.course_assessment_id')
            ->get();
    
        $total = 0;
        $count = 0;
        $maxScore = 0;
        if($caType == 1) {
            $maxScore = 10;
        } else if($caType == 2 || $caType == 3) {
            $maxScore = 15;
        } else if($caType == 4) {
            $maxScore = 100;
        }
    
        foreach ($caScores as $caScore){
            // Adjust the mark to be out of the maxScore for the status
            $adjustedMark = ($caScore->mark / 100) * $maxScore;
            $total += $adjustedMark;
            $count += 1;
        }
        if($count > 0){
            $average = $total / $count;
            // Save or update the average in the StudentsContiousAssessment table
            StudentsContinousAssessment::where([
                'course_id' => $courseId, // 'course_id' => $courseId, 'academic_year' => $academicYear, 'ca_type' => $caType, 'student_id' => $studentNumber
                'student_id' => $studentNumber, 
                'academic_year' => $academicYear, 
                'ca_type' => $caType
            ])->update(['sca_score' => $average]);
        }
        
    }
    

    private function calculateAndSubmitCA($courseId, $academicYear, $caType, $studentNumber,$courseAssessmentId){
        $caScores = CourseAssessmentScores::where('course_assessments.course_id', $courseId)
            ->where('course_assessments.academic_year', $academicYear)
            ->where('course_assessments.ca_type', $caType)
            ->where('course_assessment_scores.student_id', $studentNumber)
            ->select('course_assessment_scores.cas_score as mark')
            ->join('course_assessments', 'course_assessments.course_assessments_id', '=', 'course_assessment_scores.course_assessment_id')
            ->get();
    
        $total = 0;
        $count = count($caScores);
        $maxScore = 0;
        if($caType == 1) {
            $maxScore = 10;
        } else if($caType == 2 || $caType == 3) {
            $maxScore = 15;
        } else if($caType == 4) {
            $maxScore = 100;
        }
    
        foreach ($caScores as $caScore){
            $total += $caScore->mark;
        }
    
        $average = $total / $count;
    
        // Adjust the average to be out of the maxScore for the status
        $adjustedAverage = ($average / 100) * $maxScore;
    
        // Save or update the average in the StudentsContiousAssessment table
        $studentCA = StudentsContinousAssessment::firstOrNew(['student_id' => $studentNumber, 'course_id' => $courseId, 'academic_year' => $academicYear, 'ca_type' => $caType]);
        $studentCA->course_assessment_id = $courseAssessmentId;
        $studentCA->sca_score = $adjustedAverage;
        $studentCA->save();
    }

    private function renewCABeforeDelete($courseId, $academicYear, $caType, $studentNumber,$courseAssessmentId){
        $caScores = CourseAssessmentScores::where('course_assessments.course_id', $courseId)
            ->where('course_assessments.academic_year', $academicYear)
            ->where('course_assessments.ca_type', $caType)
            ->where('course_assessment_scores.student_id', $studentNumber)
            ->where('course_assessment_scores.course_assessment_id', '!=', $courseAssessmentId)
            ->select('course_assessment_scores.cas_score as mark')
            ->join('course_assessments', 'course_assessments.course_assessments_id', '=', 'course_assessment_scores.course_assessment_id')
            ->get();
    
        $total = 0;
        $count = count($caScores);
        $maxScore = 0;
        if($caType == 1) {
            $maxScore = 10;
        } else if($caType == 2 || $caType == 3) {
            $maxScore = 15;
        } else if($caType == 4) {
            $maxScore = 100;
        }
        if($count > 0){
    
            foreach ($caScores as $caScore){
                $total += $caScore->mark;
            }
        
            $average = $total / $count;
        
            // Adjust the average to be out of the maxScore for the status
            $adjustedAverage = ($average / 100) * $maxScore;
            $studentCA = StudentsContinousAssessment::firstOrNew(['student_id' => $studentNumber, 'course_id' => $courseId, 'academic_year' => $academicYear, 'ca_type' => $caType]);
            $studentCA->course_assessment_id = $courseAssessmentId;
            $studentCA->sca_score = $adjustedAverage;
            $studentCA->save();
        }else{
            StudentsContinousAssessment::where([
                'course_id' => $courseId, // 'course_id' => $courseId, 'academic_year' => $academicYear, 'ca_type' => $caType, 'student_id' => $studentNumber
                'student_id' => $studentNumber, 
                'academic_year' => $academicYear, 
                'ca_type' => $caType
            ])->delete();
        }    
        // Save or update the average in the StudentsContiousAssessment table
        
    }
}
