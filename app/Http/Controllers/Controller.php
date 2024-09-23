<?php

namespace App\Http\Controllers;

use App\Models\CourseAssessment;
use App\Models\CourseComponentAllocation;
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

    public function getAllocatedCourses($courseId,$basicInformationId,$delivery,$studyId, $academicYear){
        $results = CourseComponentAllocation::join('course_components', 'course_components.course_components_id', '=', 'course_component_allocations.course_component_id')
            ->where('course_component_allocations.course_id', $courseId)
            // ->where('course_component_allocations.user_id', $basicInformationId)
            ->where('course_component_allocations.delivery_mode', $delivery)
            ->where('course_component_allocations.study_id', $studyId)
            ->where('course_component_allocations.academic_year', $academicYear)
            ->orderBy('course_components.component_name')          
            ->get();
        return $results;
    }

    public function getBasicSciencesCourses(){
        $naturalScienceCourses = EduroleStudy::join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->where('study.Name', '=', 'Basic Sciences')
            ->whereIn('courses.Name', ['BAB201', 'CAG201', 'CVS301', 'GIT301','GRA201','IHD201','MCT201','NER301','PEB201','REN301','RES301'])
            ->select('study.ID')
            ->pluck('study.ID')
            ->toArray();
        return $naturalScienceCourses;
    }

    public function queryCourseFromEdurole(){
        $coursesFromCourseElectives = EduroleCourseElective::select('course-electives.CourseID')
            ->join('courses', 'courses.ID','=','course-electives.CourseID')
            ->join('program-course-link', 'program-course-link.CourseID','=','courses.ID')
            ->join('student-study-link','student-study-link.StudentID','=','course-electives.StudentID')
            ->join('study','study.ID','=','student-study-link.StudyID')
            ->where('course-electives.Year', 2024)
            ->where('course-electives.Approved', 1)
            ->distinct()
            ->pluck('course-electives.CourseID')
            ->toArray();

        return EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->join('schools', 'schools.ID', '=', 'study.ParentID')
            // ->join('course-electives', 'course-electives.CourseID', '=', 'courses.ID')
            ->select('programmes.Year as YearOfStudy','study.ShortName as ProgrammeCode', 'basic-information.ID as username','basic-information.ID as basicInformationId', 'courses.ID','courses.ID as CourseID', 'basic-information.Firstname', 'schools.Description AS SchoolName','basic-information.PrivateEmail', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName', 'courses.CourseDescription','study.Delivery','study.ParentID','study.ID as StudyID','schools.Name as School','study.ProgrammesAvailable')
            ->where('study.ProgrammesAvailable', '!=', 1)
            ->whereIn('courses.ID', $coursesFromCourseElectives);
            // ->where('course-electives.Year', 2024);
    } 

    public function getCoursesFromEdurole()
    {        
        $naturalScienceCourses = $this->getNSAttachedCourses();
        $getBasicSciencesCourses = $this->getBasicSciencesCourses();
        return $this->queryCourseFromEdurole()
        ->where(function($query) use ($naturalScienceCourses) {
            $query->whereNotIn('courses.Name', ['MAT101', 'PHY101', 'CHM101', 'BIO101'])
                ->orWhereIn('study.ID', $naturalScienceCourses);
        })
        ->where(function($query) use ($getBasicSciencesCourses) {
            $query->whereNotIn('courses.Name', ['BAB201', 'CAG201', 'CVS301', 'GIT301','GRA201','IHD201','MCT201','NER301','PEB201','REN301','RES301'])
                ->orWhereIn('study.ID', $getBasicSciencesCourses);
        })
        ->where(function($query) {
            $query->where('programmes.ProgramName', 'NOT LIKE', '%BSCBMS-DE-2023-Y2%');
                // ->where('courses.CourseDescription', 'NOT LIKE', '%Practical%')
                // ->where('courses.CourseDescription', 'NOT LIKE', '%Research%')                
                // ->where('courses.CourseDescription', 'NOT LIKE', '%Attachment%')
                // ->where('courses.CourseDescription', 'NOT LIKE', '%Clinical Practice%')
                                
                // ->where('courses.Name', 'NOT LIKE', '%OSC%')
                // ->where('courses.CourseDescription', 'NOT LIKE', '%Dissertation%');
        });
            //Attachements need to be excluded they do not have CA
            //
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
