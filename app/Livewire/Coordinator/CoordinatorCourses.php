<?php

namespace App\Livewire\Coordinator;

use App\Models\CourseAssessment;
use App\Models\EduroleStudy;
use App\Models\EduroleCourseElective;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CoordinatorCourses extends Component
{
    public $basicInformationId;
    public $studyId;
    public $results = [];
    public $isLoading = true;
    public $hasError = false;
    public $isRefreshing = false;
    public $lastUpdated = null;
    public $academicYear;

    public function mount($basicInformationId)
    {
        try {
            $this->academicYear = Setting::getCurrentAcademicYear();
            
            // Log the received basicInformationId
            Log::info('Mounting CoordinatorCourses component', [
                'receivedBasicInformationId' => $basicInformationId,
                'academicYear' => $this->academicYear
            ]);
            
            // Check if the basicInformationId is already decrypted
            if (is_numeric($basicInformationId)) {
                $this->basicInformationId = $basicInformationId;
                Log::info('BasicInformationId is already numeric', ['basicInformationId' => $this->basicInformationId]);
            } else {
                try {
                    $this->basicInformationId = Crypt::decrypt($basicInformationId);
                    Log::info('Successfully decrypted basicInformationId', ['basicInformationId' => $this->basicInformationId]);
                } catch (\Exception $e) {
                    Log::error('Failed to decrypt basicInformationId', [
                        'error' => $e->getMessage(),
                        'basicInformationId' => $basicInformationId
                    ]);
                    $this->hasError = true;
                    return;
                }
            }
            
            $this->loadStudyId();
            
            if (!$this->hasError) {
                $this->loadCourses();
            }
        } catch (\Exception $e) {
            Log::error('Error in mount method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->hasError = true;
        }
    }

    protected function loadStudyId()
    {
        try {
            // Get the study ID from the basic information ID
            $study = EduroleStudy::where('ProgrammesAvailable', $this->basicInformationId)
                ->first();
            
            if ($study) {
                $this->studyId = $study->ID;
                Log::info('Successfully loaded study ID', [
                    'studyId' => $this->studyId,
                    'basicInformationId' => $this->basicInformationId
                ]);
            } else {
                Log::warning('No study found for basic information ID', [
                    'basicInformationId' => $this->basicInformationId
                ]);
                $this->hasError = true;
            }
        } catch (\Exception $e) {
            $this->hasError = true;
            Log::error('Error loading study ID: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'basicInformationId' => $this->basicInformationId
            ]);
        }
    }

    protected function loadCourses()
    {
        try {
            $this->isLoading = true;
            
            // Natural Science courses or special study IDs
            $specialStudyIds = [163, 165, 166, 167, 168, 169, 170, 171, 172, 173, 174];
            
            // Log the study ID and basic information ID for debugging
            Log::info('Loading courses with parameters', [
                'basicInformationId' => $this->basicInformationId,
                'studyId' => $this->studyId,
                'academicYear' => $this->academicYear,
                'isSpecialStudy' => in_array($this->studyId, $specialStudyIds)
            ]);
            
            if (in_array($this->studyId, $specialStudyIds)) {
                $query = $this->getCoursesFromEdurole()
                    ->where('basic-information.ID', $this->basicInformationId)            
                    ->where('programmes.Year', $this->academicYear)
                    ->orderBy('programmes.Year')
                    ->orderBy('courses.Name')
                    ->orderBy('study.Delivery');
                
                // Log the SQL query for debugging
                Log::info('Special study query', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);
                
                $this->results = $query->get()->toArray();
            } else {
                $coursesFromCourseElectives = EduroleCourseElective::select('course-electives.CourseID')
                    ->join('courses', 'courses.ID', '=', 'course-electives.CourseID')
                    ->join('program-course-link', 'program-course-link.CourseID', '=', 'courses.ID')
                    ->join('student-study-link', 'student-study-link.StudentID', '=', 'course-electives.StudentID')
                    ->join('study', 'study.ID', '=', 'student-study-link.StudyID')
                    ->where('course-electives.Year', $this->academicYear)
                    ->where('course-electives.Approved', 1)
                    ->where('study.ProgrammesAvailable', $this->basicInformationId)
                    ->distinct();
                
                // Log the electives query for debugging
                Log::info('Electives query', [
                    'sql' => $coursesFromCourseElectives->toSql(), 
                    'bindings' => $coursesFromCourseElectives->getBindings()
                ]);
                
                $courseIds = $coursesFromCourseElectives->pluck('course-electives.CourseID')->toArray();
                
                Log::info('Course IDs from electives', ['count' => count($courseIds), 'ids' => $courseIds]);
                
                $query = $this->getCoursesFromEdurole()
                    ->where('basic-information.ID', $this->basicInformationId)
                    ->whereIn('courses.ID', $courseIds)
                    ->where('programmes.Year', $this->academicYear)
                    ->orderBy('programmes.Year')
                    ->orderBy('courses.Name')
                    ->orderBy('study.Delivery');
                
                // Log the main query for debugging
                Log::info('Main query', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);
                
                $this->results = $query->get()->toArray();
            }
            
            Log::info('Query results', ['count' => count($this->results)]);
            
            // Add assessment counts to each course
            foreach ($this->results as &$result) {
                $assessmentDetails = CourseAssessment::select(
                    'course_assessments.basic_information_id',
                    'assessment_types.assesment_type_name',
                    'assessment_types.id',
                    'course_assessments.delivery_mode',
                    DB::raw('count(course_assessments.course_assessments_id) as total')
                )
                ->where('course_assessments.course_id', $result['ID'])
                ->where('course_assessments.delivery_mode', $result['Delivery'])
                ->where('course_assessments.study_id', $result['StudyID'])
                ->where('course_assessments.academic_year', $this->academicYear)
                ->join('assessment_types', 'assessment_types.id', '=', 'course_assessments.ca_type')
                ->groupBy('assessment_types.id', 'course_assessments.basic_information_id', 'assessment_types.assesment_type_name', 'course_assessments.delivery_mode')
                ->get();
                
                $result['totalAssessments'] = $assessmentDetails->sum('total');
                $result['assessmentDetails'] = $assessmentDetails->toArray();
            }
            
            $this->lastUpdated = now()->format('Y-m-d H:i:s');
            $this->isLoading = false;
        } catch (\Exception $e) {
            $this->hasError = true;
            $this->isLoading = false;
            // Log the error for debugging
            Log::error('Error loading coordinator courses: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'basicInformationId' => $this->basicInformationId,
                'studyId' => $this->studyId,
                'academicYear' => $this->academicYear
            ]);
        }
    }

    public function refreshData()
    {
        $this->isRefreshing = true;
        $this->loadCourses();
        $this->isRefreshing = false;
        $this->dispatch('coursesUpdated', [
            'results' => $this->results,
            'lastUpdated' => $this->lastUpdated
        ]);
    }

    protected function getCoursesFromEdurole()
    {
        return DB::connection('edurole_database')->table('study')
            ->join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->join('schools', 'schools.ID', '=', 'study.ParentID')
            ->select(
                'courses.ID',
                'basic-information.ID as basicInformationId',
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
                'schools.Description as SchoolDescription',
                'programmes.Year as YearOfStudy'
            );
    }

    public function render()
    {
        return view('livewire.coordinator.coordinator-courses');
    }
}
