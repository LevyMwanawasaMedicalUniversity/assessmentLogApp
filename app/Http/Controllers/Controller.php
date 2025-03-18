<?php

namespace App\Http\Controllers;

use App\Models\CourseAssessment;
use App\Models\CourseComponentAllocation;
use App\Models\EduroleBasicInformation;
use App\Models\EduroleCourseElective;
use App\Models\EduroleStudy;
use App\Models\Setting;
use App\Models\StudentsContinousAssessment;

abstract class Controller
{
    protected $academicYear;

    public function __construct()
    {
        $this->academicYear = Setting::getCurrentAcademicYear();
    }
    //
    public function getCoursesFromLMMAX(){
        return CourseAssessment::select('course_assessment_scores.course_code','course_assessments.study_id','course_assessments.course_id','course_assessment_scores.study_id','course_assessments.delivery_mode','course_assessments.basic_information_id')
            // ->join('course_assessments', 'course_assessments.course_assessments_id', '=', 'students_continous_assessments.course_assessment_id') 
            ->join('course_assessment_scores', 'course_assessment_scores.course_assessment_id', '=', 'course_assessments.course_assessments_id')
            ->where('course_assessments.academic_year', $this->academicYear)
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
            ->whereIn('courses.Name', ['MAT101', 'PHY101', 'CHM101', 'BIO101','OGC201'])
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
            ->whereIn('courses.Name', ['BAB201', 'CAG201', 'CVS301', 'GIT301','GRA201','IHD201','MCT201','NER301','PEB201','REN301','RES301','BCH2015','BCH2060','CBP2020','HAN2040','HAN2050','PGY2040','PHR3030','PHR3060','PTH2020','PTH2040','PTH2070'])
            ->select('study.ID')
            ->pluck('study.ID')
            ->toArray();
        return $naturalScienceCourses;
    }

    public function getTempProgramme1(){
        $naturalScienceCourses = EduroleStudy::join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->where('study.Name', '=', 'Temp Programme 1')
            ->whereIn('courses.Name', ['HOA2210','HOP2210','PHO2210'])
            ->select('study.ID')
            ->pluck('study.ID')
            ->toArray();
        return $naturalScienceCourses;
    }

    public function getTempProgramme2(){
        $naturalScienceCourses = EduroleStudy::join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->where('study.Name', '=', 'Temp Programme 2')
            ->whereIn('courses.Name', ['ANP101','HAP201'])
            ->select('study.ID')
            ->pluck('study.ID')
            ->toArray();
        return $naturalScienceCourses;
    }

    public function getTempProgramme3(){
        $naturalScienceCourses = EduroleStudy::join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->where('study.Name', '=', 'Temp Programme 3')
            ->whereIn('courses.Name', ['BMM301','IPM3110'])
            ->select('study.ID')
            ->pluck('study.ID')
            ->toArray();
        return $naturalScienceCourses;
    }

    public function getTempProgramme4(){
        $naturalScienceCourses = EduroleStudy::join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->where('study.Name', '=', 'Temp Programme 4')
            ->whereIn('courses.Name', ['BBC302','BEM201','BGB301','BHT201','BHT301','BIM201','BIM301','BME302','BPR301','BSE101','MPP201'])
            ->select('study.ID')
            ->pluck('study.ID')
            ->toArray();
        return $naturalScienceCourses;
    }

    public function getTempProgramme5(){
        $naturalScienceCourses = EduroleStudy::join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->where('study.Name', '=', 'Temp Programme 5')
            ->whereIn('courses.Name', ['BMS101'])
            ->select('study.ID')
            ->pluck('study.ID')
            ->toArray();
        return $naturalScienceCourses;
    }

    public function getTempProgramme6(){
        $naturalScienceCourses = EduroleStudy::join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->where('study.Name', '=', 'Temp Programme 6')
            ->whereIn('courses.Name', ['NBC201'])
            ->select('study.ID')
            ->pluck('study.ID')
            ->toArray();
        return $naturalScienceCourses;
    }

    public function getTempProgramme7(){
        $naturalScienceCourses = EduroleStudy::join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->where('study.Name', '=', 'Temp Programme 7')
            ->whereIn('courses.Name', ['MBP201'])
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
            ->where('course-electives.Year', $this->academicYear)
            ->where('course-electives.Approved', 1)
            ->distinct()
            ->pluck('course-electives.CourseID')
            ->toArray();

        // return EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
        //     ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
        //     ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
        //     ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
        //     ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
        //     ->join('schools', 'schools.ID', '=', 'study.ParentID')
        //     // ->join('course-electives', 'course-electives.CourseID', '=', 'courses.ID')
        //     ->select('programmes.Year as YearOfStudy','study.ShortName as ProgrammeCode', 'basic-information.ID as username','basic-information.ID as basicInformationId', 'courses.ID','courses.ID as CourseID', 'basic-information.Firstname', 'schools.Description AS SchoolName','basic-information.PrivateEmail', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName', 'courses.CourseDescription','study.Delivery','study.ParentID','study.ID as StudyID','schools.Name as School','study.ProgrammesAvailable')
        //     ->where('study.ProgrammesAvailable', '!=', 1)
        //     ->whereIn('courses.ID', $coursesFromCourseElectives);
        //     // ->where('course-electives.Year', 2024);

        return EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->join('schools', 'schools.ID', '=', 'study.ParentID')
            // ->join('course-electives', 'course-electives.CourseID', '=', 'courses.ID')
            ->distinct()
            ->select('programmes.Year as YearOfStudy', 'study.ShortName as ProgrammeCode', 
                    'basic-information.ID as username', 'basic-information.ID as basicInformationId', 
                    'courses.ID', 'courses.ID as CourseID', 'basic-information.Firstname', 
                    'schools.Description AS SchoolName', 'basic-information.PrivateEmail', 
                    'basic-information.Surname', 'study.ProgrammesAvailable', 'study.Name', 
                    'courses.Name as CourseName', 'courses.CourseDescription', 'study.Delivery', 
                    'study.ParentID', 'study.ID as StudyID', 'schools.Name as School', 'study.ProgrammesAvailable')
            ->where('study.ProgrammesAvailable', '!=', 1)
            ->whereIn('courses.ID', $coursesFromCourseElectives);

    } 

