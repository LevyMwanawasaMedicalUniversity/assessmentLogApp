<?php

namespace App\Livewire;

use App\Models\CourseAssessment;
use App\Models\EduroleBasicInformation;
use App\Models\EduroleCourseElective;
use App\Models\EduroleSchool;
use App\Models\EduroleStudy;
use App\Models\SisReportsStudent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardStats extends Component
{
    public $uniqueStudentCount;
    public $totalCoursesCoordinated;
    public $totalCa;
    public $deansData;
    public $coursesFromEdurole;
    public $coursesWithCA;
    public $schoolStats;
    public $programmeCodes;
    public $coursesWithCAProgrammeCounts;
    public $coursesFromEduroleProgrammeCounts;
    public $coursesFromCourseElectivesQuery;
    public $resultsForCount;
    public $userCountsPerSchool;
    
    protected $listeners = ['refreshData' => '$refresh'];

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $coursesFromLMMAX = $this->getCoursesFromLMMAX();
        $coursesFromEduroleQuery = $this->getCoursesFromEdurole();

        $this->resultsForCount = $coursesFromEduroleQuery
            ->orderBy('programmes.Year')
            ->orderBy('courses.Name')
            ->orderBy('study.Delivery')
            ->get();
        
        $this->coursesFromEdurole = $coursesFromEduroleQuery->get();

        // Filter results for courses with CA
        $filteredResults = $this->coursesFromEdurole->filter(function ($item) use ($coursesFromLMMAX) {
            foreach ($coursesFromLMMAX as $course) {
                if ($item->CourseName == $course['course_code'] && 
                    $item->Delivery == $course['delivery_mode'] && 
                    $item->ProgrammesAvailable != 1 && 
                    $item->StudyID == $course['study_id']) {
                    return true;
                }
            }
            return false;
        });

        $this->coursesWithCA = $filteredResults;

        $this->loadStats();
        $this->loadDeansData();
        $this->prepareCoursesElectivesQuery();
        $this->calculateProgrammeStats();
        $this->calculateSchoolStats();
        $this->calculateUserCountsPerSchool();
    }

    protected function loadStats()
    {
        $this->uniqueStudentCount = Cache::remember('unique_student_count', 3600, function () {
            return SisReportsStudent::distinct()
                ->where('status', 6)
                ->count('student_number');
        });

        $this->totalCoursesCoordinated = $this->coursesFromEdurole->unique(function ($item) {
            return $item->ID . '-' . $item->Delivery . '-' . $item->StudyID;
        })->count();

        $this->totalCa = Cache::remember('total_ca', 3600, function () {
            return CourseAssessment::distinct(['course_id', 'delivery_mode', 'study_id'])->count();
        });
    }

    protected function loadDeansData()
    {
        $deansDataGet = EduroleBasicInformation::join('access', 'access.ID', '=', 'basic-information.ID')
            ->join('roles', 'roles.ID', '=', 'access.RoleID')
            ->join('schools', 'schools.Dean', '=', 'basic-information.ID')
            ->join('study', 'study.ParentID', '=', 'schools.ID')
            ->select(
                'basic-information.FirstName',
                'basic-information.Surname',
                'basic-information.ID',
                'roles.RoleName',
                'schools.ID as ParentID',
                'study.ID as StudyID',
                'schools.Name as SchoolName',
                'study.Delivery'
            )
            ->get();
        
        $this->deansData = $deansDataGet->unique('ID');
    }

    protected function prepareCoursesElectivesQuery()
    {
        $this->coursesFromCourseElectivesQuery = EduroleCourseElective::select('course-electives.CourseID')
            ->join('courses', 'courses.ID', '=', 'course-electives.CourseID')
            ->join('program-course-link', 'program-course-link.CourseID', '=', 'courses.ID')
            ->join('student-study-link', 'student-study-link.StudentID', '=', 'course-electives.StudentID')
            ->join('study', 'study.ID', '=', 'student-study-link.StudyID')
            ->where('course-electives.Year', 2024)
            ->where('course-electives.Approved', 1);
    }

    protected function calculateProgrammeStats()
    {
        $this->programmeCodes = collect($this->coursesFromEdurole)->pluck('ProgrammeCode')->unique()->values();
        
        $this->coursesWithCAProgrammeCounts = [];
        $this->coursesFromEduroleProgrammeCounts = [];

        foreach ($this->programmeCodes as $code) {
            $this->coursesWithCAProgrammeCounts[] = $this->coursesWithCA->where('ProgrammeCode', $code)->count();

            if (in_array($code, ['BBS', 'NS', 'TP1', 'TP2', 'TP3', 'TP4', 'TP5', 'TP6', 'TP7'])) {
                $this->coursesFromEduroleProgrammeCounts[] = $this->coursesFromEdurole->where('ProgrammeCode', $code)->count();
            } else {
                $coursesFromCourseElectives = clone $this->coursesFromCourseElectivesQuery;
                $coursesFromCourseElectives = $coursesFromCourseElectives
                    ->where('study.ShortName', $code)
                    ->distinct()
                    ->pluck('course-electives.CourseID')
                    ->toArray();

                $this->coursesFromEduroleProgrammeCounts[] = $this->resultsForCount
                    ->where('ProgrammeCode', $code)
                    ->whereIn('ID', $coursesFromCourseElectives)
                    ->count();
            }
        }
    }

    protected function calculateSchoolStats()
    {
        $schools = ['SOHS', 'SOPHES', 'SOMCS', 'DRGS', 'SON', 'IBBS'];
        $stats = [];

        foreach ($schools as $school) {
            $getSchools = EduroleSchool::where('Description', $school)->first();
            if ($getSchools) {
                $schoolId = $getSchools->ID;
                $schoolProgrammes = EduroleStudy::where('ParentID', $schoolId)->pluck('ID')->toArray();

                $stats[$school] = [
                    'coursesWithCA' => CourseAssessment::whereIn('study_id', $schoolProgrammes)
                        ->select('course_id', 'delivery_mode')
                        ->groupBy('course_id', 'delivery_mode')
                        ->get()
                        ->count(),
                    'totalCourses' => $this->coursesFromEdurole
                        ->where('SchoolName', $school)
                        ->unique(function ($item) {
                            return $item->ID . '-' . $item->Delivery . '-' . $item->StudyID;
                        })->count()
                ];
            }
        }

        $this->schoolStats = $stats;
    }

    protected function calculateUserCountsPerSchool()
    {
        $this->userCountsPerSchool = $this->coursesFromEdurole
            ->groupBy('SchoolName')
            ->map(function ($group) {
                return $group->unique('username')->count();
            });
    }

    private function getCoursesFromLMMAX()
    {
        return CourseAssessment::select(
                'course_assessment_scores.course_code',
                'course_assessments.study_id',
                'course_assessments.delivery_mode',
                'course_assessments.basic_information_id'
            )
            ->join('course_assessment_scores', 'course_assessment_scores.course_assessment_id', '=', 'course_assessments.course_assessments_id')
            ->distinct()
            ->get()
            ->map(function ($item) {
                return [
                    'course_code' => $item->course_code,
                    'delivery_mode' => $item->delivery_mode,
                    'study_id' => $item->study_id,
                    'basic_information_id' => $item->basic_information_id
                ];
            })
            ->toArray();
    }

    protected function getNSAttachedCourses()
    {
        return EduroleStudy::join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->where('study.Name', '=', 'Natural Science')
            ->whereIn('courses.Name', ['MAT101', 'PHY101', 'CHM101', 'BIO101', 'OGC201'])
            ->select('study.ID')
            ->pluck('study.ID')
            ->toArray();
    }

    protected function getBasicSciencesCourses()
    {
        return EduroleStudy::join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->where('study.Name', '=', 'Basic Sciences')
            ->whereIn('courses.Name', [
                'BAB201', 'CAG201', 'CVS301', 'GIT301', 'GRA201', 'IHD201',
                'MCT201', 'NER301', 'PEB201', 'REN301', 'RES301', 'BCH2015',
                'BCH2060', 'CBP2020', 'HAN2040', 'HAN2050', 'PGY2040',
                'PHR3030', 'PHR3060', 'PTH2020', 'PTH2040', 'PTH2070'
            ])
            ->select('study.ID')
            ->pluck('study.ID')
            ->toArray();
    }

    protected function queryCourseFromEdurole()
    {
        $coursesFromCourseElectives = EduroleCourseElective::select('course-electives.CourseID')
            ->join('courses', 'courses.ID', '=', 'course-electives.CourseID')
            ->join('program-course-link', 'program-course-link.CourseID', '=', 'courses.ID')
            ->join('student-study-link', 'student-study-link.StudentID', '=', 'course-electives.StudentID')
            ->join('study', 'study.ID', '=', 'student-study-link.StudyID')
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
            ->distinct()
            ->select(
                'programmes.Year as YearOfStudy',
                'study.ShortName as ProgrammeCode',
                'basic-information.ID as username',
                'basic-information.ID as basicInformationId',
                'courses.ID',
                'courses.ID as CourseID',
                'basic-information.Firstname',
                'schools.Description AS SchoolName',
                'basic-information.PrivateEmail',
                'basic-information.Surname',
                'study.ProgrammesAvailable',
                'study.Name',
                'courses.Name as CourseName',
                'courses.CourseDescription',
                'study.Delivery',
                'study.ParentID',
                'study.ID as StudyID',
                'schools.Name as School',
                'study.ProgrammesAvailable'
            )
            ->where('study.ProgrammesAvailable', '!=', 1)
            ->whereIn('courses.ID', $coursesFromCourseElectives);
    }

    private function getCoursesFromEdurole()
    {
        $naturalScienceCourses = $this->getNSAttachedCourses();
        $getBasicSciencesCourses = $this->getBasicSciencesCourses();

        return $this->queryCourseFromEdurole()
            ->where(function($query) use ($naturalScienceCourses) {
                $query->whereNotIn('courses.Name', ['MAT101', 'PHY101', 'CHM101', 'BIO101'])
                    ->orWhereIn('study.ID', $naturalScienceCourses);
            })
            ->where(function($query) use ($getBasicSciencesCourses) {
                $query->whereNotIn('courses.Name', [
                    'BAB201', 'CAG201', 'CVS301', 'GIT301', 'GRA201', 'IHD201',
                    'MCT201', 'NER301', 'PEB201', 'REN301', 'RES301', 'BCH2015',
                    'BCH2060', 'CBP2020', 'HAN2040', 'HAN2050', 'PGY2040',
                    'PHR3030', 'PHR3060', 'PTH2020', 'PTH2040', 'PTH2070'
                ])
                ->orWhereIn('study.ID', $getBasicSciencesCourses);
            })
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
            });
    }

    public function exportToCSV()
    {
        return response()->streamDownload(function () {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['School', 'Courses with CA', 'Total Courses']);
            
            foreach ($this->schoolStats as $school => $stats) {
                fputcsv($output, [
                    $school,
                    $stats['coursesWithCA'],
                    $stats['totalCourses']
                ]);
            }
            fclose($output);
        }, 'CA_per_School.csv');
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}
