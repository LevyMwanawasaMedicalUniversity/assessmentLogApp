<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PagesController;
use App\Models\CourseAssessment;
use App\Models\EduroleBasicInformation;
use App\Models\EduroleCourseElective;
use App\Models\EduroleSchool;
use App\Models\EduroleStudy;
use App\Models\SisReportsStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get students with CA count
     */
    public function getStudentsWithCa()
    {
        try {
            // Use the exact same query as before
            $uniqueStudentIds = SisReportsStudent::distinct()
                ->where('status', 6)
                ->pluck('student_number');
            
            return response()->json([
                'status' => 'success',
                'totalStudentsWithCa' => $uniqueStudentIds->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get courses from Edurole
     */
    public function getCoursesFromEdurole()
    {
        try {
            // Use the exact query from the parent Controller class
            $coursesFromEdurole = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
                ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
                ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
                ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
                ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
                ->join('schools', 'schools.ID', '=', 'study.ParentID')
                ->select(
                    'courses.ID',
                    'basic-information.Firstname', 
                    'basic-information.Surname', 
                    'basic-information.PrivateEmail', 
                    'study.ProgrammesAvailable', 
                    'study.Name', 
                    'courses.Name as CourseName',
                    'courses.CourseDescription',
                    'study.Delivery',
                    'study.ID as StudyID',
                    'study.ShortName as ProgrammeCode',
                    'schools.Name as SchoolName',
                    'schools.Description as SchoolDescription'
                )
                ->get();

            // Count unique courses based on ID, Delivery, and StudyID
            $totalCoursesCoordinated = $coursesFromEdurole->unique(function ($item) {
                return $item->ID . '-' . $item->Delivery . '-' . $item->StudyID;
            })->count();

            return response()->json([
                'status' => 'success',
                'totalCoursesCoordinated' => $totalCoursesCoordinated
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get courses from LMMAX
     */
    public function getCoursesFromLmmax()
    {
        try {
            // Use the exact same method from Controller class
            $coursesFromLMMAX = CourseAssessment::select('course_assessment_scores.course_code','course_assessments.study_id','course_assessments.course_id','course_assessment_scores.study_id','course_assessments.delivery_mode','course_assessments.basic_information_id')
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
            
            return response()->json([
                'status' => 'success',
                'totalCoursesWithCa' => count($coursesFromLMMAX)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get deans per school data
     */
    public function getDeansPerSchool()
    {
        try {
            // Using the correct join query as provided
            // $deansData = DB::table('schools as s')
            //     ->join('basic-information as bi', 'bi.ID', '=', 's.Dean')
            //     ->select('bi.FirstName', 'bi.Surname', 's.Description', 's.ID', 'bi.PrivateEmail')
            //     ->get();
            
            $deans = EduroleBasicInformation::join('schools', 'schools.Dean', '=', 'basic-information.ID')
                    ->select('basic-information.FirstName', 'basic-information.Surname', 'schools.Description', 'schools.ID', 'basic-information.PrivateEmail')
                    ->get();           
            
            return response()->json([
                'status' => 'success',
                'deans' => $deans
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get coordinators traffic data
     */
    public function getCoordinatorsTraffic()
    {
        try {
            // Query to get coordinators per school
            $coordinatorsPerSchool = EduroleBasicInformation::join('study', 'study.ProgrammesAvailable', '=', 'basic-information.ID')
                ->join('schools', 'schools.ID', '=', 'study.ParentID')
                ->select(
                    'schools.Name as school_name',
                    'schools.Description as school_description',
                    DB::raw('COUNT(DISTINCT `basic-information`.ID) as coordinator_count')
                )
                ->groupBy('schools.Name', 'schools.Description')
                ->orderBy('coordinator_count', 'desc')
                ->get();

            // Get total number of unique coordinators
            $totalCoordinators = $coordinatorsPerSchool->sum('coordinator_count');

            return response()->json([
                'status' => 'success',
                'coordinatorsPerSchool' => $coordinatorsPerSchool,
                'totalCoordinators' => $totalCoordinators
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get CA per school data
     */
    public function getCaPerSchool()
    {
        try {
            // Use the same schools list as in the original implementation
            $schools = ['SOHS', 'SOPHES', 'SOMCS', 'DRGS', 'SON', 'IBBS'];
            
            // Get courses from Edurole
            $coursesFromEdurole = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
                ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
                ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
                ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
                ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
                ->join('schools', 'schools.ID', '=', 'study.ParentID')
                ->select(
                    'courses.ID',
                    'basic-information.Firstname', 
                    'basic-information.Surname', 
                    'basic-information.PrivateEmail', 
                    'study.ProgrammesAvailable', 
                    'study.Name', 
                    'courses.Name as CourseName',
                    'courses.CourseDescription',
                    'study.Delivery',
                    'study.ID as StudyID',
                    'study.ShortName as ProgrammeCode',
                    'schools.Name as SchoolName',
                    'schools.Description as SchoolDescription'
                )
                ->get();
            
            $caPerSchool = [];
            
            foreach ($schools as $school) {
                $getSchools = EduroleSchool::where('Description', $school)->first();
                if ($getSchools) {
                    $schoolId = $getSchools->ID;
                    $schoolProgrammes = EduroleStudy::where('ParentID', $schoolId)->pluck('ID')->toArray();
                    
                    // Count courses with CA
                    $coursesWithCACount = CourseAssessment::whereIn('study_id', $schoolProgrammes)
                        ->select('course_id', 'delivery_mode')
                        ->groupBy('course_id', 'delivery_mode')
                        ->get()
                        ->count();
                    
                    // Count total courses
                    $totalCourses = $coursesFromEdurole->where('SchoolName', $school)
                        ->unique(function ($item) {
                            return $item->ID . '-' . $item->Delivery . '-' . $item->StudyID;
                        })->count();
                    
                    $caPerSchool[] = [
                        'school_name' => $school,
                        'courses_with_ca' => $coursesWithCACount,
                        'total_courses' => $totalCourses
                    ];
                }
            }

            return response()->json([
                'status' => 'success',
                'caPerSchool' => $caPerSchool
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get course with CA per programme data
     */
    public function getCourseWithCaPerProgramme()
    {
        try {
            // Get courses from LMMAX
            $coursesFromLMMAX = CourseAssessment::select('course_assessment_scores.course_code','course_assessments.study_id','course_assessments.course_id','course_assessment_scores.study_id','course_assessments.delivery_mode','course_assessments.basic_information_id')
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
            
            // Get courses from Edurole
            $coursesFromEdurole = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
                ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
                ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
                ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
                ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
                ->join('schools', 'schools.ID', '=', 'study.ParentID')
                ->select(
                    'courses.ID',
                    'basic-information.Firstname', 
                    'basic-information.Surname', 
                    'basic-information.PrivateEmail', 
                    'study.ProgrammesAvailable', 
                    'study.Name', 
                    'courses.Name as CourseName',
                    'courses.CourseDescription',
                    'study.Delivery',
                    'study.ID as StudyID',
                    'study.ShortName as ProgrammeCode',
                    'schools.Name as SchoolName',
                    'schools.Description as SchoolDescription'
                );
            
            // Get results for count
            $resultsForCount = $coursesFromEdurole
                ->orderBy('programmes.Year')
                ->orderBy('courses.Name')
                ->orderBy('study.Delivery')
                ->get();
            
            $coursesFromEdurole = $coursesFromEdurole->get();
            
            // Filter courses with CA
            $coursesWithCA = collect($coursesFromEdurole)->filter(function ($item) use ($coursesFromLMMAX) {
                foreach ($coursesFromLMMAX as $course) {
                    if ($item->CourseName == $course['course_code'] && $item->Delivery == $course['delivery_mode'] && $item->ProgrammesAvailable != 1 && $item->StudyID == $course['study_id']) {
                        return true;
                    }
                }
                return false;
            });
            
            // Extract unique ProgrammeCodes from coursesFromEdurole
            $programmeCodes = $coursesFromEdurole->pluck('ProgrammeCode')->unique()->values()->toArray();
            
            $programmeData = [];
            
            foreach ($programmeCodes as $code) {
                // Count courses with CA for the current ProgrammeCode
                $coursesWithCACount = $coursesWithCA->where('ProgrammeCode', $code)->count();
                
                // Get total courses count
                $totalCoursesCount = 0;
                if (in_array($code, ['BBS', 'NS', 'TP1', 'TP2', 'TP3', 'TP4', 'TP5', 'TP6', 'TP7'])) {
                    $totalCoursesCount = $coursesFromEdurole->where('ProgrammeCode', $code)->count();
                } else {
                    // Clone the query for each iteration to avoid modifying the original query
                    $coursesFromCourseElectives = EduroleCourseElective::select('course-electives.CourseID')
                        ->join('courses', 'courses.ID', '=', 'course-electives.CourseID')
                        ->join('program-course-link', 'program-course-link.CourseID', '=', 'courses.ID')
                        ->join('student-study-link', 'student-study-link.StudentID', '=', 'course-electives.StudentID')
                        ->join('study', 'study.ID', '=', 'student-study-link.StudyID')
                        ->where('course-electives.Year', 2024)
                        ->where('course-electives.Approved', 1)
                        ->where('study.ShortName', $code)
                        ->distinct()
                        ->pluck('course-electives.CourseID')
                        ->toArray();
                        
                    $totalCoursesCount = $resultsForCount->where('ProgrammeCode', $code)
                        ->whereIn('ID', $coursesFromCourseElectives)
                        ->count();
                }
                
                $programmeData[] = [
                    'programme_name' => $code,
                    'courses_with_ca' => $coursesWithCACount,
                    'total_courses' => $totalCoursesCount
                ];
            }
            
            // Sort by assessment count in descending order and take top 10
            $programmeData = collect($programmeData)
                ->sortBy('programme_name')
                ->values()
                ->toArray();

            return response()->json([
                'status' => 'success',
                'coursesWithCaPerProgramme' => $programmeData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