    public function getCoursesFromEdurole()
    {        
        $naturalScienceCourses = $this->getNSAttachedCourses();
        $getBasicSciencesCourses = $this->getBasicSciencesCourses();
        // $tempProgramme1 = $this->getTempProgramme1();
        // $tempProgramme2 = $this->getTempProgramme2();
        // $tempProgramme3 = $this->getTempProgramme3();
        // $tempProgramme4 = $this->getTempProgramme4();
        // $tempProgramme5 = $this->getTempProgramme5();
        // $tempProgramme6 = $this->getTempProgramme6();

        // $tempProgramme7 = $this->getTempProgramme7();   
        return $this->queryCourseFromEdurole()
        ->where(function($query) use ($naturalScienceCourses) {
            $query->whereNotIn('courses.Name', ['MAT101', 'PHY101', 'CHM101', 'BIO101'])
                ->orWhereIn('study.ID', $naturalScienceCourses);
        })
        ->where(function($query) use ($getBasicSciencesCourses) {
            $query->whereNotIn('courses.Name', ['BAB201', 'CAG201', 'CVS301', 'GIT301','GRA201','IHD201','MCT201','NER301','PEB201','REN301','RES301','BCH2015','BCH2060','CBP2020','HAN2040','HAN2050','PGY2040','PHR3030','PHR3060','PTH2020','PTH2040','PTH2070'])
                ->orWhereIn('study.ID', $getBasicSciencesCourses);
        })
        // ->where(function($query) use ($tempProgramme1) {
        //     $query->whereNotIn('courses.Name', ['HOA2210','HOP2210','PHO2210'])
        //         ->orWhereIn('study.ID', $tempProgramme1);
        // })
        // ->where(function($query) use ($tempProgramme2) {
        //     $query->whereNotIn('courses.Name', ['ANP101','HAP201'])
        //         ->orWhereIn('study.ID', $tempProgramme2);
        // })
        // ->where(function($query) use ($tempProgramme3) {
        //     $query->whereNotIn('courses.Name', ['BMM301','IPM3110'])
        //         ->orWhereIn('study.ID', $tempProgramme3);
        // })
        // ->where(function($query) use ($tempProgramme4) {
        //     $query->whereNotIn('courses.Name', ['BBC302','BEM201','BGB301','BHT201','BHT301','BIM201','BIM301','BME302','BPR301','BSE101','MPP201'])
        //         ->orWhereIn('study.ID', $tempProgramme4);
        // })
        // ->where(function($query) use ($tempProgramme5) {
        //     $query->whereNotIn('courses.Name', ['BMS101'])
        //         ->orWhereIn('study.ID', $tempProgramme5);
        // })
        // ->where(function($query) use ($tempProgramme6) {
        //     $query->whereNotIn('courses.Name', ['NBC201'])
        //         ->orWhereIn('study.ID', $tempProgramme6);
        // })
        // ->where(function($query) use ($tempProgramme7) { 
        //     $query->whereNotIn('courses.Name', ['MBP201'])
        //         ->orWhereIn('study.ID', $tempProgramme7);
        // })
        ->where(function($query) {
            $query->where('programmes.ProgramName', 'NOT LIKE', '%BSCBMS-DE-2023-Y2%')
                ->where('programmes.ProgramName', 'NOT LIKE', '%BSCPHNUR-DE-2023-Y2%')
                ->where('programmes.ProgramName', 'NOT LIKE', '%BSCMHN-DE-2023-Y2%')
                ->where('programmes.ProgramName', 'NOT LIKE', '%BAGC-FT-2023-Y1%')
                ->where('programmes.ProgramName', 'NOT LIKE', '%BAGC-FT-2023-Y2%')
                ->where('programmes.ProgramName', 'NOT LIKE', '%BAGC-FT-2023-Y3%')
                ->where('programmes.ProgramName', 'NOT LIKE', '%BAGC-FT-2023-Y4%')
                ->where('programmes.ProgramName', 'NOT LIKE', '%BAGC-FT-2019-Y1%')
                ->where('programmes.ProgramName', 'NOT LIKE', '%BAGC-FT-2019-Y2%')
                ->where('programmes.ProgramName', 'NOT LIKE', '%BAGC-FT-2019-Y3%')
                ->where('programmes.ProgramName', 'NOT LIKE', '%BAGC-FT-2019-Y4%')
                ->where('programmes.ProgramName', 'NOT LIKE', '%DipMID-FT-2019-Y3%')
                ->where('programmes.ProgramName', 'NOT LIKE', '%DIPMID-FT-2023-Y3%');
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

    public function arrayOfValidProgrammes($studyId){

        return [$studyId, 163, 165,166, 167,168,169,170,171,172,173,174];
    }
}
