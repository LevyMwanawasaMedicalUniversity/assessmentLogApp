<?php

namespace App\Http\Controllers;

use App\Models\CourseAssessment;
use App\Models\CourseComponentAllocation;
use App\Models\EduroleBasicInformation;
use App\Models\EduroleCourses;
use App\Models\StudentsContinousAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ContinousAssessmentController extends Controller
{

    public function searchForStudents(Request $request)
    {
        $searchTerm = $request->get('term');

        // Retrieve student IDs that match the search term
        $studentIds = StudentsContinousAssessment::where('student_id', 'LIKE', '%' . $searchTerm . '%')
                        ->distinct()
                        ->pluck('student_id');

        // Create an array of objects with label and id
        $results = $studentIds->map(function($studentId) {
            return [
                'label' => $studentId,  // Label for autocomplete
                'value' => $studentId,  // Value for autocomplete selection
                'id' => $studentId      // ID for URL construction
            ];
        });

        return response()->json($results);
    }

    public function studentsCAResults(Request $request)
    {
        // $courseAssessments = LMMAXCourseAssessment::join('course_assessment_scores', 'course_assessments.course_assessments_id', '=', 'course_assessment_scores.course_assessment_id')
        //     ->join('students_continous_assessments', 'course_assessments.course_assessments_id', '=', 'students_continous_assessments.course_assessment_id')
        //     ->where('students_continous_assessments.student_id', $studentNumber)
        //     ->select('students_continous_assessments.student_id', DB::raw('SUM(students_continous_assessments.sca_score) as total_marks'),'course_assessments.course_id','course_assessment_scores.course_code')
        //     ->groupBy('course_assessments.course_id','students_continous_assessments.student_id')
        //     ->get();
        $academicYear= 2024;
        $studentNumber = $request->studentId;

        // return $studentNumber;

        $results = StudentsContinousAssessment::join('course_assessments', 'course_assessments.course_assessments_id', '=', 'students_continous_assessments.course_assessment_id')        
            ->where('students_continous_assessments.student_id', $studentNumber)
            ->where('course_assessments.academic_year', $academicYear)    
            // ->where('students_continous_assessments.ca_type', '=', DB::raw('course_assessments.ca_type')) // Ensures ca_type matches
            ->select('students_continous_assessments.student_id',
                    'students_continous_assessments.course_id',
                    'students_continous_assessments.study_id',
                    'students_continous_assessments.delivery_mode',
                    DB::raw('SUM(students_continous_assessments.sca_score) as total_marks'))
            ->groupBy('students_continous_assessments.student_id',
                    'students_continous_assessments.course_id',
                    'students_continous_assessments.study_id',
                    'students_continous_assessments.delivery_mode')
            ->get();
        // $courseAssessmentScores = LMMAXCourseAssessmentScores::all();
        // $moodleCourses = MoodleCourses::all();
        // return  $results;

        $studentDetails = $this->getStudentDetails($studentNumber);
        if (!$studentDetails) {
            return redirect()->back()->with('error', 'Student Not Found on Edurole');
        }

        return view('allStudents.continousAssessment.viewCa', compact('results','studentNumber','studentDetails'));
    }

    private function getStudentDetails($studentNumber){
        $studentDetails = EduroleBasicInformation::where('basic-information.ID', $studentNumber)
                ->join('student-study-link', 'student-study-link.StudentID', '=', 'basic-information.ID')
                ->join('study', 'study.ID', '=', 'student-study-link.StudyID')
                ->join('schools', 'schools.ID', '=', 'study.ParentID')
                ->select('basic-information.ID', 'basic-information.FirstName','basic-information.PrivateEmail', 'basic-information.Surname','basic-information.StudyType', 'study.Name','schools.Description')
                ->first();
        return $studentDetails;
    }

    public function viewCaComponents(Request $request)
    {
        // $studentNumber = Auth::user()->name;
        // return $request->student_id;
        $studentNumber = Crypt::decrypt($request->student_id);
        $academicYear= 2024;

        $delivery = Crypt::decrypt($request->delivery_mode);
        $studyId = Crypt::decrypt($request->study_id);
        $courseId = Crypt::decrypt($request->course_id);
        if ($request->course_component_id) {
            $componentId = Crypt::decrypt($request->course_component_id);
        } else {
            $componentId = null;
        }

        if ($request->component_name) {
            $componentName = Crypt::decrypt($request->component_name);
        } else {
            $componentName = "";
        }

        
        
        $results = CourseAssessment::join('students_continous_assessments', 'course_assessments.course_assessments_id', '=', 'students_continous_assessments.course_assessment_id')
            ->join('assessment_types', 'assessment_types.id', '=', 'students_continous_assessments.ca_type')
            ->where('students_continous_assessments.student_id', $studentNumber)
            ->where('students_continous_assessments.course_id', $courseId)
            ->where('students_continous_assessments.study_id', $studyId)
            ->where('students_continous_assessments.delivery_mode', $delivery)  
            ->where('students_continous_assessments.component_id', $componentId)
            // ->where('students_continous_assessments.ca_type', '=', DB::raw('course_assessments.ca_type')) // Ensures ca_type matches
            ->select(
                'course_assessments.basic_information_id',
                'course_assessments.course_assessments_id',
                'course_assessments.course_id',
                'students_continous_assessments.student_id',
                'students_continous_assessments.component_id',
                'students_continous_assessments.delivery_mode',
                'students_continous_assessments.study_id',
                'students_continous_assessments.students_continous_assessment_id',
                'students_continous_assessments.student_id',
                DB::raw('SUM(students_continous_assessments.sca_score) as total_marks'),
                'students_continous_assessments.course_id',
                'students_continous_assessments.ca_type',
                'assessment_types.assesment_type_name'
            )
            ->groupBy(
                'students_continous_assessments.student_id',
                'students_continous_assessments.course_id',
                'students_continous_assessments.ca_type',
                'students_continous_assessments.component_id',
                'students_continous_assessments.delivery_mode',
                'students_continous_assessments.study_id',
                'students_continous_assessments.students_continous_assessment_id',
                'assessment_types.assesment_type_name'
            )
            ->get();

            // return $results;
            if ($results->isEmpty()) {
                return redirect()->back()->with('warning', 'No Results Uploaded Yet');
            }
            $studentDetails = $this->getStudentDetails($studentNumber);
            if (!$studentDetails) {
                return redirect()->back()->with('error', 'Student Not Found on Edurole');
            }

        // return $results;
        return view('allStudents.continousAssessment.viewCaComponents', compact('results','studentNumber','componentName','studentDetails'));
    }

    public function viewCaComponentsWithComponent(Request $request){
        $studentNumber = Crypt::decrypt($request->student_id);
        $academicYear= 2024;
        $delivery = Crypt::decrypt($request->delivery_mode);
        $studyId = Crypt::decrypt($request->study_id);
        $courseId = Crypt::decrypt($request->course_id);
        

        $results = CourseComponentAllocation::join('course_components', 'course_components.course_components_id', '=', 'course_component_allocations.course_component_id')
            ->where('course_id', $courseId)
            ->where('delivery_mode', $delivery)
            ->where('study_id', $studyId)
            ->where('academic_year', $academicYear)
            ->get();

        $course = EduroleCourses::where('ID', $courseId)->first();
        $courseName = $course->CourseDescription;
        $courseCode = $course->Name;   
        $studentDetails = $this->getStudentDetails($studentNumber);
        if (!$studentDetails) {
            return redirect()->back()->with('error', 'Student Not Found on Edurole');
        } 
        // return $results;
        return view('allStudents.continousAssessment.viewCourseComponents', compact('results','studentNumber','courseName','courseCode','studentDetails'));
    }

    public function viewInSpecificCaComponent(Request $request, $courseId,$caType)
    {
        $studentNumber = Crypt::decrypt($request->student_id);
        $academicYear= 2024;
        // return $studentNumber;
        if ($request->component_name) {
            $componentName = Crypt::decrypt($request->component_name);
        } else {
            $componentName = "";
        }

        if ($request->component_id) {
            $componentId = Crypt::decrypt($request->component_id);
        } else {
            $componentId = null;
        }
        
        $results = CourseAssessment::join('course_assessment_scores', 'course_assessment_scores.course_assessment_id', '=', 'course_assessments.course_assessments_id')
            ->join('assessment_types', 'assessment_types.id', '=', 'course_assessments.ca_type')
            ->where('course_assessments.course_id', $courseId)
            ->where('course_assessments.ca_type', $caType)
            ->where('course_assessments.academic_year', $academicYear)
            ->where('course_assessment_scores.student_id', $studentNumber)
            ->where('course_assessments.component_id', $componentId)
            ->orderBy('course_assessments.course_assessments_id', 'asc')
            ->get();
        if ($results->isEmpty()) {
            return redirect()->back()->with('warning', 'No Results Uploaded Yet');
        }

        $studentDetails = $this->getStudentDetails($studentNumber);
        if (!$studentDetails) {
            return redirect()->back()->with('error', 'Student Not Found on Edurole');
        } 
        
        return view('allStudents.continousAssessment.viewInSpecificCaComponent', compact('componentName','results','studentNumber','studentDetails'));
    }
}
