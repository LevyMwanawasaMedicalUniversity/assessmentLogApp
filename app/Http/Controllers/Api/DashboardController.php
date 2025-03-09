<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SisReportsStudent;
use App\Models\CourseAssessment;
use App\Models\AuditTrail;
use App\Models\Announcement;
use App\Models\School;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Get count of students with CA
     */
    public function getStudentsWithCA()
    {
        $uniqueStudentIds = SisReportsStudent::distinct()
            ->where('status', 6)
            ->pluck('student_number');

        return response()->json([
            'count' => $uniqueStudentIds->count(),
        ]);
    }

    /**
     * Get count of courses from Edurole
     */
    public function getCoursesFromEdurole()
    {
        // This is a placeholder - you'll need to replace with your actual query
        // that was causing performance issues
        $coursesFromEdurole = DB::table('edurole_course')
            ->select('ID', 'Delivery', 'StudyID')
            ->get();

        $totalCoursesCoordinated = $coursesFromEdurole->unique(function ($item) {
            return $item->ID . '-' . $item->Delivery . '-' . $item->StudyID;
        })->count();

        return response()->json([
            'count' => $totalCoursesCoordinated,
        ]);
    }

    /**
     * Get count of courses from LM-MAX
     */
    public function getCoursesFromLMMAX()
    {
        $totalCa = CourseAssessment::distinct(['course_id', 'delivery_mode', 'study_id'])
            ->count();

        return response()->json([
            'count' => $totalCa,
            'hasRegistrarPermission' => Auth::user()->hasPermissionTo('Registrar'),
        ]);
    }

    /**
     * Get data for programme chart
     */
    public function getProgrammeChartData()
    {
        // This is a placeholder - you'll need to replace with your actual query
        // that was causing performance issues
        $coursesWithCA = collect([]);
        $coursesFromEdurole = DB::table('edurole_course')
            ->select('ID', 'Delivery', 'StudyID', 'ProgrammeCode')
            ->get();

        // Extract unique ProgrammeCodes
        $programmeCodes = $coursesFromEdurole->pluck('ProgrammeCode')->unique()->values();

        // Initialize arrays to hold counts for each ProgrammeCode
        $coursesWithCAProgrammeCountsArray = [];
        $coursesFromEduroleProgrammeCountsArray = [];

        foreach ($programmeCodes as $code) {
            // Count courses with CA for the current ProgrammeCode
            // This is a placeholder - replace with your actual query
            $coursesWithCAProgrammeCountsArray[] = rand(5, 30); // Placeholder random data

            // Count courses from Edurole for the current ProgrammeCode
            $coursesFromEduroleProgrammeCountsArray[] = $coursesFromEdurole->where('ProgrammeCode', $code)->count();
        }

        return response()->json([
            'programmeCodes' => $programmeCodes,
            'coursesWithCA' => $coursesWithCAProgrammeCountsArray,
            'coursesFromEdurole' => $coursesFromEduroleProgrammeCountsArray,
        ]);
    }

    /**
     * Get data for school chart
     */
    public function getSchoolChartData()
    {
        // Get all schools
        $schools = School::all();
        
        // Initialize arrays to store data for the chart
        $schoolNames = [];
        $totalCoursesArray = [];
        $coursesWithCAArray = [];
        
        foreach ($schools as $school) {
            // Add school name to the array
            $schoolNames[] = $school->short_name;
            
            // Get all courses for this school
            $totalCourses = Course::where('school_id', $school->id)->count();
            $totalCoursesArray[] = $totalCourses;
            
            // Get courses with CA for this school
            $coursesWithCA = CourseAssessment::whereHas('course', function ($query) use ($school) {
                $query->where('school_id', $school->id);
            })->distinct('course_id')->count('course_id');
            
            $coursesWithCAArray[] = $coursesWithCA;
        }

        return response()->json([
            'schoolNames' => $schoolNames,
            'totalCourses' => $totalCoursesArray,
            'coursesWithCA' => $coursesWithCAArray,
        ]);
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities()
    {
        $recentActivities = AuditTrail::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($activity) {
                return [
                    'time_ago' => $activity->created_at->diffForHumans(),
                    'event' => $activity->event,
                    'user_name' => $activity->user->name,
                    'auditable_type' => $activity->auditable_type,
                ];
            });

        return response()->json([
            'activities' => $recentActivities,
        ]);
    }

    /**
     * Get announcements
     */
    public function getAnnouncements()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($announcement) {
                return [
                    'title' => $announcement->title,
                    'content' => \Illuminate\Support\Str::limit($announcement->content, 100),
                    'date' => $announcement->created_at->format('M d, Y'),
                ];
            });

        return response()->json([
            'announcements' => $announcements,
        ]);
    }
}
