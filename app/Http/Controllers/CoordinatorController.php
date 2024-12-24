<?php

namespace App\Http\Controllers;

use App\Models\AssessmentTypes;
use App\Models\CaAndExamUpload;
use App\Models\CATypeMarksAllocation;
use App\Models\CourseAssessment;
use App\Models\CourseAssessmentScores;
use App\Models\CourseComponent;
use App\Models\CourseComponentAllocation;
use App\Models\EduroleBasicInformation;
use App\Models\EduroleCourseElective;
use App\Models\EduroleCourses;
use App\Models\EduroleStudy;
use App\Models\FinalExamination;
use App\Models\FinalExaminationResults;
use App\Models\StudentsContinousAssessment;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\QueryException;
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
        // return  $basicInformationId;
        $basicInformationId = Crypt::decrypt($basicInformationId);
        // return  $basicInformationId;
        $hasComponents = $request->hasComponents;
        
        $componentId = $request->input('componentId');
        
        $studyId = $request->studyId;
        $getAssessmentType = AssessmentTypes::where('id', $caType)->first();
        $assessmentType = $getAssessmentType->assesment_type_name;
        // return $assessmentType;
        // return $studyId;
        // return $courseId . '  ' . $basicInformationId . '  ' . $delivery . '  ' . $caType;

        // return $courseId;

        $results = $this->getCoursesFromEdurole()            
            ->where('courses.ID', $courseId)
            ->where('study.Delivery', $delivery)
            ->where('study.ProgrammesAvailable', $basicInformationId)
            ->first();
        // return $results;
        
            return view('coordinator.uploadCa', compact('assessmentType','studyId','results', 'caType', 'courseId', 'basicInformationId', 'delivery','hasComponents','componentId'))
                ->with('info', 'Kindly note that you are uploading under ' . $delivery . ' education');

    }

    public function uploadCaAndFinalExamAtOnce(Request $request, $courseIdValue,$basicInformationId){
        $delivery = $request->delivery; 
        $courseId = Crypt::decrypt($courseIdValue);
        $typeOfExam = $request->typeOfExam;

        // $caType = Crypt::decrypt($caType);
        // return  $basicInformationId;
        $basicInformationId = Crypt::decrypt($basicInformationId);
        // return  $basicInformationId;
        $hasComponents = $request->hasComponents;
        
        $componentId = $request->input('componentId');
        
        $studyId = $request->studyId;
        // $getAssessmentType = AssessmentTypes::where('id', $caType)->first();
        // $assessmentType = $getAssessmentType->assesment_type_name;
        // return $assessmentType;
        // return $studyId;
        // return $courseId . '  ' . $basicInformationId . '  ' . $delivery . '  ' . $caType;

        // return $courseId;

        $results = $this->getCoursesFromEdurole()            
            ->where('courses.ID', $courseId)
            ->where('study.Delivery', $delivery)
            ->where('study.ProgrammesAvailable', $basicInformationId)
            ->first();
        // return $results;
        
            return view('coordinator.uploadCaAndFinalExamAtOnce', compact('studyId','results',  'courseId', 'basicInformationId','typeOfExam' ,'delivery','hasComponents','componentId'))
                ->with('info', 'Kindly note that you are uploading under ' . $delivery . ' education');

    }



    public function uploadCaFinalExam(Request $request, $courseIdValue,$basicInformationId){
        $delivery = $request->delivery; 
        $courseId = Crypt::decrypt($courseIdValue);
        $typeOfExam = $request->typeOfExam;

        // $caType = Crypt::decrypt($caType);
        // return  $basicInformationId;
        $basicInformationId = Crypt::decrypt($basicInformationId);
        // return  $basicInformationId;
        $hasComponents = $request->hasComponents;
        
        $componentId = $request->input('componentId');
        
        $studyId = $request->studyId;
        // $getAssessmentType = AssessmentTypes::where('id', $caType)->first();
        // $assessmentType = $getAssessmentType->assesment_type_name;
        // return $assessmentType;
        // return $studyId;
        // return $courseId . '  ' . $basicInformationId . '  ' . $delivery . '  ' . $caType;

        // return $courseId;

        $results = $this->getCoursesFromEdurole()            
            ->where('courses.ID', $courseId)
            ->where('study.Delivery', $delivery)
            ->where('study.ProgrammesAvailable', $basicInformationId)
            ->first();
        // return $results;
        
            return view('coordinator.uploadCaFinalExam', compact('studyId','results',  'courseId', 'basicInformationId', 'delivery','hasComponents','componentId'))
                ->with('info', 'Kindly note that you are uploading under ' . $delivery . ' education');

    }

    public function showCaWithin(Request $request,$courseId){
        $courseId = Crypt::decrypt($courseId);
        $studyId = $request->studyId;
        $delivery = $request->delivery;

        // return $delivery;

        if($request->componentId){
            $componentId = $request->componentId;
        }else{
            $componentId = null;
        }
        $assessmentDetails = CourseAssessment::select(
            'course_assessments.basic_information_id',
            'assessment_types.assesment_type_name',
            'assessment_types.id',
            'course_assessments.delivery_mode',
            DB::raw('count(course_assessments.course_assessments_id) as total')
        )
        ->where('course_assessments.course_id', $courseId)
        ->where('course_assessments.study_id', $studyId)
        ->where('course_assessments.delivery_mode', $delivery)
        ->where('course_assessments.component_id', $componentId)
        ->join('assessment_types', 'assessment_types.id', '=', 'course_assessments.ca_type')
        ->groupBy('assessment_types.id','course_assessments.basic_information_id', 'assessment_types.assesment_type_name','course_assessments.delivery_mode')
        ->get();

        // return $assessmentDetails;

        $courseInfo = EduroleCourses::where('ID', $courseId)->first();
        
        // return $assessmentDetails;
    

        // return $assessmentDetails;
        return view('admin.showCaInCourse', compact('courseInfo','assessmentDetails','courseId','studyId'));

    }

    public function viewCourseWithComponents(Request $request, $courseIdValue, $basicInformationId, $delivery) {
        $courseId = Crypt::decrypt($courseIdValue);
        $basicInformationId = Crypt::decrypt($basicInformationId);        
        $delivery = Crypt::decrypt($delivery);

         // return $basicInformationId;

        $studyId = $request->studyId;
        $isSettings = $request->isSettings;
        $academicYear = 2024;
        $courseDetails = EduroleCourses::where('ID', $courseId)->first();
        $user = auth()->user();
    
        $courseComponentAllocated = CourseComponentAllocation::where('course_id', $courseId)
            ->where('delivery_mode', $delivery)
            ->where('study_id', $studyId)
            ->where('academic_year', $academicYear)
            ->pluck('course_component_id')
            ->toArray();

        // return $courseComponentAllocated;
    
        $courseComponents = CourseComponent::all();
        $getCoure = EduroleCourses::where('ID', $courseId)->first();
        $courseCode = $getCoure->Name;

        $courseIdEncrypt = encrypt($courseId);
        $basicInformationIdEncrypt = encrypt($basicInformationId);
        $deliveryEncrypt = encrypt($delivery);
        $studyIdEncrypt = encrypt($studyId);

        // return $basicInformationId;

        if(!$courseComponentAllocated || $isSettings == 1){
            return view('coordinator.caComponents.setCourseComponents', compact('academicYear','courseComponentAllocated','courseDetails','courseId', 'basicInformationId', 'delivery', 'studyId', 'courseComponents'));
        }else{
            if ($user->hasRole('Coordinator')) {
                return redirect()->route('pages.uploadCourseWithComponents', ['courseId' => $courseIdEncrypt, 'basicInformationId' => $basicInformationIdEncrypt, 'delivery' => $deliveryEncrypt, 'studyId' => $studyIdEncrypt])
                    ->with('success', $courseCode . 'Select Component In which you want to upload CA');
            } else {
                return redirect()->route('admin.viewCoordinatorsCoursesWithComponents', ['courseId' => $courseIdEncrypt, 'basicInformationId' => $basicInformationIdEncrypt, 'delivery' => $deliveryEncrypt, 'studyId' => $studyIdEncrypt] )
                    ->with('success', $courseCode . 'Select Component In which you want to upload CA');
            }
        }
        
    }
    

    // public function viewCourseWithComponents(Request $request,$courseIdValue, $basicInformationId, $delivery){
    //     $courseId = Crypt::decrypt($courseIdValue);
    //     $basicInformationId = Crypt::decrypt($basicInformationId);
    //     $delivery = Crypt::decrypt($delivery);
    //     $studyId = $request->studyId;
    //     $academicYear = 2024;
    //     $courseDetails = EduroleCourses::where('ID', $courseId)->first();

    //     $courseComponentAllocated = CourseComponentAllocation::where('course_id', $courseId)
    //         ->where('delivery_mode', $delivery)
    //         ->where('study_id', $studyId)
    //         ->where('academic_year', $academicYear)
    //         ->get();

    //     if ($courseComponentAllocated->isEmpty()) {

    //         $courseComponents = CourseComponent::all();
    //         $courseComponentAllocated = CourseComponentAllocation::where('course_id', $courseId)
    //         ->where('delivery_mode', $delivery)
    //         ->where('study_id', $studyId)
    //         ->where('academic_year', $academicYear)
    //         ->pluck('course_component_id')
    //         ->toArray();
            
    //         return view('coordinator.caComponents.setCourseComponents', compact('academicYear','courseComponentAllocated','courseDetails','courseId', 'basicInformationId', 'delivery', 'studyId', 'courseComponents'));
    //     }else{
    //         $courseComponents = CourseComponent::all();
    //         return view('coordinator.caComponents.setCourseComponents', compact('academicYear','courseComponentAllocated','courseDetails','courseId', 'basicInformationId', 'delivery', 'studyId', 'courseComponents'));
    //     }       
    // }

    public function courseCASettings(Request $request,$courseIdValue, $basicInformationId, $delivery){ 
        set_time_limit(200000000000);
        $courseId = Crypt::decrypt($courseIdValue);
        $delivery = Crypt::decrypt($delivery);
        $basicInformationId = Crypt::decrypt($basicInformationId);
        // return $basicInformationId;
        $studyId = $request->studyId;   
        $hasComponents = $request->hasComponents;
        $componentId = $request->input('componentId');
        // return $componentId;
        // return $componentId . ' ' . $hasComponents . ' ' . $courseId . ' ' . $delivery . ' ' . $studyId;
        // return $hasComponents;
        $allAssesmentTypes = AssessmentTypes::all();
        $courseAssessmenetTypes = CATypeMarksAllocation::where('course_id', $courseId)
            ->where('delivery_mode', $delivery)
            ->where('study_id', $studyId)
            ->where('component_id', $componentId)
            ->join('assessment_types', 'assessment_types.id', '=', 'c_a_type_marks_allocations.assessment_type_id')
            ->pluck('total_marks', 'assessment_type_id')
            ->toArray();
        // return $courseAssessmenetTypes;
        $course = EduroleCourses::where('ID', $courseId)->first();
    
        $marksToDeduct = !empty($courseAssessmenetTypes) ? array_sum($courseAssessmenetTypes) : 0;
    
        return view('coordinator.courseCASettings', compact('componentId','courseId','delivery','studyId','delivery','courseAssessmenetTypes', 'allAssesmentTypes', 'course', 'marksToDeduct','basicInformationId','hasComponents'));
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
        
        $getCourdinatoresCourses = EduroleStudy::where('ProgrammesAvailable', $coordinatorId)->pluck('ID')->toArray();

        // return $getCourdinatoresCourses;

        $results = $this->getCoursesFromEdurole()
            
            ->whereIn('study.ID', $getCourdinatoresCourses)
            ->get();
        // return $results;
        $filteredResults = $results->filter(function ($item) use ($coursesWithCA) {
            foreach ($coursesWithCA as $course) {
                if ($item->CourseName == $course['course_code'] && $item->Delivery == $course['delivery_mode'] && $item->ProgrammesAvailable != 1 && $item->StudyID == $course['study_id']) {
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

        set_time_limit(200000000000);
        // return "here";
        DB::beginTransaction();

        try {
            // Get the course ID and other required parameters from the request
            $courseId = $request->input('courseId');
            $basicInformationId = $request->input('basicInformationId');
            $delivery = $request->input('delivery');
            $studyId = $request->input('studyId');
            $componentId = $request->input('componentId');

            // Get the array of assessment types and marks allocated from the request
            $assessmentTypes = $request->input('assessmentType');
            $marksAllocated = $request->input('marks_allocated');
            // return $assessmentTypes;
            // return $marksAllocated;
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
                        ->where('component_id', $componentId)    
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
                            'study_id' => $studyId,
                            'component_id' => $componentId
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
                ->where('component_id', $componentId)
                ->join('assessment_types', 'assessment_types.id', '=', 'c_a_type_marks_allocations.assessment_type_id')
                ->get();

            $academicYear = 2024;
            $getCoure = EduroleCourses::where('ID', $courseId)->first();
            $courseCode = $getCoure->Name;

            foreach ($courseAssessmenetTypes as $courseAssessmentType) {
                $studentsInAssessmentType = CourseAssessmentScores::where('course_code', $courseCode)
                    ->where('delivery_mode', $delivery)
                    ->where('study_id', $studyId)
                    ->where('component_id', $componentId)
                    ->get();

                foreach ($studentsInAssessmentType as $studentNumber) {
                    $this->refreshCAMark(
                        $courseId,
                        $academicYear,
                        $courseAssessmentType->assessment_type_id,
                        $studentNumber->student_id,
                        $studentNumber->course_assessment_id,
                        $delivery,
                        $studentNumber->study_id,
                        $componentId
                    );
                }
            }

            DB::commit();

            $courseIdEncrypt = encrypt($courseId);
            $basicInformationEncrypt = encrypt($basicInformationId);
            $deliveryEncrypt = encrypt($delivery);
            $studyIdEncrypt = encrypt($studyId);

            // Redirect based on user role
            if ($user->hasRole('Coordinator')) {
                if($componentId){
                    return redirect()->route('pages.uploadCourseWithComponents', ['courseId' => $courseIdEncrypt, 'basicInformationId' => $basicInformationEncrypt, 'delivery' => $deliveryEncrypt, 'studyId' => $studyIdEncrypt])
                        ->with('success', $courseCode . ' CA settings updated successfully');
                }else{
                    return redirect()->route('pages.upload')->with('success', $courseCode . ' CA settings updated successfully');
                }
            } else {
                if($componentId){
                    return redirect()->route('admin.viewCoordinatorsCoursesWithComponents', ['courseId' => $courseIdEncrypt, 'basicInformationId' => $basicInformationEncrypt, 'delivery' => $deliveryEncrypt, 'studyId' => $studyIdEncrypt])
                        ->with('success', $courseCode . ' CA settings updated successfully');
                }else{
                    return redirect()->route('admin.viewCoordinatorsCourses', $basicInformationEncrypt)->with('success', $courseCode . ' CA settings updated successfully');
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while updating the course CA settings. Please try again. Error: ' . $e->getMessage());
        }
    }

    public function updateCourseWithComponents(Request $request)
    {
        DB::beginTransaction();

        try {
            // Get the course ID and other required parameters from the request
            $courseId = $request->input('courseId');
            $basicInformationId = $request->input('basicInformationId');
            $delivery = $request->input('delivery');
            $studyId = $request->input('studyId');
            $academicYear = $request->input('academicYear');

            // Get the array of assessment types and marks allocated from the request
            $courseComponents = $request->input('courseComponent');
            // return $courseComponents;
            // $marksAllocated = $request->input('marks_allocated');
            $user = auth()->user();
            $userBasicInformationId = $user->basic_information_id;

            // Retrieve existing assessment type IDs for the course
            // $existingAssessmentTypeIds = CATypeMarksAllocation::where('course_id', $courseId)
            //     ->where('delivery_mode', $delivery)
            //     ->where('study_id', $studyId)
            //     ->pluck('assessment_type_id')
            //     ->toArray();
            $existingCourseComponentAllocatedIds = CourseComponentAllocation::where('course_id', $courseId)
                ->where('delivery_mode', $delivery)
                ->where('study_id', $studyId)
                ->where('academic_year', $academicYear)
                ->pluck('course_component_id')
                ->toArray();

            // Remove assessment types that are no longer checked
            foreach ($existingCourseComponentAllocatedIds as $existingCourseComponentAllocatedId) {
                if (!array_key_exists($existingCourseComponentAllocatedId, $courseComponents ?? [])) {
                    CourseComponentAllocation::where('course_id', $courseId)
                        ->where('delivery_mode', $delivery)
                        ->where('study_id', $studyId)
                        ->where('academic_year', $academicYear)
                        ->where('course_component_id', $existingCourseComponentAllocatedId)
                        ->delete();
                }
            }

            // Update or create the assessment type allocations
            foreach ($courseComponents as $courseComponentId => $isChecked) {
                if ($isChecked) {
                    // $marks = $marksAllocated[$courseComponentId];
                        CourseComponentAllocation::updateOrCreate(
                        [
                            'course_id' => $courseId,
                            'course_component_id' => $courseComponentId,
                            'delivery_mode' => $delivery,
                            'study_id' => $studyId,
                            'academic_year' => $academicYear
                        ],
                        [
                            'user_id' => auth()->user()->id,
                        ]
                    );
                }
            }

            // Update related CA marks for students
            // $courseAssessmenetTypes = CATypeMarksAllocation::where('course_id', $courseId)
            //     ->where('study_id', $studyId)
            //     ->where('delivery_mode', $delivery)
            //     ->join('assessment_types', 'assessment_types.id', '=', 'c_a_type_marks_allocations.assessment_type_id')
            //     ->get();

            // $academicYear = 2024;
            // $getCoure = EduroleCourses::where('ID', $courseId)->first();
            // $courseCode = $getCoure->Name;

            // foreach ($courseAssessmenetTypes as $courseAssessmentType) {
            //     $studentsInAssessmentType = CourseAssessmentScores::where('course_code', $courseCode)
            //         ->where('delivery_mode', $delivery)
            //         ->where('study_id', $studyId)
            //         ->get();

            //     foreach ($studentsInAssessmentType as $studentNumber) {
            //         $this->refreshCAMark(
            //             $courseId,
            //             $academicYear,
            //             $courseAssessmentType->assessment_type_id,
            //             $studentNumber->student_id,
            //             $studentNumber->course_assessment_id,
            //             $delivery,
            //             $studentNumber->study_id
            //         );
            //     }
            // }
            $getCoure = EduroleCourses::where('ID', $courseId)->first();
            $courseCode = $getCoure->Name;

            DB::commit();
            $basicInformationEncrypt = encrypt($basicInformationId);

            // Redirect based on user role
            if ($user->hasRole('Coordinator')) {
                return redirect()->route('pages.upload')->with('success', $courseCode . ' Component settings updated successfully');
            } else {
                return redirect()->route('admin.viewCoordinatorsCourses', $basicInformationEncrypt)->with('success', $courseCode . ' Component settings updated successfully');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while updating the course CA settings. Please try again. Error: ' . $e->getMessage());
        }
    }


    public function editCaInCourse(Request $request, $courseAssessmenId,$courseId, $basicInformationId){
        $courseAssessmentId = Crypt::decrypt($courseAssessmenId);
        $courseId = Crypt::decrypt($courseId);
        $basicInformationId = Crypt::decrypt($basicInformationId);
        $componentId = $request->componentId;
        $studyId = $request->study_id;
        $delivery = $request->delivery;

        // return 'Study ID: ' . $studyId . ' Delivery: ' . $delivery ;
        $hasComponents = $request->hasComponents;
        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->select('courses.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name','study.ID as StudyID', 'courses.Name as CourseName','courses.CourseDescription','basic-information.ID as basicInformationId')
            ->where('courses.ID', $courseId)
            ->first();
        $courseAssessment = CourseAssessment::where('course_assessments_id', $courseAssessmentId)
            ->join('assessment_types','assessment_types.id','=','course_assessments.ca_type')
            ->first();

        // return $courseAssessment;\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
        // $delivery;
        // $delivery = $courseAssessment->delivery_mode;
        return view('coordinator.editCaInCourse', compact('courseAssessment','hasComponents','componentId','delivery','results', 'courseId','courseAssessmentId','basicInformationId','studyId'));
    }

    public function editAStudentsCaInCourse(Request $request, $courseAssessmenId,$courseId, $basicInformationId){
        $courseAssessmentId = Crypt::decrypt($courseAssessmenId);
        $courseId = Crypt::decrypt($courseId);
        $basicInformationId = Crypt::decrypt($basicInformationId);
        $studentId = $request->studentId;
        $componentId = $request->componentId;

        return $courseAssessmentId . ' ' . $courseId . ' ' . $basicInformationId . ' ' . $studentId . ' ' . $componentId;
        $componentId = $request->componentId;
        $hasComponents = $request->hasComponents;
        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->select('courses.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name','study.ID as StudyID', 'courses.Name as CourseName','courses.CourseDescription','basic-information.ID as basicInformationId')
            ->where('courses.ID', $courseId)
            ->first();
        $courseAssessment = CourseAssessment::where('course_assessments_id', $courseAssessmentId)
            ->join('assessment_types','assessment_types.id','=','course_assessments.ca_type')
            ->first();

        // return $courseAssessment;
        // $delivery;
        $delivery = $courseAssessment->delivery_mode;
        return view('coordinator.editCaInCourse', compact('courseAssessment','hasComponents','componentId','delivery','results', 'courseId','courseAssessmentId','basicInformationId'));
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
        $componentId = $request->componentId;
        $hasComponents = $request->hasComponents;

        // return $hasComponents;
        $courseDetails = EduroleCourses::where('ID', $courseId)->first();

        $results = CourseAssessment::where('course_id', $courseId)
            ->where('ca_type', $statusId)
            ->where('delivery_mode', $delivery)
            ->where('study_id', $studyId)
            ->where('component_id', $componentId)
            // ->join('course_assessment_scores', 'course_assessments.id', '=', 'course_assessment_scores.course_assessment_id')
            ->orderBy('course_assessments.course_assessments_id', 'asc')
            ->get();
        // return $results;
        $assessmentType = $this->setAssesmentType($statusId);

        return view('coordinator.viewAllCaInCourse', compact('studyId','componentId','hasComponents','delivery','results', 'statusId', 'courseId','courseDetails','assessmentType','basicInformationId'));
    }

    

    public function viewExamCaInCourseFinalExamAndCa(Request $request, $courseIdValue, $basicInformationId, $delivery){
        $courseId = Crypt::decrypt($courseIdValue);
        $basicInformationId = Crypt::decrypt($basicInformationId);
        $delivery = Crypt::decrypt($delivery);
        $academicYear = 2024;
        
        $studyId = $request->studyId;       

        // return $hasComponents;
        $courseDetails = EduroleCourses::where('ID', $courseId)->first();

        // return 'Course ID: ' . $courseId . ' Basic Information ID: ' . $basicInformationId . ' Delivery: ' . $delivery . ' Study ID: ' . $studyId;

        $results = CaAndExamUpload::where('ca_and_exam_uploads.course_id', $courseId)
            ->where('ca_and_exam_uploads.delivery_mode', $delivery)
            ->where('ca_and_exam_uploads.study_id', $studyId)
            ->where('ca_and_exam_uploads.academic_year', $academicYear)
            ->select('ca_and_exam_uploads.student_id', 'ca_and_exam_uploads.exam as FinalExam', 'ca_and_exam_uploads.type_of_exam', 'ca_and_exam_uploads.ca as Ca','ca_and_exam_uploads.grade', 'ca_and_exam_uploads.course_code', 'ca_and_exam_uploads.academic_year', 'ca_and_exam_uploads.course_id','ca_and_exam_uploads.delivery_mode','ca_and_exam_uploads.study_id','ca_and_exam_uploads.basic_information_id','ca_and_exam_uploads.updated_at','ca_and_exam_uploads.created_at','ca_and_exam_uploads.ca_and_exam_uploads_id')
            
            ->get();

    // return $results;

        $resultsArrayStudentNumbers = $results->pluck('student_id')->toArray();

        $resultsFromBasicInformation= EduroleBasicInformation::join('student-study-link', 'student-study-link.StudentID', '=', 'basic-information.ID')
            ->join('study', 'study.ID', '=', 'student-study-link.StudyID')            
            ->join('schools', 'schools.ID', '=', 'study.ParentID')
            ->select('basic-information.ID', 'basic-information.FirstName','basic-information.StudyType', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.Name as Programme', 'schools.Name as School')
            ->whereIn('basic-information.ID', $resultsArrayStudentNumbers)
            ->get();

            $results = $results->map(function ($result) use ($resultsFromBasicInformation) {
                $result->basic_information = $resultsFromBasicInformation->firstWhere('ID', $result->student_id);
                return $result;
            });
    
        // return $results; 
        if($results->isEmpty()){
            return redirect()->back()->with('error', 'No results found for the selected course');
        }

        return view('coordinator.viewAllExamInCourseFinalExamAndCa', compact('studyId','delivery','results', 'courseId','courseDetails','basicInformationId'));
    }
    public function viewAllExamInCourse(Request $request, $courseIdValue, $basicInformationId, $delivery){
        $courseId = Crypt::decrypt($courseIdValue);
        $basicInformationId = Crypt::decrypt($basicInformationId);
        $delivery = Crypt::decrypt($delivery);
        $academicYear = 2024;
        
        $studyId = $request->studyId;       

        // return $hasComponents;
        $courseDetails = EduroleCourses::where('ID', $courseId)->first();

        // return 'Course ID: ' . $courseId . ' Basic Information ID: ' . $basicInformationId . ' Delivery: ' . $delivery . ' Study ID: ' . $studyId;

        $results = FinalExaminationResults::where('final_examination_results.course_id', $courseId)
            ->where('final_examination_results.delivery_mode', $delivery)
            ->where('final_examination_results.study_id', $studyId)
            ->where('final_examination_results.academic_year', $academicYear)
            // ->join('course_assessment_scores', 'course_assessments.id', '=', 'course_assessment_scores.course_assessment_id')
            ->join('final_examinations', 'final_examinations.final_examinations_id', '=', 'final_examination_results.final_examinations_id')
            ->select('final_examination_results.student_id', 'final_examination_results.cas_score as TotalMarks', 'final_examination_results.final_examination_results_id', 'final_examinations.course_code', 'final_examinations.cas_score as PercentageMark','final_examinations.academic_year', 'final_examinations.course_id','final_examinations.delivery_mode','final_examinations.study_id','final_examinations.basic_information_id','final_examinations.updated_at','final_examinations.created_at','final_examination_results.final_examination_results_id')
            ->orderBy('final_examination_results.final_examination_results_id', 'asc')
            ->get();

        $resultsArrayStudentNumbers = $results->pluck('student_id')->toArray();

        $resultsFromBasicInformation= EduroleBasicInformation::join('student-study-link', 'student-study-link.StudentID', '=', 'basic-information.ID')
            ->join('study', 'study.ID', '=', 'student-study-link.StudyID')            
            ->join('schools', 'schools.ID', '=', 'study.ParentID')
            ->select('basic-information.ID', 'basic-information.FirstName','basic-information.StudyType', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.Name as Programme', 'schools.Name as School')
            ->whereIn('basic-information.ID', $resultsArrayStudentNumbers)
            ->get();

            $results = $results->map(function ($result) use ($resultsFromBasicInformation) {
                $result->basic_information = $resultsFromBasicInformation->firstWhere('ID', $result->student_id);
                return $result;
            });
    
        // return $results; 
        if($results->isEmpty()){
            return redirect()->back()->with('error', 'No results found for the selected course');
        }

        return view('coordinator.viewAllExamInCourse', compact('studyId','delivery','results', 'courseId','courseDetails','basicInformationId'));
    }

    private function setAssesmentType($statusId){
        $getAssesmntType = AssessmentTypes::where('id', $statusId)->first();
        return $getAssesmntType->assesment_type_name;
    }

    public function viewSpecificCaInCourse(Request $request,$statusId, $courseIdValue, $assessmentNumber){
        $courseIdAssessmentId = Crypt::decrypt($courseIdValue);
        $statusId = Crypt::decrypt($statusId);
        try{
            $caTypeFromAssessment = Crypt::decrypt($request->caType);
        }catch(Exception $e){
            $caTypeFromAssessment = $statusId;
        }

        // return $caTypeFromAssessment;
        $assessmentNumber = Crypt::decrypt($assessmentNumber);
        $componentId = $request->componentId;

        // return 'Ca Type: ' . $caTypeFromAssessment . ' Status ID: ' . $statusId;

        // return $statusId;
        // return $courseId;
        $results = CourseAssessment::where('course_assessments.course_assessments_id', $courseIdAssessmentId)
            // ->where('ca_type', $statusId)
            ->join('course_assessment_scores', 'course_assessments.course_assessments_id', '=', 'course_assessment_scores.course_assessment_id')
            ->orderBy('course_assessments.created_at', 'asc')
            ->get();

        $courseId = $results->first()->course_id;
        
        $delivery = $results[0]->delivery_mode;
        $hasComponents =  $request->hasComponents;
            
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
        return view('coordinator.viewSpecificCaInCourse', compact('caTypeFromAssessment','componentId','hasComponents','delivery','results', 'courseId','assessmentType','courseDetails','statusId'));
    }

    public function cleanUpDuplicatesForCourse($courseId, $studyId, $deliveryMode, $componentId)
    {
        // Step 1: Find IDs to delete by keeping the one with the latest updated_at
        $idsToDelete = DB::table('students_continous_assessments as sca1')
            ->select('sca1.students_continous_assessment_id')
            ->where('sca1.course_id', $courseId)
            ->where('sca1.study_id', $studyId)
            ->where('sca1.delivery_mode', $deliveryMode)
            ->where('sca1.component_id', $componentId)
            ->whereExists(function ($query) use ($courseId, $studyId, $deliveryMode, $componentId) {
                $query->select(DB::raw(1))
                    ->from('students_continous_assessments as sca2')
                    ->whereRaw('sca1.student_id = sca2.student_id')
                    ->whereRaw('sca1.course_id = sca2.course_id')
                    ->whereRaw('sca1.academic_year = sca2.academic_year')
                    ->whereRaw('sca1.ca_type = sca2.ca_type')
                    ->whereRaw('sca1.delivery_mode = sca2.delivery_mode')
                    ->whereRaw('sca1.study_id = sca2.study_id')
                    ->whereRaw('sca1.component_id <=> sca2.component_id') // NULL-safe comparison
                    ->whereRaw('sca1.updated_at < sca2.updated_at'); // Keep the one with the latest updated_at
            })
            ->pluck('sca1.students_continous_assessment_id') // Fetch duplicate IDs into an array
            ->toArray();

        // Step 2: Delete duplicates
        if (!empty($idsToDelete)) {
            DB::table('students_continous_assessments')
                ->whereIn('students_continous_assessment_id', $idsToDelete)
                ->delete();
        }
    }




    public function viewTotalCaInCourse(Request $request ,$statusId, $courseIdValue, $basicInformationId,$delivery){
        $courseId = Crypt::decrypt($courseIdValue);
        // $caType = Crypt::decrypt($statusId);
        $basicInformationId = Crypt::decrypt($basicInformationId);
        $delivery = Crypt::decrypt($delivery);
        $componentId = $request->componentId;
        $hasComponents = $request->hasComponents;
        $courseDetails = EduroleCourses::where('ID', $courseId)->first();
        $coursesInEdurole = $this->getCoursesFromEdurole()
                ->where('courses.ID', $courseId)
                ->where('study.ProgrammesAvailable', $basicInformationId)
                ->where('study.Delivery', $delivery)
                ->first();
        // return $courseDetails;
        // if($caType != 4){   
        $this->cleanUpDuplicatesForCourse($courseId, $coursesInEdurole->StudyID, $delivery,$componentId);
        
        $results = StudentsContinousAssessment::where('students_continous_assessments.course_id', $courseId)
            ->where('students_continous_assessments.delivery_mode', $delivery)
            ->where('students_continous_assessments.study_id', $coursesInEdurole->StudyID)
            ->where('students_continous_assessments.component_id', $componentId)
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
        $arrayOfProgrammes = $this->arrayOfValidProgrammes($coursesInEdurole->StudyID);

        // return $arrayOfProgrammes;

        // ??TO DO: Add the study ID to the query below

        $resultsFromBasicInformation= EduroleBasicInformation::join('student-study-link', 'student-study-link.StudentID', '=', 'basic-information.ID')
            ->join('study', 'study.ID', '=', 'student-study-link.StudyID')            
            ->join('schools', 'schools.ID', '=', 'study.ParentID')
            ->select('basic-information.ID', 'basic-information.FirstName', 'basic-information.Surname','basic-information.StudyType', 'basic-information.PrivateEmail', 'study.Name as Programme', 'schools.Name as School', 'study.ID as StudyID')
            ->whereIn('basic-information.ID', $resultsArrayStudentNumbers)
            // ->whereIn('study.ID', $arrayOfProgrammes)
            ->get();
        // return $resultsFromBasicInformation;
        $results = $results->map(function ($result) use ($resultsFromBasicInformation) {
            $result->basic_information = $resultsFromBasicInformation->firstWhere('ID', $result->student_id);
            return $result;
        });
        // return $results;
        return view('coordinator.viewTotalCaInCourse', compact('componentId','delivery','results', 'statusId', 'courseId','courseDetails','hasComponents')); 
    }

    public function viewTotalCaInCourseAndFinalExam(Request $request ,$statusId, $courseIdValue, $basicInformationId,$delivery){
        $courseId = Crypt::decrypt($courseIdValue);
        // $caType = Crypt::decrypt($statusId);
        $basicInformationId = Crypt::decrypt($basicInformationId);
        $delivery = Crypt::decrypt($delivery);
        $componentId = $request->componentId;
        $hasComponents = $request->hasComponents;
        $courseDetails = EduroleCourses::where('ID', $courseId)->first();
        $coursesInEdurole = $this->getCoursesFromEdurole()
                ->where('courses.ID', $courseId)
                ->where('study.ProgrammesAvailable', $basicInformationId)
                ->where('study.Delivery', $delivery)
                ->first();
        // return $courseDetails;

        $academicYear = 2024;
        // if($caType != 4){            
        // $results = StudentsContinousAssessment::where('students_continous_assessments.course_id', $courseId)
        //     ->where('students_continous_assessments.delivery_mode', $delivery)
        //     ->where('students_continous_assessments.study_id', $coursesInEdurole->StudyID)
        //     ->where('students_continous_assessments.component_id', $componentId)
        //     // ->whereIn('ca_type', [1,2,3]) 
        //     ->join('course_assessments', 'course_assessments.course_assessments_id', '=', 'students_continous_assessments.course_assessment_id')
        //     ->select('students_continous_assessments.student_id', DB::raw('SUM(students_continous_assessments.sca_score) as total_marks'))
        //     ->groupBy('students_continous_assessments.student_id')
        //     ->get();

        $results = FinalExaminationResults::leftJoin('students_continous_assessments', function($join) use ($courseId, $delivery, $coursesInEdurole, $componentId) {
        $join->on('final_examination_results.student_id', '=', 'students_continous_assessments.student_id')
             ->where('students_continous_assessments.course_id', $courseId)
             ->where('students_continous_assessments.delivery_mode', $delivery)
             ->where('students_continous_assessments.study_id',$coursesInEdurole->StudyID)
             ->where('students_continous_assessments.component_id', $componentId);
    })
    ->join('final_examinations', 'final_examinations.final_examinations_id', '=', 'final_examination_results.final_examinations_id')
    ->select(
        'final_examination_results.student_id',
        'final_examination_results.cas_score as TotalMarks',
        'final_examination_results.final_examination_results_id',
        'final_examinations.course_code',
        'final_examinations.cas_score as PercentageMark',
        'final_examinations.academic_year',
        'final_examinations.course_id',
        'final_examinations.delivery_mode',
        'final_examinations.study_id',
        'final_examinations.basic_information_id',
        'final_examinations.updated_at',
        'final_examinations.created_at',
        DB::raw('SUM(students_continous_assessments.sca_score) as total_marks')
    )
    ->where('final_examination_results.course_id', $courseId)
    ->where('final_examination_results.delivery_mode', $delivery)
    ->where('final_examination_results.study_id', $coursesInEdurole->StudyID)
    ->where('final_examination_results.academic_year', $academicYear)
    ->groupBy(
        'final_examination_results.student_id',
        'final_examination_results.cas_score',
        'final_examination_results.final_examination_results_id',
        'final_examinations.course_code',
        'final_examinations.cas_score',
        'final_examinations.academic_year',
        'final_examinations.course_id',
        'final_examinations.delivery_mode',
        'final_examinations.study_id',
        'final_examinations.basic_information_id',
        'final_examinations.updated_at',
        'final_examinations.created_at'
    )
    ->orderBy('final_examination_results.final_examination_results_id', 'asc')
    ->get();

    // return $results;
            
        // }else{
        //     $results = StudentsContinousAssessment::where('students_continous_assessments.course_id', $courseId)
        //         ->where('ca_type', 4) 
        //         ->select('students_continous_assessments.student_id', DB::raw('SUM(students_continous_assessments.sca_score) as total_marks'))
        //         ->groupBy('students_continous_assessments.student_id')
        //         ->get();
        // }

        $resultsArrayStudentNumbers = $results->pluck('student_id')->toArray();
        $arrayOfProgrammes = $this->arrayOfValidProgrammes($coursesInEdurole->StudyID);

        // return $arrayOfProgrammes;

        // ??TO DO: Add the study ID to the query below

        $resultsFromBasicInformation= EduroleBasicInformation::join('student-study-link', 'student-study-link.StudentID', '=', 'basic-information.ID')
            ->join('study', 'study.ID', '=', 'student-study-link.StudyID')            
            ->join('schools', 'schools.ID', '=', 'study.ParentID')
            ->select('basic-information.ID', 'basic-information.FirstName', 'basic-information.Surname','basic-information.StudyType', 'basic-information.PrivateEmail', 'study.Name as Programme', 'schools.Name as School', 'study.ID as StudyID')
            ->whereIn('basic-information.ID', $resultsArrayStudentNumbers)
            // ->whereIn('study.ID', $arrayOfProgrammes)
            ->get();
        // return $resultsFromBasicInformation;
        $results = $results->map(function ($result) use ($resultsFromBasicInformation) {
            $result->basic_information = $resultsFromBasicInformation->firstWhere('ID', $result->student_id);
            return $result;
        });
        // return $results;
        return view('coordinator.viewTotalCaAndExam', compact('componentId','delivery','results', 'statusId', 'courseId','courseDetails','hasComponents')); 
    }

    public function viewTotalCaInComponentCourse(Request $request ,$statusId, $courseIdValue, $basicInformationId,$delivery){
        $courseId = Crypt::decrypt($courseIdValue);
        // $caType = Crypt::decrypt($statusId);
        $basicInformationId = Crypt::decrypt($basicInformationId);
        $delivery = Crypt::decrypt($delivery);
        $componentId = $request->componentId;
        $hasComponents = $request->hasComponents;

        // return $courseId . ' ' . $basicInformationId . ' ' . $delivery . ' ' . $componentId . ' ' . $hasComponents;
        $courseDetails = EduroleCourses::where('ID', $courseId)->first();
        $coursesInEdurole = $this->getCoursesFromEdurole()
                ->where('courses.ID', $courseId)
                ->where('study.ProgrammesAvailable', $basicInformationId)
                ->where('study.Delivery', $delivery)
                ->first();
        // return $courseDetails;
        // if($caType != 4){            
        $resultsGetAllInstances = StudentsContinousAssessment::where('students_continous_assessments.course_id', $courseId)
            ->where('students_continous_assessments.delivery_mode', $delivery)
            ->where('students_continous_assessments.study_id', $coursesInEdurole->StudyID)
            ->whereNotNull('students_continous_assessments.component_id');
        
        // Count the number of unique instances based on component_id
        $numberOfUniqueInstances = $resultsGetAllInstances->distinct('students_continous_assessments.component_id')->count('students_continous_assessments.component_id');
        
        // Calculate the total marks for each student
        $results = $resultsGetAllInstances
            ->join('course_assessments', 'course_assessments.course_assessments_id', '=', 'students_continous_assessments.course_assessment_id')
            ->select('students_continous_assessments.student_id', DB::raw('SUM(students_continous_assessments.sca_score) as total_marks'))
            ->groupBy('students_continous_assessments.student_id')
            ->get();
        
        // Divide the total marks by the number of unique instances
        $results->transform(function ($item) use ($numberOfUniqueInstances) {
            $item->total_marks = round($item->total_marks / $numberOfUniqueInstances, 2);
            return $item;
        });

        // return $results;
            
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
        return view('coordinator.viewTotalCaInCourse', compact('delivery','results', 'statusId', 'courseId','courseDetails','hasComponents')); 
    }

    public function viewTotalCaInComponentCourseAndFinalExam(Request $request ,$statusId, $courseIdValue, $basicInformationId,$delivery){
        $courseId = Crypt::decrypt($courseIdValue);
        // $caType = Crypt::decrypt($statusId);
        $basicInformationId = Crypt::decrypt($basicInformationId);
        $delivery = Crypt::decrypt($delivery);
        $componentId = $request->componentId;
        $hasComponents = $request->hasComponents;

        // return $courseId . ' ' . $basicInformationId . ' ' . $delivery . ' ' . $componentId . ' ' . $hasComponents;
        $courseDetails = EduroleCourses::where('ID', $courseId)->first();
        $coursesInEdurole = $this->getCoursesFromEdurole()
                ->where('courses.ID', $courseId)
                ->where('study.ProgrammesAvailable', $basicInformationId)
                ->where('study.Delivery', $delivery)
                ->first();
        // return $courseDetails;
        // if($caType != 4){            
        $resultsGetAllInstances = StudentsContinousAssessment::where('students_continous_assessments.course_id', $courseId)
            ->where('students_continous_assessments.delivery_mode', $delivery)
            ->where('students_continous_assessments.study_id', $coursesInEdurole->StudyID)
            ->whereNotNull('students_continous_assessments.component_id');
        
        // Count the number of unique instances based on component_id
        $numberOfUniqueInstances = $resultsGetAllInstances->distinct('students_continous_assessments.component_id')->count('students_continous_assessments.component_id');
        
        // Calculate the total marks for each student
        $results = $resultsGetAllInstances
            ->join('course_assessments', 'course_assessments.course_assessments_id', '=', 'students_continous_assessments.course_assessment_id')
            ->select('students_continous_assessments.student_id', DB::raw('SUM(students_continous_assessments.sca_score) as total_marks'))
            ->groupBy('students_continous_assessments.student_id')
            ->get();
        
        // Divide the total marks by the number of unique instances
        $results->transform(function ($item) use ($numberOfUniqueInstances) {
            $item->total_marks = round($item->total_marks / $numberOfUniqueInstances, 2);
            return $item;
        });

        // return $results;
            
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
        return view('coordinator.viewTotalCaAndExam', compact('delivery','results', 'statusId', 'courseId','courseDetails','hasComponents')); 
    }

    public function deleteCaInCourse(Request $request, $courseAssessmentId, $courseId)
    {
        $courseAssessmentId = Crypt::decrypt($courseAssessmentId);
        $courseId = Crypt::decrypt($courseId);

        DB::beginTransaction();

        $courseCode = EduroleCourses::where('ID', $courseId)->first()->Name;

        try {
            // Fetch the course assessment record
            $courseAssessment = CourseAssessment::where('course_assessments_id', $courseAssessmentId)
                ->where('ca_type', $request->ca_type)
                ->where('course_id', $courseId)
                ->where('delivery_mode', $request->delivery)
                ->where('study_id', $request->study_id)
                ->firstOrFail(); // Fail if not found

            // Fetch course assessment scores
            $getCourseAssessmentsScores = CourseAssessmentScores::where('course_assessment_id', $courseAssessmentId)
                ->where('course_code', $courseCode)
                ->where('delivery_mode', $request->delivery)
                ->where('study_id', $request->study_id)
                ->get();  // Get the full collection instead of `pluck`

            if ($getCourseAssessmentsScores->isEmpty()) {
                throw new \Exception('No assessment scores found.');
            }

            // Renew continuous assessments before deletion
            foreach ($getCourseAssessmentsScores as $entry) {
                $this->renewCABeforeDelete($courseId, $request->academicYear, $request->ca_type, trim($entry->student_id), $courseAssessmentId, $request->delivery, $courseAssessment->study_id, $courseAssessment->component_id);
            }

            // Delete the course assessment scores
            $getCourseAssessmentsScores->each(function ($item) {
                $item->delete();
            });

            // Delete the course assessment itself
            $courseAssessment->delete();

            // Commit the transaction if everything is successful
            DB::commit();

            return redirect()->back()->with('success', 'Data deleted successfully');
        } catch (DecryptException $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Decryption failed.');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Database query failed.');
        } catch (\Exception $e) {
            // Rollback the transaction if there is an error
            DB::rollBack();
            return redirect()->back()->with('error', 'Data deletion failed: ' . $e->getMessage());
        }
    }



    public function deleteStudentCaInCourse( Request $request)
    {        
        
        DB::beginTransaction();
        
        try {
            // Fetch the course assessment record    
            $courseAssessmenScoresId = $request->courseAssessmentScoresId;        
            $getCourseAssessmentsScores = CourseAssessmentScores::where('course_assessment_scores_id', $courseAssessmenScoresId);
            $courseAssessmentsScores = $getCourseAssessmentsScores->pluck('student_id')->toArray();
            Log::info($courseAssessmentsScores);
            $courseAssessmentId = $getCourseAssessmentsScores->first()->course_assessment_id;
            // $courseId = $getCourseAssessmentsScores->first()->course_id;
            $delivery = $getCourseAssessmentsScores->first()->delivery_mode;
            $study_id = $getCourseAssessmentsScores->first()->study_id;
            $component_id = $getCourseAssessmentsScores->first()->component_id;
            $academicYear = 2024;
            $caType = $request->caType;
            $courseId = $request->courseId;
            // $ca_type = $getCourseAssessmentsScores->first()->ca_type;   

            // Update and renew the continuous assessments before deletion
            foreach ($courseAssessmentsScores as $entry) {
                $this->renewCABeforeDelete($courseId, $academicYear, $caType, trim($entry), $courseAssessmentId, $delivery, $study_id,$component_id);
            }
            CourseAssessmentScores::where('course_assessment_scores_id', $courseAssessmenScoresId)->delete();
            
            // Find and delete orphaned continuous assessments
            // $assessmentsToDelete = StudentsContinousAssessment::leftJoin('course_assessments', 'students_continous_assessments.course_assessment_id', '=', 'course_assessments.course_assessments_id')
            //     ->whereNull('course_assessments.course_assessments_id')
            //     ->select('students_continous_assessments.students_continous_assessment_id')
            //     ->get();
            
            // foreach ($assessmentsToDelete as $assessment) {
            //     $assessmentInstance = StudentsContinousAssessment::find($assessment->students_continous_assessment_id);
            //     if ($assessmentInstance) {
            //         $assessmentInstance->delete();
            //     }
            // }
            
            // Commit the transaction if everything is successful
            DB::commit();

            return redirect()->back()->with('success', 'Data deleted successfully');
        } catch (\Exception $e) {
            // Rollback the transaction if there is an error
            DB::rollBack();

            return redirect()->back()->with('error', 'Data deletion failed: ' . $e->getMessage());
        }
    }

    public function deleteStudentExamInCourse( Request $request)
    {        
        
        DB::beginTransaction();
        
        // try {
        //     // Fetch the course assessment record    
        //     $courseAssessmenScoresId = $request->courseAssessmentScoresId;        
        //     $getCourseAssessmentsScores = CourseAssessmentScores::where('course_assessment_scores_id', $courseAssessmenScoresId);
        //     $courseAssessmentsScores = $getCourseAssessmentsScores->pluck('student_id')->toArray();
        //     Log::info($courseAssessmentsScores);
        //     $courseAssessmentId = $getCourseAssessmentsScores->first()->course_assessment_id;
        //     // $courseId = $getCourseAssessmentsScores->first()->course_id;
        //     $delivery = $getCourseAssessmentsScores->first()->delivery_mode;
        //     $study_id = $getCourseAssessmentsScores->first()->study_id;
        //     $component_id = $getCourseAssessmentsScores->first()->component_id;
        //     $academicYear = 2024;
        //     $caType = $request->caType;
        //     $courseId = $request->courseId;
        //     // $ca_type = $getCourseAssessmentsScores->first()->ca_type;   

        //     // Update and renew the continuous assessments before deletion
        //     foreach ($courseAssessmentsScores as $entry) {
        //         $this->renewCABeforeDelete($courseId, $academicYear, $caType, trim($entry), $courseAssessmentId, $delivery, $study_id,$component_id);
        //     }
        //     CourseAssessmentScores::where('course_assessment_scores_id', $courseAssessmenScoresId)->delete();
            
        //     // Find and delete orphaned continuous assessments
        //     // $assessmentsToDelete = StudentsContinousAssessment::leftJoin('course_assessments', 'students_continous_assessments.course_assessment_id', '=', 'course_assessments.course_assessments_id')
        //     //     ->whereNull('course_assessments.course_assessments_id')
        //     //     ->select('students_continous_assessments.students_continous_assessment_id')
        //     //     ->get();
            
        //     // foreach ($assessmentsToDelete as $assessment) {
        //     //     $assessmentInstance = StudentsContinousAssessment::find($assessment->students_continous_assessment_id);
        //     //     if ($assessmentInstance) {
        //     //         $assessmentInstance->delete();
        //     //     }
        //     // }
            
        //     // Commit the transaction if everything is successful
        //     DB::commit();

        //     return redirect()->back()->with('success', 'Data deleted successfully');
        // } catch (\Exception $e) {
        //     // Rollback the transaction if there is an error
        //     DB::rollBack();

        //     return redirect()->back()->with('error', 'Data deletion failed: ' . $e->getMessage());
        // }
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
            // 'component_id' => 'required',
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
                        // if ($actualColumnCount != $expectedColumnCount) {
                        //     $reader->close();
                        //     return back()->with('error', "The uploaded Excel sheet must contain exactly $expectedColumnCount columns.");
                        // }
                        try {
                            // Clean and trim the student number
                            $studentNumber = trim($row->getCellAtIndex(0)->getValue());
                            // if (!is_numeric($studentNumber) || strlen($studentNumber) < 7 || strlen($studentNumber) > 10) {
                            //     throw new \Exception("Student number contains special characters or is not within the valid length range.");
                            // }

                            // Clean and trim the mark, then convert it to a float
                            $mark = trim($row->getCellAtIndex(1)->getValue());
                            // if (!is_numeric($mark)) {
                            //     throw new \Exception("Mark is not a valid number.");
                            // }
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
                        'component_id' => $request->component_id,
                    ]);

                    foreach ($data as $entry) {
                        CourseAssessmentScores::updateOrCreate(
                            [
                                'course_assessment_id' => $newAssessment->course_assessments_id,
                                'student_id' => trim($entry['student_number']),
                                'course_code' => $request->course_code,
                                'delivery_mode' => $request->delivery,
                                'study_id' => $request->study_id,
                                'component_id' => $request->component_id,
                            ],
                            [
                                'cas_score' => $entry['mark'],
                            ]
                        );
                        $this->calculateAndSubmitCA($request->course_id, $request->academicYear, $request->ca_type, trim($entry['student_number']), $newAssessment->course_assessments_id, $request->delivery, $request->study_id,$request->component_id);
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

    public function importFinalExamFromExcelSheet(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M'); // Adjust as needed
        
        // Validate the form data
        $request->validate([
            'excelFile' => 'required|mimes:xls,xlsx,csv',
            'academicYear' => 'required',
            // 'ca_type' => 'required',
            'course_id' => 'required',
            'course_code' => 'required',
            'basicInformationId' => 'required',
            'delivery' => 'required',
            'study_id' => 'required',
            // 'component_id' => 'required',
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
                        // if ($actualColumnCount != $expectedColumnCount) {
                        //     $reader->close();
                        //     return back()->with('error', "The uploaded Excel sheet must contain exactly $expectedColumnCount columns.");
                        // }
                        try {
                            // Clean and trim the student number
                            $studentNumber = trim($row->getCellAtIndex(0)->getValue());
                            // if (!is_numeric($studentNumber) || strlen($studentNumber) < 7 || strlen($studentNumber) > 10) {
                            //     throw new \Exception("Student number contains special characters or is not within the valid length range.");
                            // }

                            // Clean and trim the mark, then convert it to a float
                            $mark = trim($row->getCellAtIndex(1)->getValue());
                            // if (!is_numeric($mark)) {
                            //     throw new \Exception("Mark is not a valid number.");
                            // }
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
                    // $newExam = FinalExamination::updateOrCreate([
                    //     'course_id' => $request->course_id,
                    //     // 'ca_type' => $request->ca_type,
                    //     'course_code' => $request->course_code,
                    //     'description' => $request->description,
                    //     'academic_year' => $request->academicYear,
                    //     'basic_information_id' => $request->basicInformationId,
                    //     'delivery_mode' => $request->delivery,
                    //     'study_id' => $request->study_id,
                    //     'component_id' => $request->component_id,
                    // ]);

                    foreach ($data as $entry) {
                        $newExam = FinalExamination::updateOrCreate(
                            [
                                'course_id' => $request->course_id,
                                'student_id' => trim($entry['student_number']),
                                // 'ca_type' => $request->ca_type,
                                'course_code' => $request->course_code,
                                // 'description' => $request->description,
                                'academic_year' => $request->academicYear,
                                'basic_information_id' => $request->basicInformationId,
                                'delivery_mode' => $request->delivery,
                                'study_id' => $request->study_id,                               
                            ],
                            [
                                'cas_score' => $entry['mark'],
                            ]
                        );
                        $this->calculateAndSubmitFinalExam($request->course_id, $request->academicYear,  trim($entry['student_number']), $newExam->final_examinations_id, $request->delivery, $request->study_id, $request->basicInformationId, $entry['mark']);
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->with('error', 'An error occurred while importing the data. Please try again. Error: ' . $e->getMessage());
                }
            }

            // $statusIdToRoute = encrypt($request->ca_type);
            // $courseIdToRoute = encrypt($newAssessment->course_assessments_id);
            // $assessmentNumber = encrypt(1);
            // $delivery = encrypt($request->delivery);
            return redirect()->back()->with('success', 'Data imported successfully');
            // return redirect()->route('coordinator.viewSpecificCaInCourse', ['statusId' => $statusIdToRoute, 'courseIdValue' => $courseIdToRoute, 'assessmentNumber' => $assessmentNumber])->with('success', 'Data imported successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred during the upload process. Please try again. Error: ' . $e->getMessage());
        }
    }

    public function importFinalExamAndCaFromExcelSheet(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');
    
        try {
            // Validation
            $request->validate([
                'excelFile' => 'required|mimes:xls,xlsx,csv',
                'academicYear' => 'required',
                'course_id' => 'required',
                'course_code' => 'required',
                'basicInformationId' => 'required',
                'delivery' => 'required',
                'study_id' => 'required',
                'typeOfExam' => 'required|in:1,2',
            ]);
    
            $typeOfExam = $request->typeOfExam;
            if (!$request->hasFile('excelFile')) {
                return back()->with('error', 'No file was uploaded. Please try again.');
            }
    
            $file = $request->file('excelFile');
            $filePath = $file->getPathname();
    
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->open($filePath);
    
            $data = [];
            $rowNumber = 0;
            $errors = [];
    
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $rowNumber++;
                    try {
                        $studentNumber = trim($row->getCellAtIndex(0)->getValue());
                        if (empty($studentNumber)) {
                            $errors[] = "Row {$rowNumber}: Student number cannot be empty.";
                            continue;
                        }
    
                        $caScore = $typeOfExam == 1 ? ($row->getCellAtIndex(1) ? trim($row->getCellAtIndex(1)->getValue()) : null) : null;
                        $examScore = $row->getCellAtIndex($typeOfExam == 1 ? 2 : 1) ? trim($row->getCellAtIndex($typeOfExam == 1 ? 2 : 1)->getValue()) : null;
                        $gradeFromExcel = $row->getCellAtIndex($typeOfExam == 1 ? 3 : 2) ? trim($row->getCellAtIndex($typeOfExam == 1 ? 3 : 2)->getValue()) : null;

    
                        $totalMark = $typeOfExam == 1 ? ($caScore + $examScore) : $examScore;
                        $grade = null;
    
                        // Determine grade based on available data
                        if (is_numeric($totalMark) && $totalMark >= 0) {
                            if ($totalMark >= 90) $grade = 'A+';
                            elseif ($totalMark >= 80) $grade = 'A';
                            elseif ($totalMark >= 70) $grade = 'B+';
                            elseif ($totalMark >= 60) $grade = 'B';
                            elseif ($totalMark >= 55) $grade = 'C+';
                            elseif ($totalMark >= 50) $grade = 'C';
                            elseif ($totalMark >= 45) $grade = 'D+';
                            elseif ($totalMark >= 40) $grade = 'D';
                            else $grade = 'F';
                        } elseif (!empty($gradeFromExcel)) {
                            $grade = $gradeFromExcel; // Use pre-assigned grade if provided
                        } elseif (empty($examScore) || !is_numeric($examScore)) {
                            $grade = 'NE'; // No Exam score                        
                        }
    
                        $data[] = [
                            'student_id' => $studentNumber,
                            'ca' => $typeOfExam == 1 ? (is_numeric($caScore) ? (float)$caScore : null) : null,
                            'exam' => is_numeric($examScore) ? (float)$examScore : null,
                            'grade' => $grade,
                        ];
                    } catch (\Exception $e) {
                        $errors[] = "Row {$rowNumber}: Error processing row - {$e->getMessage()}";
                    }
                }
            }
            $reader->close();
    
            if (!empty($errors)) {
                return back()->with('error', 'Errors found in upload:<br>' . implode('<br>', $errors));
            }
    
            if (empty($data)) {
                return back()->with('error', 'No valid data found in the uploaded file.');
            }
    
            DB::beginTransaction();
            try {
                $successCount = 0;
                $updateCount = 0;
    
                foreach ($data as $entry) {
                    $conditions = [
                        'student_id' => $entry['student_id'],
                        'course_code' => $request->course_code,
                        'delivery_mode' => $request->delivery,
                        'study_id' => $request->study_id,
                        'course_id' => $request->course_id,
                        'academic_year' => $request->academicYear,
                    ];
    
                    $values = [
                        'basic_information_id' => $request->basicInformationId,
                        'status' => 1,
                        'type_of_exam' => $typeOfExam,
                        'ca' => $entry['ca'],
                        'exam' => $entry['exam'],
                        'grade' => $entry['grade'],
                    ];
    
                    $result = CaAndExamUpload::updateOrCreate($conditions, $values);
                    if ($result->wasRecentlyCreated) {
                        $successCount++;
                    } else {
                        $updateCount++;
                    }
                }
    
                DB::commit();
                $message = "Data imported successfully. ";
                $message .= $successCount > 0 ? "{$successCount} new records created. " : "";
                $message .= $updateCount > 0 ? "{$updateCount} records updated." : "";
    
                return redirect()->back()->with('success', $message);
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Database error: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            return back()->with('error', 'System error: ' . $e->getMessage());
        }
    }
    

    public function importStudentCA(Request $request)
    {
        set_time_limit(1200000);

        // Validate the form data
        $request->validate([
            'studentNumber' => 'required',
            // 'oldStudentNumber' => 'required',
            'mark' => 'required',
            'academicYear' => 'required',
            'course_assessment_id' => 'required',
            'course_id' => 'required',
            'course_code' => 'required',
            'basicInformationId' => 'required',
            'delivery' => 'required',
            'study_id' => 'required',
            'course_assessment_scores_id' => 'required',
            // 'component_id' => 'required',
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
            if($request->component_id){
                $componentId = $request->component_id;
            }else{
                $componentId =  null;
            }
            // $courseAssessmentScoresStudentNumber = CourseAssessmentScores::where('course_assessment_scores_id', $request->course_assessment_scores_id)->first()->student_id;
            
            CourseAssessmentScores::updateOrCreate([
                'course_assessment_id' => $request->course_assessment_id,
                'student_id' => trim($request->studentNumber),
                'component_id' => $componentId,
                'course_code' => $request->course_code,
                'delivery_mode' => $request->delivery,
                'study_id' => $studyId,
            ],[
                'cas_score' => trim($request->mark),
            ]);
            $this->calculateAndSubmitCA($request->course_id, $request->academicYear, $caType, trim($request->studentNumber), $request->course_assessment_id, $request->delivery, $studyId,$componentId);
            
            DB::commit();

            return redirect()->back()->with('success', 'Student Updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to import data: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while importing the data. Please try again.');
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
            // 'component_id' => 'required',
        ]);

        DB::beginTransaction();

        // try {
            $newAssessment = CourseAssessment::where('course_assessments_id', $request->course_assessment_id)
                ->update([
                    'academic_year' => $request->academicYear,
                    'basic_information_id' => $request->basicInformationId,
                ]);

            $getCaType = CourseAssessment::where('course_assessments_id', $request->course_assessment_id)->first();
            $caType = $request->ca_type;
            $studyId = $request->study_id;
            // return ' Ca Type: ' . $caType . ' Study ID: ' . $studyId;
            if($request->component_id){
                $componentId = $request->component_id;
            }else{
                $componentId =  null;
            }
            

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
                        'component_id' => $componentId,
                        'study_id' => $studyId,
                    ],[
                        'cas_score' => $entry['mark'],
                    ]);
                    $this->calculateAndSubmitCA($request->course_id, $request->academicYear, $caType, trim($entry['student_number']), $request->course_assessment_id, $request->delivery, $studyId,$componentId);
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Data imported successfully');
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     Log::error('Failed to import data: ' . $e->getMessage());
        //     return back()->with('error', 'An error occurred while importing the data. Please try again.');
        // }
    }

    public function updateCAForSingleStudent(Request $request)
    {
        set_time_limit(1200000);

        // Validate the form data
        $request->validate([
            'studentNumber' => 'required',
            'oldStudentNumber' => 'required',
            'mark' => 'required',
            'academicYear' => 'required',
            'course_assessment_id' => 'required',
            'course_id' => 'required',
            'course_code' => 'required',
            'basicInformationId' => 'required',
            'delivery' => 'required',
            'study_id' => 'required',
            'course_assessment_scores_id' => 'required',
            // 'component_id' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $newAssessment = CourseAssessment::where('course_assessments_id', $request->course_assessment_id)
                ->update([
                    'academic_year' => $request->academicYear,
                    'basic_information_id' => $request->basicInformationId,
                ]);

            $getCaType = CourseAssessment::where('course_assessments_id', $request->course_assessment_id)->first();
            $caType = $request->ca_type;
            $studyId = $request->study_id;
            if($request->component_id){
                $componentId = $request->component_id;
            }else{
                $componentId =  null;
            }
            // $courseAssessmentScoresStudentNumber = CourseAssessmentScores::where('course_assessment_scores_id', $request->course_assessment_scores_id)->first()->student_id;
            
            CourseAssessmentScores::updateOrCreate([
                'course_assessment_id' => $request->course_assessment_id,
                'student_id' => trim($request->studentNumber),
                'component_id' => $componentId,
                'course_code' => $request->course_code,
                'delivery_mode' => $request->delivery,
                'study_id' => $studyId,
            ],[
                'cas_score' => trim($request->mark),
            ]);
            $this->calculateAndSubmitCA($request->course_id, $request->academicYear, $caType, trim($request->studentNumber), $request->course_assessment_id, $request->delivery, $studyId,$componentId);
            
            if(trim($request->studentNumber) != trim($request->oldStudentNumber)){
                $studentNumber = $request->studentNumber;
                $deletionData = [
                    'courseAssessmentScoresId' => $request->course_assessment_scores_id,
                    'caType' => $caType,
                    'courseId' => $request->course_id,
                ];
                // Create a new request instance with the custom data
                $deletionRequest = Request::create('', 'POST', $deletionData);
                
                // Call the method with the new request object
                $this->deleteStudentCaInCourse($deletionRequest);
            }          

            DB::commit();

            return redirect()->back()->with('success', 'Student Updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to import data: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while importing the data. Please try again.');
        }
    }

    public function exportBoardOfExaminersReportFinalExam($basicInformationId){
        $basicInformationId = Crypt::decrypt($basicInformationId);
        // return $basicInformationId;
        $getStudyId = EduroleStudy::where('ProgrammesAvailable', '=', $basicInformationId)->first();
        // return $getStudyId;
        $studyId = $getStudyId->ID;
        // return $coursesFromCourseElectives;
        
        // $naturalScienceCourses = $this->getNSAttachedCourses();
        if($studyId == 163 || $studyId == 165 || $studyId == 166 || $studyId == 167 || $studyId == 168 || $studyId == 169 || $studyId == 170 || $studyId == 171 || $studyId == 172 || $studyId == 173 || $studyId == 174){
            $results = $this->getCoursesFromEdurole()
            ->where('basic-information.ID', $basicInformationId)            
            ->orderBy('programmes.Year')
            ->orderBy('courses.Name')
            ->orderBy('study.Delivery')            
            ->get();
        }else{
            $coursesFromCourseElectives = EduroleCourseElective::select('course-electives.CourseID')
                ->join('courses', 'courses.ID','=','course-electives.CourseID')
                ->join('program-course-link', 'program-course-link.CourseID','=','courses.ID')
                ->join('student-study-link','student-study-link.StudentID','=','course-electives.StudentID')
                ->join('study','study.ID','=','student-study-link.StudyID')
                ->where('course-electives.Year', 2024)  
                ->where('course-electives.Approved', 1)
                ->where('study.ProgrammesAvailable', $basicInformationId)
                ->distinct()
                ->pluck('course-electives.CourseID')
                ->toArray();
            $results = $this->getCoursesFromEdurole()
                ->where('basic-information.ID', $basicInformationId)
                ->whereIn('courses.ID', $coursesFromCourseElectives)
                ->orderBy('programmes.Year')
                ->orderBy('courses.Name')
                ->orderBy('study.Delivery')            
                ->get();
        }        
        
        return view('coordinator.reports.viewCoordinatorsExamReport', compact('results','studyId'));
    }

    public function exportBoardOfExaminersReport($basicInformationId){

        $basicInformationId = Crypt::decrypt($basicInformationId);
        // return $basicInformationId;
        $getStudyId = EduroleStudy::where('ProgrammesAvailable', '=', $basicInformationId)->first();
        // return $getStudyId;
        $studyId = $getStudyId->ID;
        // return $coursesFromCourseElectives;

        
        // $naturalScienceCourses = $this->getNSAttachedCourses();
        if($studyId == 163 || $studyId == 165 || $studyId == 166 || $studyId == 167 || $studyId == 168 || $studyId == 169 || $studyId == 170 || $studyId == 171 || $studyId == 172 || $studyId == 173 || $studyId == 174){
            $results = $this->getCoursesFromEdurole()
            ->where('basic-information.ID', $basicInformationId)            
            ->orderBy('programmes.Year')
            ->orderBy('courses.Name')
            ->orderBy('study.Delivery')            
            ->get();
        }else{
            $coursesFromCourseElectives = EduroleCourseElective::select('course-electives.CourseID')
                ->join('courses', 'courses.ID','=','course-electives.CourseID')
                ->join('program-course-link', 'program-course-link.CourseID','=','courses.ID')
                ->join('student-study-link','student-study-link.StudentID','=','course-electives.StudentID')
                ->join('study','study.ID','=','student-study-link.StudyID')
                ->where('course-electives.Year', 2024)  
                ->where('course-electives.Approved', 1)
                ->where('study.ProgrammesAvailable', $basicInformationId)
                ->distinct()
                ->pluck('course-electives.CourseID')
                ->toArray();
            $results = $this->getCoursesFromEdurole()
                ->where('basic-information.ID', $basicInformationId)
                ->whereIn('courses.ID', $coursesFromCourseElectives)
                ->orderBy('programmes.Year')
                ->orderBy('courses.Name')
                ->orderBy('study.Delivery')            
                ->get();
        }
        
        
        return view('coordinator.reports.viewCoordinatorsCourses', compact('results','studyId'));
    }

    public function getCoursesWithResults(){
        // $results = $this->queryCoursesWithResults();
        $results = "here";
        return $results;
    }

    public function exportData($headers, $rowData, $results, $filename)
    {
        $filePath = storage_path('app/' . $filename);

        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($filePath);

        $headerRow = WriterEntityFactory::createRowFromArray($headers);
        $writer->addRow($headerRow);

        foreach ($results as $result) {
            $data = [];
            foreach ($rowData as $field) {
                $data[] = $result->$field;
            }

            $dataRow = WriterEntityFactory::createRowFromArray($data);
            $writer->addRow($dataRow);
        }
        $writer->close();
        return response()->download($filePath, $filename . '.xlsx')->deleteFileAfterSend();
    }

    public function updateExamForSingleStudent(Request $request)
    {
        set_time_limit(1200000);

        // Validate the form data
        $request->validate([
            'studentNumber' => 'required',
            'oldStudentNumber' => 'required',
            'mark' => 'required',
            'academicYear' => 'required',
            'course_id' => 'required',
            'course_code' => 'required',
            'basicInformationId' => 'required',
            'delivery' => 'required',
            'study_id' => 'required',
            'final_examination_results_id' => 'required',
            // 'component_id' => 'required',
        ]);

        DB::beginTransaction();

        try {
            
            $getCaType = CourseAssessment::where('course_assessments_id', $request->course_assessment_id)->first();
            $caType = $request->ca_type;
            $studyId = $request->study_id;
            if($request->component_id){
                $componentId = $request->component_id;
            }else{
                $componentId =  null;
            }
            // $courseAssessmentScoresStudentNumber = CourseAssessmentScores::where('course_assessment_scores_id', $request->course_assessment_scores_id)->first()->student_id;
            
            CourseAssessmentScores::updateOrCreate([
                'course_assessment_id' => $request->course_assessment_id,
                'student_id' => trim($request->studentNumber),
                'component_id' => $componentId,
                'course_code' => $request->course_code,
                'delivery_mode' => $request->delivery,
                'study_id' => $studyId,
            ],[
                'cas_score' => trim($request->mark),
            ]);
            $this->calculateAndSubmitCA($request->course_id, $request->academicYear, $caType, trim($request->studentNumber), $request->course_assessment_id, $request->delivery, $studyId,$componentId);
            
            if(trim($request->studentNumber) != trim($request->oldStudentNumber)){
                $studentNumber = $request->studentNumber;
                $deletionData = [
                    'courseAssessmentScoresId' => $request->course_assessment_scores_id,
                    'caType' => $caType,
                    'courseId' => $request->course_id,
                ];
                // Create a new request instance with the custom data
                $deletionRequest = Request::create('', 'POST', $deletionData);
                
                // Call the method with the new request object
                $this->deleteStudentCaInCourse($deletionRequest);
            }          

            DB::commit();

            return redirect()->back()->with('success', 'Student Updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to import data: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while importing the data. Please try again.');
        }
    }
    
    private function calculateScores($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery, $studyId,$componentId, $excludeCurrent = false)
    {
        DB::beginTransaction();

        try {
            // Fetch CA scores
            $caScores = $this->getCourseAssessmentScores($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery, $studyId,$componentId, $excludeCurrent);
            $total = $caScores->sum('mark');            
            // Fetch the count of assessments
            $count = $this->getNumberOfAssessmnets($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery, $studyId,$componentId, $excludeCurrent);
            Log::info('Total score: ' . $total . ' Count: ' . $count);
            // Fetch max score
            $maxScore = $this->getMaxScore($courseId, $caType, $delivery, $studyId,$componentId);
            // Calculate average and adjusted average
            $average = $count > 0 ? $total / $count : 0;
            $adjustedAverage = ($average / 100) * $maxScore;
            // Save or update the student's CA
            $this->saveOrUpdateStudentCA($studentNumber, $courseId, $academicYear, $caType, $courseAssessmentId, $adjustedAverage, $delivery, $studyId,$componentId);
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

    private function calculateFinalExamScores($courseId, $academicYear, $studentNumber, $finalExamId, $delivery, $studyId, $basicInformationId, $mark) {
        DB::beginTransaction();
    
        try {
            $totalMark = 60;
            $percentOfTotal = ($mark * $totalMark) / 100;
    
            FinalExaminationResults::updateOrCreate(                [
                    'student_id' => $studentNumber,
                    'course_id' => $courseId,
                    'academic_year' => $academicYear,
                    'final_examinations_id' => $finalExamId,
                    'delivery_mode' => $delivery,
                    'study_id' => $studyId,
                    'basic_information_id'=> $basicInformationId,
                ],
                [
                    'cas_score' => $percentOfTotal,
                ]
            );
    
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


    private function getNumberOfAssessmnets($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId,$delivery,$studyId,$componentId, $excludeCurrent){
        return CourseAssessmentScores::where('course_assessments.course_id', $courseId)
            ->where('course_assessments.academic_year', $academicYear)
            ->where('course_assessments.ca_type', $caType)
            ->where('course_assessments.delivery_mode', $delivery)
            ->where('course_assessments.component_id', $componentId)
            // ->where('course_assessment_scores.student_id', $studentNumber)
            ->where('course_assessment_scores.study_id', $studyId)
            ->when($excludeCurrent, function ($query) use ($courseAssessmentId) {
                return $query->where('course_assessment_scores.course_assessment_id', '!=', $courseAssessmentId);
            })
            ->join('course_assessments', 'course_assessments.course_assessments_id', '=', 'course_assessment_scores.course_assessment_id')
            ->distinct('course_assessment_scores.course_assessment_id')
            ->count('course_assessment_scores.course_assessment_id');
    }
    
    private function getCourseAssessmentScores($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId,$delivery,$studyId,$componentId , $excludeCurrent){
        return CourseAssessmentScores::where('course_assessments.course_id', $courseId)
            ->where('course_assessments.academic_year', $academicYear)
            ->where('course_assessments.ca_type', $caType)
            ->where('course_assessments.delivery_mode', $delivery)
            ->where('course_assessment_scores.student_id', $studentNumber)
            ->where('course_assessment_scores.study_id', $studyId)
            ->where('course_assessment_scores.component_id', $componentId)
            ->when($excludeCurrent, function ($query) use ($courseAssessmentId) {
                return $query->where('course_assessment_scores.course_assessment_id', '!=', $courseAssessmentId);
            })
            ->select('course_assessment_scores.cas_score as mark')
            ->join('course_assessments', 'course_assessments.course_assessments_id', '=', 'course_assessment_scores.course_assessment_id')
            ->get();
    }
    
    private function getMaxScore($courseId, $caType, $delivery, $studyId,$componentId){
        $courseAssessmenetTypes = CATypeMarksAllocation::where('c_a_type_marks_allocations.course_id', $courseId)
            ->where('c_a_type_marks_allocations.assessment_type_id', $caType)
            ->where('c_a_type_marks_allocations.delivery_mode', $delivery)
            ->where('c_a_type_marks_allocations.study_id', $studyId)
            ->where('c_a_type_marks_allocations.component_id', $componentId)                    
            ->select('c_a_type_marks_allocations.total_marks')
            ->first();
        return $courseAssessmenetTypes->total_marks;
    }

    //Old Function
    private function saveOrUpdateStudentCA($studentNumber, $courseId, $academicYear, $caType, $courseAssessmentId, $adjustedAverage, $delivery, $studyId, $componentId){
        $studentCA = StudentsContinousAssessment::firstOrNew(['student_id' => $studentNumber,
                        'course_id' => $courseId,
                        'academic_year' => $academicYear, 
                        'ca_type' => $caType, 
                        'delivery_mode' => $delivery, 
                        'study_id' => $studyId,
                        'component_id' => $componentId
                    ]);
        $studentCA->course_assessment_id = $courseAssessmentId;
        $studentCA->sca_score = $adjustedAverage;
        $studentCA->save();
    }
    
    // private function saveOrUpdateStudentCA($studentNumber, $courseId, $academicYear, $caType, $courseAssessmentId, $adjustedAverage, $delivery, $studyId, $componentId) {
    //     StudentsContinousAssessment::updateOrCreate(
    //         [
    //             'student_id' => $studentNumber,
    //             'course_id' => $courseId,
    //             'academic_year' => $academicYear,
    //             'ca_type' => $caType,
    //             'delivery_mode' => $delivery,
    //             'study_id' => $studyId,
    //             'component_id' => $componentId,
    //             'course_assessment_id' => $courseAssessmentId,
    //         ],
    //         [
    //             'sca_score' => $adjustedAverage,
    //         ]
    //     );
    // }

    private function calculateAndSubmitFinalExam($courseId, $academicYear,  $studentNumber, $finalExamId, $delivery, $studyId, $basicInformationId, $mark){
        $this->calculateFinalExamScores($courseId, $academicYear,  $studentNumber, $finalExamId, $delivery, $studyId,$basicInformationId, $mark);
    }

    public function refreshAllStudentsMarks($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery, $studyId, $componentId){
        $this->refreshCAMark($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery, $studyId, $componentId);
    }
    
    private function calculateAndSubmitCA($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery, $studyId, $componentId){
        $this->calculateScores($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery, $studyId,$componentId, false);
    }

    
    
    private function renewCABeforeDelete($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery,$studyId, $componentId){
        $this->calculateScores($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery, $studyId,$componentId, true);
    }
    
    private function refreshCAMark($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery, $studyId, $componentId){        
        $this->calculateScores($courseId, $academicYear, $caType, $studentNumber, $courseAssessmentId, $delivery, $studyId, $componentId,false);        
    }
}
