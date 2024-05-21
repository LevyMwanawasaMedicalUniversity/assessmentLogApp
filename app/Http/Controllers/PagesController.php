<?php

namespace App\Http\Controllers;

use App\Models\EduroleStudy;
use App\Models\StudentsContinousAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagesController extends Controller
{
    public function upload()
    {
        $user = auth()->user();
        try {
            $role = $user->roles->first()->name;
        } catch (\Exception $e) {
            return redirect()->route('dashboard');
        }
        $userBasicInformation = $user->basic_information_id;

        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->select('courses.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName','courses.CourseDescription')
            ->where('basic-information.ID', $userBasicInformation)
            ->get();
        return view('admin.viewCoordinatorsCourses', compact('results'));
    }

    public function dashboard()
    {
        $coursesFromLMMAX = $this->getCoursesFromLMMAX();

        $coursesWithCA = $this->getCoursesFromEdurole()
            ->whereIn('courses.Name', $coursesFromLMMAX)
            ->get();

        $coursesFromEdurole = $this->getCoursesFromEdurole()->get();
        // return $coursesWithCA;
        return view('dashboard', compact('coursesWithCA', 'coursesFromEdurole'));
    }

    private function getCoursesFromLMMAX()
    {
        return StudentsContinousAssessment::select('course_assessment_scores.course_code')
            ->join('course_assessments', 'course_assessments.course_assessments_id', '=', 'students_continous_assessments.course_assessment_id') 
            ->join('course_assessment_scores', 'course_assessment_scores.course_assessment_id', '=', 'course_assessments.course_assessments_id')
            ->distinct('course_assessment_scores.course_code')              
            ->pluck('course_assessment_scores.course_code')->toArray();
    }

    private function getCoursesFromEdurole()
    {
        return EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->join('schools', 'schools.ID', '=', 'study.ParentID')
            ->select('study.ShortName as ProgrammeCode','basic-information.ID as username','courses.ID','basic-information.Firstname','schools.Description AS SchoolName', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName','courses.CourseDescription');
    }

    public function viewCoordinatorsCourses(Request $request){

        $basicInformationId = $request->basicInformationId;
        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->select('courses.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName','courses.CourseDescription')
            ->where('basic-information.ID', $basicInformationId)
            ->get();
        
        return view('admin.viewCoordinatorsCourses', compact('results'));

    }
}
