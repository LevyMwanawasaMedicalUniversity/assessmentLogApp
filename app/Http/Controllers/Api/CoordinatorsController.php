<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseAssessment;
use App\Models\EduroleStudy;
use App\Models\EduroleCourseElective;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class CoordinatorsController extends Controller
{
    /**
     * Get coordinators data
     */
    public function getCoordinatorsData(Request $request)
    {
        try {
            $schoolId = $request->query('schoolId');
            
            // Use the same query as in AdministratorController@generateCoordinatorsView
            $coursesFromEduroleQuery = $this->getCoursesFromEdurole()
                ->orderBy('schools.Name')
                ->orderBy('study.Name')
                ->orderBy('basic-information.FirstName');

            if ($schoolId) {
                $coursesFromEduroleQuery->where('study.ParentID', $schoolId);
            }

            $coursesFromEdurole = $coursesFromEduroleQuery->get();
            
            // Generate necessary data for view
            $resultsForCount = $coursesFromEduroleQuery
                ->orderBy('programmes.Year')
                ->orderBy('courses.Name')
                ->orderBy('study.Delivery')
                ->get();
                
            $coursesFromCourseElectivesQuery = EduroleCourseElective::select('course-electives.CourseID')
                ->join('courses', 'courses.ID', '=', 'course-electives.CourseID')
                ->join('program-course-link', 'program-course-link.CourseID', '=', 'courses.ID')
                ->join('student-study-link', 'student-study-link.StudentID', '=', 'course-electives.StudentID')
                ->join('study', 'study.ID', '=', 'student-study-link.StudyID')
                ->where('course-electives.Year', 2024)
                ->where('course-electives.Approved', 1);

            $totalCoursesCoordinated = $coursesFromEdurole->unique(function ($item) {
                return $item['ID'] . '-' . $item['Delivery'] . '-' . $item['StudyID'];
            })->count();
            
            $counts = $coursesFromEdurole->countBy('StudyID');
            $results = $coursesFromEdurole->unique('basicInformationId', 'Name');
            
            // Process the results to include last login information
            $processedResults = $results->map(function($result) use ($counts) {
                $user = User::where('basic_information_id', $result->basicInformationId)->first();
                
                // Get courses with CA for this coordinator
                $getCourdinatoresCourses = EduroleStudy::where('ProgrammesAvailable', $result->basicInformationId)
                    ->pluck('ID')
                    ->toArray();

                $coursesWithCa = CourseAssessment::whereIn('study_id', $getCourdinatoresCourses)
                    ->select('course_id', 'delivery_mode')
                    ->groupBy('course_id', 'delivery_mode')
                    ->get()
                    ->count();
                
                // Get number of courses
                $numberOfCourses = 0;
                if(($result->StudyID == 163) || ($result->StudyID == 165) || ($result->StudyID == 166) || 
                   ($result->StudyID == 167) || ($result->StudyID == 168)) {
                    $numberOfCourses = $counts[$result->StudyID] ?? 0;
                } else {
                    $numberOfCourses = $counts[$result->StudyID] ?? 0;
                }
                
                return [
                    'id' => $result->basicInformationId,
                    'encrypted_id' => Crypt::encrypt($result->basicInformationId), // Add encrypted ID for routes
                    'firstname' => $result->Firstname,
                    'surname' => $result->Surname,
                    'name' => $result->Name,
                    'school' => $result->School,
                    'studyId' => $result->StudyID,
                    'last_login' => $user && $user->last_login_at ? $user->last_login_at : 'NEVER',
                    'numberOfCourses' => $numberOfCourses,
                    'coursesWithCa' => $coursesWithCa
                ];
            });

            return response()->json([
                'status' => 'success',
                'coordinators' => $processedResults->values(), // Convert to indexed array
                'counts' => $counts,
                'totalCoursesCoordinated' => $totalCoursesCoordinated,
                'schoolId' => $schoolId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
