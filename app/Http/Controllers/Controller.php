<?php

namespace App\Http\Controllers;

use App\Models\EduroleStudy;
use App\Models\StudentsContinousAssessment;

abstract class Controller
{
    //
    public function getCoursesFromLMMAX()
    {
        return StudentsContinousAssessment::select('course_assessment_scores.course_code')
            ->join('course_assessments', 'course_assessments.course_assessments_id', '=', 'students_continous_assessments.course_assessment_id') 
            ->join('course_assessment_scores', 'course_assessment_scores.course_assessment_id', '=', 'course_assessments.course_assessments_id')
            ->distinct('course_assessment_scores.course_code')              
            ->pluck('course_assessment_scores.course_code')->toArray();
    }

    public function getCoursesFromEdurole()
    {
        return EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->join('schools', 'schools.ID', '=', 'study.ParentID')
            ->select('study.ShortName as ProgrammeCode','basic-information.ID as username','courses.ID','basic-information.Firstname','schools.Description AS SchoolName', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName','courses.CourseDescription');
    }
}
