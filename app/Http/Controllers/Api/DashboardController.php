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
                'studentCount' => $uniqueStudentIds->count()
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
            $controller = new PagesController();
            $coursesFromEdurole = $controller->getCoursesFromEdurole()->get();

            // Count unique courses based on ID, Delivery, and StudyID
            $totalCoursesCoordinated = $coursesFromEdurole->unique(function ($item) {
                return $item['ID'] . '-' . $item['Delivery'] . '-' . $item['StudyID'];
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
            // Use the exact same method from PagesController
            $controller = new PagesController();
            $coursesFromLMMAX = $controller->getCoursesFromLMMAX();
            
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
            // Same query as was used in the original dashboard
            $schools = EduroleSchool::where('ID', '>=', 4)->get();
            
            $deans = [];
            
            foreach ($schools as $school) {
                $dean = EduroleBasicInformation::find($school->DeanID);
                
                if ($dean) {
                    $deans[] = [
                        'school' => $school->Name,
                        'dean' => $dean->Firstname . ' ' . $dean->Surname,
                        'email' => $dean->PrivateEmail,
                        'parentId' => $school->ID
                    ];
                }
            }

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
            $controller = new PagesController();
            $coursesFromEdurole = $controller->getCoursesFromEdurole()
                ->join('users', 'users.basic_information_id', '=', 'basic-information.ID')
                ->addSelect('users.email as username')
                ->get();
            
            // Get total number of unique coordinators
            $coordinatorsCount = $coursesFromEdurole->unique('username')->count();
            
            // Aggregate the number of unique usernames per SchoolName
            $userCountsPerSchool = $coursesFromEdurole->groupBy('SchoolName')->map(function ($group) {
                return $group->unique('username')->count();
            });

            return response()->json([
                'status' => 'success',
                'coordinatorsCount' => $coordinatorsCount,
                'schoolNames' => $userCountsPerSchool->keys()->toArray(),
                'userCounts' => $userCountsPerSchool->values()->toArray()
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
            $controller = new PagesController();
            $coursesFromEdurole = $controller->getCoursesFromEdurole()->get();
            
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
                            return $item['ID'] . '-' . $item['Delivery'] . '-' . $item['StudyID'];
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
            $controller = new PagesController();
            
            // Get courses from Edurole
            $coursesFromEdurole = $controller->getCoursesFromEdurole()->get();
            
            // Get courses from LMMAX
            $coursesFromLMMAX = $controller->getCoursesFromLMMAX();
            
            // Filter courses with CA
            $coursesWithCA = collect($coursesFromEdurole)->filter(function ($item) use ($coursesFromLMMAX) {
                foreach ($coursesFromLMMAX as $course) {
                    if ($item->CourseName == $course['course_code'] && $item->Delivery == $course['delivery_mode'] && $item->ProgrammesAvailable != 1 && $item->StudyID == $course['study_id']) {
                        return true;
                    }
                }
                return false;
            });
            
            // Get results for count
            $resultsForCount = $controller->getCoursesFromEdurole()
                ->orderBy('programmes.Year')
                ->orderBy('courses.Name')
                ->orderBy('study.Delivery')
                ->get();
            
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
                    'assessment_count' => $coursesWithCACount,
                    'total_courses' => $totalCoursesCount
                ];
            }
            
            // Sort by assessment count in descending order and take top 10
            $programmeData = collect($programmeData)
                ->sortByDesc('assessment_count')
                ->take(10)
                ->values()
                ->toArray();

            return response()->json([
                'status' => 'success',
                'courseWithCaPerProgramme' => $programmeData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
