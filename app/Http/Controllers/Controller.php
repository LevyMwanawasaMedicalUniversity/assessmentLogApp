<?php

namespace App\Http\Controllers;

use App\Models\CourseAssessment;
use App\Models\EduroleBasicInformation;
use App\Models\EduroleCourseElective;
use App\Models\EduroleStudy;
use App\Models\StudentsContinousAssessment;

abstract class Controller
{
    //
    public function getCoursesFromLMMAX()
    {
        return CourseAssessment::select('course_assessment_scores.course_code','course_assessments.study_id','course_assessments.course_id','course_assessment_scores.study_id','course_assessments.delivery_mode','course_assessments.basic_information_id')
            // ->join('course_assessments', 'course_assessments.course_assessments_id', '=', 'students_continous_assessments.course_assessment_id') 
            ->join('course_assessment_scores', 'course_assessment_scores.course_assessment_id', '=', 'course_assessments.course_assessments_id')
            ->distinct()
            ->get()
            ->map(function ($item) {
                return [
                    'course_code' => $item->course_code,
                    'delivery_mode' => $item->delivery_mode,
                    'basic_information_id' => $item->basic_information_id,
                    'study_id' => $item->study_id
                ];
            })->toArray();
    }

    public function getNSAttachedCourses(){
        $naturalScienceCourses = EduroleStudy::join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->where('study.Name', '=', 'Natural Science')
            ->whereIn('courses.Name', ['MAT101', 'PHY101', 'CHM101', 'BIO101'])
            ->select('study.ID')
            ->pluck('study.ID')
            ->toArray();
        return $naturalScienceCourses;
    }

    public function queryCourseFromEdurole(){
        $coursesFromCourseElectives = EduroleCourseElective::select('CourseID')
            ->where('Year', 2024)
            ->where('Approved', 1)
            ->distinct()
            ->pluck('CourseID')
            ->toArray();

        return EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->join('schools', 'schools.ID', '=', 'study.ParentID')
            // ->join('course-electives', 'course-electives.CourseID', '=', 'courses.ID')
            ->select('programmes.Year as YearOfStudy','study.ShortName as ProgrammeCode', 'basic-information.ID as username','basic-information.ID as basicInformationId', 'courses.ID','courses.ID as CourseID', 'basic-information.Firstname', 'schools.Description AS SchoolName','basic-information.PrivateEmail', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName', 'courses.CourseDescription','study.Delivery','study.ParentID','study.ID as StudyID')
            ->where('study.ProgrammesAvailable', '!=', 1)
            ->whereIn('courses.ID', $coursesFromCourseElectives);
            // ->where('course-electives.Year', 2024);
    } 

    public function getCoursesFromEdurole()
    {        
        $naturalScienceCourses = $this->getNSAttachedCourses();
        return $this->queryCourseFromEdurole()
            ->where(function($query) use ($naturalScienceCourses) {
                $query->whereNotIn('courses.Name', ['MAT101', 'PHY101', 'CHM101', 'BIO101'])
                    ->orWhereIn('study.ID', $naturalScienceCourses);
            });
    }

    public function getStudentInformation($studentId){
        $resultsFromBasicInformation= EduroleBasicInformation::join('student-study-link', 'student-study-link.StudentID', '=', 'basic-information.ID')
            ->join('study', 'study.ID', '=', 'student-study-link.StudyID')            
            ->join('schools', 'schools.ID', '=', 'study.ParentID')
            ->select('basic-information.ID', 'basic-information.FirstName','basic-information.StudyType', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.Name as Programme', 'schools.Name as School')
            ->where('basic-information.ID', $studentId);
        return $resultsFromBasicInformation;
    }
}
