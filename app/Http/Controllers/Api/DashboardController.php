<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PagesController;
use App\Models\CourseAssessment;
use App\Models\EduroleBasicInformation;
use App\Models\EduroleSchool;
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
            $caPerSchool = DB::table('sis_reports')
                ->join('basic-information', 'basic-information.ID', '=', 'sis_reports.instructor_id')
                ->join('schools', 'schools.ID', '=', 'sis_reports.school_id')
                ->whereNotNull('sis_reports.ca_test_marks')
                ->where('sis_reports.ca_test_marks', '<>', '')
                ->select('schools.Name as school_name', DB::raw('COUNT(*) as assessment_count'))
                ->groupBy('schools.Name')
                ->get();

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
            $courseWithCaPerProgramme = DB::table('sis_reports')
                ->join('study', 'study.ID', '=', 'sis_reports.study_id')
                ->whereNotNull('sis_reports.ca_test_marks')
                ->where('sis_reports.ca_test_marks', '<>', '')
                ->select('study.Name as programme_name', DB::raw('COUNT(*) as assessment_count'))
                ->groupBy('study.Name')
                ->orderBy('assessment_count', 'DESC')
                ->limit(10)
                ->get();

            return response()->json([
                'status' => 'success',
                'courseWithCaPerProgramme' => $courseWithCaPerProgramme
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
