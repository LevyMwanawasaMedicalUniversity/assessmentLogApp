<?php

namespace App\Http\Controllers;

use App\Models\CATypeMarksAllocation;
use App\Models\CourseAssessment;
use App\Models\CourseAssessmentScores;
use App\Models\CourseComponentAllocation;
use App\Models\EduroleBasicInformation;
use App\Models\EduroleCourseElective;
use App\Models\EduroleCourses;
use App\Models\EduroleGradesPublished;
use App\Models\EduroleStudy;
use App\Models\MismatchedSenateResults;
use App\Models\SenateApprovedResults;
use App\Models\StudentsContinousAssessment;
use App\Models\User;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use OwenIt\Auditing\Models\Audit;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdministratorController extends Controller
{
    //
    public function index()
    {
        return view('admin.index');
    }

    public function importGradesForReview(Request $request)
    {
        set_time_limit(1000000);
        ini_set('memory_limit', '512M');
        
        $request->validate([
            'excelFile' => 'required|mimes:xls,xlsx,csv',
            'academicYear' => 'required',
        ]);

        try {
            if ($request->hasFile('excelFile')) {
                $file = $request->file('excelFile');
                $filePath = $file->getPathname();

                if (!is_readable($filePath)) {
                    return back()->with('error', 'The uploaded file could not be read.');
                }

                $reader = ReaderEntityFactory::createXLSXReader();
                $reader->open($filePath);

                // Check sheet count
                $sheetCount = iterator_count($reader->getSheetIterator());
                if ($sheetCount > 1) {
                    $reader->close();
                    return back()->with('error', 'The workbook must contain exactly one sheet.');
                }

                $reader->close();
                $reader->open($filePath);

                $mismatches = [];
                DB::beginTransaction();
                try {
                    foreach ($reader->getSheetIterator() as $sheet) {
                        foreach ($sheet->getRowIterator() as $rowIndex => $row) {
                            if ($rowIndex === 1) continue;

                            $studentNumber = $row->getCellAtIndex(0) ? trim($row->getCellAtIndex(0)->getValue()) : null;
                            $studentNumber = preg_replace('/[^0-9]/', '', $studentNumber);
                            $academicYear = $request->academicYear;
                            $courseCode = $row->getCellAtIndex(2) ? trim($row->getCellAtIndex(2)->getValue()) : null;
                            
                            $caMark = $row->getCellAtIndex(3) ? (float)trim($row->getCellAtIndex(3)->getValue()) : null;
                            $examMark = $row->getCellAtIndex(4) ? (float)trim($row->getCellAtIndex(4)->getValue()) : null;
                            $grade = $row->getCellAtIndex(5) ? trim($row->getCellAtIndex(5)->getValue()) : null;

                            $publishedGrade = EduroleGradesPublished::where([
                                'StudentNo' => $studentNumber,
                                'AcademicYear' => $academicYear,
                                'CourseNo' => $courseCode
                            ])->first();

                            // Calculate Senate Grade
                            $totalMark = $caMark + $examMark;
                            if (!empty($grade)) {
                                $senateGrade = $grade; // Use pre-assigned grade if provided
                            }elseif ($examMark === null) {
                                $senateGrade = 'NE';
                            } elseif (is_numeric($totalMark) && $totalMark >= 0) {
                                if ($totalMark >= 90) $senateGrade = 'A+';
                                elseif ($totalMark >= 80) $senateGrade = 'A';
                                elseif ($totalMark >= 70) $senateGrade = 'B+';
                                elseif ($totalMark >= 60) $senateGrade = 'B';
                                elseif ($totalMark >= 55) $senateGrade = 'C+';
                                elseif ($totalMark >= 50) $senateGrade = 'C';
                                elseif ($totalMark >= 45) $senateGrade = 'D+';
                                elseif ($totalMark >= 40) $senateGrade = 'D';
                                else $senateGrade = 'F';
                            }

                            // Save to senate_approved_results
                            SenateApprovedResults::updateOrCreate(
                                [
                                    'student_id' => (int)$studentNumber,
                                    'academic_year' => $academicYear,
                                    'course_code' => $courseCode,
                                ],
                                [
                                    'senate_ca_score' => $caMark,
                                    'senate_exam_score' => $examMark,
                                    'edurole_ca_score' => $publishedGrade ? $publishedGrade->CAMarks : null,
                                    'edurole_exam_score' => $publishedGrade ? $publishedGrade->ExamMarks : null,
                                    'senate_grade' => $senateGrade,
                                    'edurole_grade' => $publishedGrade ? $publishedGrade->Grade : null,
                                ]
                            );

                            if ($publishedGrade) {
                                if ($publishedGrade->CAMarks != $caMark || 
                                    $publishedGrade->ExamMarks != $examMark) {
                                    
                                    // Save to mismatched_senate_results table
                                    MismatchedSenateResults::create([
                                        'student_id' => (int)$studentNumber,
                                        'academic_year' => $academicYear,
                                        'course_code' => $courseCode,
                                        'senate_ca_score' => $caMark,
                                        'edurole_ca_score' => $publishedGrade->CAMarks,
                                        'senate_exam_score' => $examMark,
                                        'edurole_exam_score' => $publishedGrade->ExamMarks,
                                        'senate_grade' => $senateGrade,
                                        'edurole_grade' => $publishedGrade->Grade
                                    ]);
 
                                    $publishedGrade->update([
                                        'CAMarks' => $caMark,
                                        'ExamMarks' => $examMark,
                                        'Grade' => $senateGrade // Also updating the grade to match new scores
                                    ]);

                                    $mismatches[] = [
                                        $studentNumber,
                                        $academicYear,
                                        $courseCode,
                                        $caMark,
                                        $publishedGrade->CAMarks,
                                        $examMark,
                                        $publishedGrade->ExamMarks,
                                        $senateGrade,
                                        $publishedGrade->Grade
                                    ];
                                }
                            }
                        }
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $reader->close();
                    return back()->with('error', 'Database error: ' . $e->getMessage());
                }
                
                $reader->close();

                if (!empty($mismatches)) {
                    // Create temporary file
                    $tempFile = tempnam(sys_get_temp_dir(), 'grade_mismatches');
                    $writer = WriterEntityFactory::createXLSXWriter();
                    $writer->openToFile($tempFile);

                    // Updated headers to include grades
                    $headers = [
                        'Student Number',
                        'Academic Year',
                        'Course Code',
                        'Senate CA Mark',
                        'Edurole CA Mark',
                        'Senate Exam Mark',
                        'Edurole Exam Mark',
                        'Senate Grade',
                        'Edurole Grade'
                    ];
                    $headerRow = WriterEntityFactory::createRowFromArray($headers);
                    $writer->addRow($headerRow);

                    foreach ($mismatches as $mismatch) {
                        $rowFromValues = WriterEntityFactory::createRowFromArray($mismatch);
                        $writer->addRow($rowFromValues);
                    }

                    $writer->close();

                    $content = file_get_contents($tempFile);
                    unlink($tempFile);

                    $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $fileName = $originalFileName . '_grade_mismatches_' . date('Y-m-d_His') . '.xlsx';
                    
                    return response($content)
                        ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                        ->header('Content-Length', strlen($content));
                }

                return back()->with('success', 'Review completed. No mismatches found.');
            }

        } catch (\Exception $e) {
            if (isset($reader)) {
                $reader->close();
            }
            if (isset($writer)) {
                $writer->close();
            }
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    
    // public function importGradesForReview(Request $request)
    // {
    //     set_time_limit(1000000);
    //     ini_set('memory_limit', '512M');
        
    //     $request->validate([
    //         'excelFile' => 'required|mimes:xls,xlsx,csv',
    //         'academicYear' => 'required',
    //     ]);

    //     try {
    //         if ($request->hasFile('excelFile')) {
    //             $file = $request->file('excelFile');
    //             $filePath = $file->getPathname();

    //             if (!is_readable($filePath)) {
    //                 return back()->with('error', 'The uploaded file could not be read.');
    //             }

    //             $reader = ReaderEntityFactory::createXLSXReader();
    //             $reader->open($filePath);

    //             // Check sheet count
    //             $sheetCount = iterator_count($reader->getSheetIterator());
    //             if ($sheetCount > 1) {
    //                 $reader->close();
    //                 return back()->with('error', 'The workbook must contain exactly one sheet.');
    //             }

    //             $reader->close();
    //             $reader->open($filePath);

    //             $mismatches = [];
    //             foreach ($reader->getSheetIterator() as $sheet) {
    //                 foreach ($sheet->getRowIterator() as $rowIndex => $row) {
    //                     try {
    //                         if ($rowIndex === 1) continue;

    //                         $studentNumber = trim($row->getCellAtIndex(0)->getValue());
    //                         $academicYear = $request->academicYear;
    //                         $courseCode = trim($row->getCellAtIndex(2)->getValue());
    //                         $caMark = (float)trim($row->getCellAtIndex(3)->getValue());
    //                         $examMark = (float)trim($row->getCellAtIndex(4)->getValue());

    //                         $publishedGrade = EduroleGradesPublished::where([
    //                             'StudentNo' => $studentNumber,
    //                             'AcademicYear' => $academicYear,
    //                             'CourseNo' => $courseCode
    //                         ])->first();

    //                         if ($publishedGrade) {
    //                             if ($publishedGrade->CAMarks != $caMark || 
    //                                 $publishedGrade->ExamMarks != $examMark) {
                                    
    //                                 $mismatches[] = [
    //                                     $studentNumber,
    //                                     $academicYear,
    //                                     $courseCode,
    //                                     $caMark,
    //                                     $publishedGrade->CAMarks,
    //                                     $examMark,
    //                                     $publishedGrade->ExamMarks
    //                                 ];
    //                             }
    //                         }

    //                     } catch (\Exception $e) {
    //                         $reader->close();
    //                         return back()->with('error', 'Error in row ' . $rowIndex . ': ' . $e->getMessage());
    //                     }
    //                 }
    //             }
    //             $reader->close();

    //             if (!empty($mismatches)) {
    //                 // Create temporary file
    //                 $tempFile = tempnam(sys_get_temp_dir(), 'grade_mismatches');
    //                 $writer = WriterEntityFactory::createXLSXWriter();
    //                 $writer->openToFile($tempFile);

    //                 // Add headers
    //                 $headers = [
    //                     'Student Number',
    //                     'Academic Year',
    //                     'Course Code',
    //                     'Uploaded CA Mark',
    //                     'Published CA Mark',
    //                     'Uploaded Exam Mark',
    //                     'Published Exam Mark'
    //                 ];
    //                 $headerRow = WriterEntityFactory::createRowFromArray($headers);
    //                 $writer->addRow($headerRow);

    //                 // Add data rows
    //                 foreach ($mismatches as $mismatch) {
    //                     $rowFromValues = WriterEntityFactory::createRowFromArray($mismatch);
    //                     $writer->addRow($rowFromValues);
    //                 }

    //                 $writer->close();

    //                 // Read the file content
    //                 $content = file_get_contents($tempFile);
                    
    //                 // Delete temporary file
    //                 unlink($tempFile);

    //                 // Prepare response
    //                 $fileName = 'grade_mismatches_' . date('Y-m-d_His') . '.xlsx';
                    
    //                 return response($content)
    //                     ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
    //                     ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
    //                     ->header('Content-Length', strlen($content));
    //             }

    //             return back()->with('success', 'Review completed. No mismatches found.');
    //         }

    //     } catch (\Exception $e) {
    //         if (isset($reader)) {
    //             $reader->close();
    //         }
    //         if (isset($writer)) {
    //             $writer->close();
    //         }
    //         return back()->with('error', 'An error occurred: ' . $e->getMessage());
    //     }
    // }

    public function refreshCAs(Request $request)
    {
        set_time_limit(12000000);
        $cooedinatorController = new CoordinatorController();
        
        $academicYear = 2024;
        
        $caTypeAllocation = CATypeMarksAllocation::all();

        $courseAssessments = CourseAssessment::all();  
        
        StudentsContinousAssessment::join('course_assessments', 'students_continous_assessments.course_assessment_id', '=', 'course_assessments.course_assessments_id')
            ->whereColumn('students_continous_assessments.ca_type', '!=', 'course_assessments.ca_type')
            // ->where('course_assessments.study_id', $programmeId)
            ->delete();


        $courseAssessments = CourseAssessment::all(); 
        $courseAssessmenetTypes = CATypeMarksAllocation::all();
        // return $courseAssessmenetTypes;

        foreach($courseAssessments as $courseAssessment){
            $courseId = $courseAssessment->course_id;
            $basicInformationId = $courseAssessment->basic_information_id;
            $delivery = $courseAssessment->delivery_mode;
            $studyId = $courseAssessment->study_id;
            $componentId = $courseAssessment->component_id;
            $course_assessmet_id = $courseAssessment->course_assessments_id;
            $assessmentTypes = $courseAssessment->ca_type;
            $courseAssessmenetTypes = CATypeMarksAllocation::where('course_id', $courseId)
                ->where('study_id', $studyId)
                ->where('delivery_mode', $delivery)
                ->where('component_id', $componentId)
                ->join('assessment_types', 'assessment_types.id', '=', 'c_a_type_marks_allocations.assessment_type_id')
                ->get();

            $academicYear = 2024;
            $getCoure = EduroleCourses::where('ID', $courseId)->first();
            $courseCode = $getCoure->Name;

            foreach ($courseAssessmenetTypes as $courseAssessmentType) {
                $studentsInAssessmentType = CourseAssessmentScores::where('course_code', $courseCode)
                    ->where('delivery_mode', $delivery)
                    ->where('study_id', $studyId)
                    // ->where('course_assessment_id', $course_assessmet_id)
                    ->where('component_id', $componentId)
                    ->get();

                foreach ($studentsInAssessmentType as $studentNumber) {
                    $cooedinatorController->refreshAllStudentsMarks(
                        $courseId,
                        $academicYear,
                        $courseAssessmentType->assessment_type_id,
                        $studentNumber->student_id,
                        $studentNumber->course_assessment_id,
                        $delivery,
                        $studentNumber->study_id,
                        $componentId
                    );
                }
            }
        }

        

        // foreach ($courseAssessmenetTypes as $courseAssessmentType) {
        //     // $coursesInEdurole = $this->getCoursesFromEdurole()
        //     //     ->where('courses.ID', $courseAssessment->course_id)
        //     //     ->where('study.ProgrammesAvailable', $courseAssessment->basic_information_id)
        //     //     ->where('study.Delivery', $courseAssessment->delivery_mode)
        //     //     ->first();
        //     $getCoure = EduroleCourses::where('ID', $courseAssessmentType->course_id)->first();
        //     $courseCode = $getCoure->Name;
        //     $studentsInAssessmentType = CourseAssessmentScores::where('course_code', $courseCode)->get();
        //     // $studentInCourseAssessment = $studentAssessments->unique('student_id');
        //     try{
        //         foreach ($studentsInAssessmentType as $student) {
        //             $cooedinatorController->refreshAllStudentsMarks(
        //                 $courseAssessmentType->course_id, 
        //                 $academicYear, 
        //                 $courseAssessmentType->assessment_type_id, 
        //                 $student->student_id, 
        //                 $student->course_assessment_id, 
        //                 $courseAssessmentType->delivery_mode, 
        //                 $student->study_id,
        //                 $courseAssessment->component_id
        //             );
        //         }
        //     }catch (Exception $e) {
        //         Log::error('Error refreshing student marks: ' . $e->getMessage());
        //         continue;
        //     }
        // }

        return redirect()->back()->with('success', 'Course Assessments refreshed successfully');
    }

    public function refreshCAInAprogram(Request $request)
    {
        

        $programmeId = $request->studyId;
        set_time_limit(12000000);
        $cooedinatorController = new CoordinatorController();

        StudentsContinousAssessment::join('course_assessments', 'students_continous_assessments.course_assessment_id', '=', 'course_assessments.course_assessments_id')
            ->whereColumn('students_continous_assessments.ca_type', '!=', 'course_assessments.ca_type')
            ->where('students_continous_assessments.study_id', $programmeId)
            ->delete();
        
        $academicYear = 2024;
        
        $caTypeAllocation = CATypeMarksAllocation::all();

        $courseAssessments = CourseAssessment::where('study_id', $programmeId)->get();
        $courseAssessmenetTypes = CATypeMarksAllocation::all();
        // return $courseAssessmenetTypes;

        foreach($courseAssessments as $courseAssessment){
            $courseId = $courseAssessment->course_id;
            $basicInformationId = $courseAssessment->basic_information_id;
            $delivery = $courseAssessment->delivery_mode;
            $studyId = $courseAssessment->study_id;
            $componentId = $courseAssessment->component_id;
            $course_assessmet_id = $courseAssessment->course_assessments_id;
            $assessmentTypes = $courseAssessment->ca_type;
            $courseAssessmenetTypes = CATypeMarksAllocation::where('course_id', $courseId)
                ->where('study_id', $studyId)
                ->where('delivery_mode', $delivery)
                ->where('component_id', $componentId)
                ->join('assessment_types', 'assessment_types.id', '=', 'c_a_type_marks_allocations.assessment_type_id')
                ->get();

            $academicYear = 2024;
            $getCoure = EduroleCourses::where('ID', $courseId)->first();
            $courseCode = $getCoure->Name;

            foreach ($courseAssessmenetTypes as $courseAssessmentType) {
                $studentsInAssessmentType = CourseAssessmentScores::where('course_code', $courseCode)
                    ->where('delivery_mode', $delivery)
                    ->where('study_id', $studyId)
                    // ->where('course_assessment_id', $course_assessmet_id)
                    ->where('component_id', $componentId)
                    ->get();

                foreach ($studentsInAssessmentType as $studentNumber) {
                    $cooedinatorController->refreshAllStudentsMarks(
                        $courseId,
                        $academicYear,
                        $courseAssessmentType->assessment_type_id,
                        $studentNumber->student_id,
                        $studentNumber->course_assessment_id,
                        $delivery,
                        $studentNumber->study_id,
                        $componentId
                    );
                }
            }
        }   


        return redirect()->back()->with('success', 'Course Assessments refreshed successfully');
    }


    public function importCoordinators(){
        set_time_limit(1200000);
    
        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->select('basic-information.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name','study.ParentID')
            ->get();
    
        foreach ($results as $result) {
            $email = trim($result->PrivateEmail);
            if (User::where('email', $email)->where('basic_information_id', '!=',$result->ID )->exists()) {
                $email = $result->ID . '@lmmu.ac.zm';
            }
    
            try {
                // Check if user already exists
                $existingUser = User::where('basic_information_id', $result->ID)->first();
                $password = $existingUser ? null : $this->generateRandomPassword();
    
                $user = User::updateOrCreate(
                    [
                        'basic_information_id' => $result->ID
                    ],
                    [
                        'name' => $result->Firstname . ' ' . $result->Surname,
                        'password' => $password ? bcrypt($password) : $existingUser->password,
                        'school_id' => $result->ParentID,
                        'email' => $email
                    ]
                );
    
                $coordinatorRole = Role::firstOrCreate(['name' => 'Coordinator']);
                $coordinatorPermission = Permission::firstOrCreate(['name' => 'Coordinator']);
                $user->assignRole($coordinatorRole);
                $user->givePermissionTo($coordinatorPermission);
    
                if ($password) {
                    $this->sendCredentialsEmail($user, $password);
                }
    
            } catch (\Exception $e) {
                Log::error('Error creating user: ' . $e->getMessage());
                continue;
            }
        }        
    
        return redirect()->back()->with('success', 'Coordinators imported successfully');
    }    

    public function importDeans(){
        set_time_limit(1200000);
    
        $results = EduroleBasicInformation::join('access', 'access.ID', '=', 'basic-information.ID')
            ->join('roles', 'roles.ID', '=', 'access.RoleID')
            ->join('schools', 'schools.Dean', '=', 'basic-information.ID')
            ->select('basic-information.FirstName', 'basic-information.Surname','basic-information.PrivateEmail', 'basic-information.ID', 'roles.RoleName', 'schools.ID as ParentID')
            ->get();
    
        foreach ($results as $result) {
            $email = trim($result->PrivateEmail);
            if (User::where('email', $email)->where('basic_information_id', '!=', $result->ID)->exists()) {
                $email = $result->ID . '@lmmu.ac.zm';
            }
    
            try {
                // Check if user already exists
                $existingUser = User::where('basic_information_id', $result->ID)->first();
                $password = $existingUser ? null : $this->generateRandomPassword();
    
                $user = User::updateOrCreate(
                    [
                        'basic_information_id' => $result->ID
                    ],
                    [
                        'name' => $result->FirstName . ' ' . $result->Surname,
                        'password' => $password ? bcrypt($password) : $existingUser->password,
                        'school_id' => $result->ParentID,
                        'email' => $email
                    ]
                );
    
                $deanRole = Role::firstOrCreate(['name' => 'Dean']);
                $deanPermission = Permission::firstOrCreate(['name' => 'Dean']);
                $user->assignRole($deanRole);
                $user->givePermissionTo($deanPermission);
    
                if ($password) {
                    $this->sendCredentialsEmail($user, $password);
                }
    
            } catch (\Exception $e) {
                Log::error('Error creating user: ' . $e->getMessage());
                continue;
            }
        }        
    
        return redirect()->back()->with('success', 'Deans imported successfully');
    }    
    
    private function generateRandomPassword($length = 10) {
        return Str::random($length);
    }
    
    private function sendCredentialsEmail($user, $password) {
        $details = [
            'title' => 'Account Created',
            'body' => 'Your account has been created. Your login details are:',
            'email' => $user->email,
            // 'email' => 'azwelsimwinga@gmail.com',
            'password' => $password
        ];
    
        Mail::to($user->email)->send(new \App\Mail\UserCredentialsMail($details));
    }
    
    public function auditTrails(){

        $audits = Audit::with('user')
        ->orderBy('created_at', 'desc')
        ->paginate(100); // Eager load the related user

        return view('admin.users.audits', compact('audits'));
    }

    public function editCourseAssessmentDescription($courseAssessmentId,$statusId){

        $courseAssessmentId = Crypt::decrypt($courseAssessmentId);
        $statusId = Crypt::decrypt($statusId);
        $result = CourseAssessment::where('course_assessments_id',$courseAssessmentId)
                ->first();
        // return $result;
        return view('coordinator.editCourseAssessmentDescription',compact('result'));
    }

    public function updateCourseAssessmentDescription(Request $request, $courseAssessmentId){

        $description = $request->description;
        
        if($description){
            $courseAssessment = CourseAssessment::find($courseAssessmentId);
            $courseAssessment->description = $description;
            $courseAssessment->save();
        }       

        return back()->with('success', 'Updated successfully click "View CA"');
    }

    public function viewCoordinators(){
        return $this->generateCoordinatorsView();
    }

    public function viewCoordinatorsUnderDean($schoolId)
    {
        $schoolId = Crypt::decrypt($schoolId);
        return $this->generateCoordinatorsView($schoolId);
    }

    private function generateCoordinatorsView($schoolId = null)
    {
        // Fetch courses from Edurole, optionally filtering by school ID
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
        $coursesFromCourseElectivesQuery =EduroleCourseElective::select('course-electives.CourseID')
        ->join('courses', 'courses.ID', '=', 'course-electives.CourseID')
        ->join('program-course-link', 'program-course-link.CourseID', '=', 'courses.ID')
        ->join('student-study-link', 'student-study-link.StudentID', '=', 'course-electives.StudentID')
        ->join('study', 'study.ID', '=', 'student-study-link.StudyID')
        ->where('course-electives.Year', 2024)
        ->where('course-electives.Approved', 1);
        // return $coursesFromEdurole;

        // $totalCoursesCoordinated = $coursesFromEdurole->unique('ID','Delivery','StudyID')->count();
        $totalCoursesCoordinated = $coursesFromEdurole->unique(function ($item) {
            return $item['ID'] . '-' . $item['Delivery'] . '-' . $item['StudyID'];
        })->count();
        $counts = $coursesFromEdurole->countBy('StudyID');
        $results = $coursesFromEdurole->unique('basicInformationId', 'Name');

        // Return the view with the data
        return view('dean.viewCoordinators', compact('schoolId','coursesFromCourseElectivesQuery','resultsForCount', 'results', 'counts', 'totalCoursesCoordinated'));
    }


    public function viewDeans(){
        $results = EduroleBasicInformation::join('access', 'access.ID', '=', 'basic-information.ID')
            ->join('roles', 'roles.ID', '=', 'access.RoleID')
            ->join('schools', 'schools.Dean', '=', 'basic-information.ID')
            ->join('study', 'study.ParentID', '=', 'schools.ID')
            ->select('basic-information.FirstName', 'basic-information.Surname', 'basic-information.ID as basicInformationId', 'roles.RoleName', 'schools.ID as ParentID', 'study.ID as StudyID', 'schools.Name as SchoolName')
            // ->groupBy('basic-information.ID')
            ->get();
        $counts = $results->countBy('basicInformationId');
        $results= $results->unique('basicInformationId');
        
        // return $results;
        return view('registrar.viewDeans', compact('results', 'counts'));
    }

    public function viewCoordinatorsCourses($basicInformationId){
        $basicInformationId = Crypt::decrypt($basicInformationId);
        
        
        $getStudyId = EduroleStudy::where('ProgrammesAvailable', '=', $basicInformationId)->first();
        // return $getStudyId;
        $studyId = $getStudyId->ID;
        // return $coursesFromCourseElectives;

        
        // $naturalScienceCourses = $this->getNSAttachedCourses();
        if($studyId == 163 || $studyId == 165 || $studyId == 166 || $studyId == 167 || $studyId == 168 || $studyId == 169 || $studyId == 170 || $studyId == 171 || $studyId == 172 || $studyId == 173 || $studyId == 174){
            $results = $this->getCoursesFromEdurole()
            ->where('basic-information.ID', $basicInformationId)            
            ->orderBy('programmes.Year')
            ->orderBy('courses.Name')
            ->orderBy('study.Delivery')            
            ->get();
        }else{
            $coursesFromCourseElectives = EduroleCourseElective::select('course-electives.CourseID')
                ->join('courses', 'courses.ID','=','course-electives.CourseID')
                ->join('program-course-link', 'program-course-link.CourseID','=','courses.ID')
                ->join('student-study-link','student-study-link.StudentID','=','course-electives.StudentID')
                ->join('study','study.ID','=','student-study-link.StudyID')
                ->where('course-electives.Year', 2024)
                ->where('course-electives.Approved', 1)
                ->where('study.ProgrammesAvailable', $basicInformationId)
                ->distinct()
                ->pluck('course-electives.CourseID')
                ->toArray();
            $results = $this->getCoursesFromEdurole()
                ->where('basic-information.ID', $basicInformationId)
                ->whereIn('courses.ID', $coursesFromCourseElectives)
                ->orderBy('programmes.Year')
                ->orderBy('courses.Name')
                ->orderBy('study.Delivery')            
                ->get();
        }
        
        
        return view('coordinator.viewCoordinatorsCourses', compact('basicInformationId','results','studyId'));

    }

    public function viewCoordinatorsCoursesWithComponents($courseIdEncrypt,$basicInformationIdEncrypt,$deliveryEncrypt,$studyIdEncrypt){

        $courseId = Crypt::decrypt($courseIdEncrypt);
        // return $courseIdEncrypt;
        // return $basicInformationIdEncrypt;
        $basicInformationId = Crypt::decrypt($basicInformationIdEncrypt);
        // return $basicInformationId;
        $delivery = Crypt::decrypt($deliveryEncrypt);
        $studyId = Crypt::decrypt($studyIdEncrypt);
        $academicYear = 2024;
        // return $courseId . ' ' . $basicInformationId . ' ' . $delivery . ' ' . $studyId;
        $naturalScienceCourses = $this->getNSAttachedCourses();
        $results = $this->getAllocatedCourses($courseId,$basicInformationId,$delivery,$studyId, $academicYear);
        $getCoure = EduroleCourses::where('ID', $courseId)->first();
        $courseCode = $getCoure->Name;
        $getStudy = EduroleStudy::where('ID', $studyId)->first();
        $studyName = $getStudy->Name;
        $deliveryMode = $getStudy->Delivery;
                
        return view('coordinator.caComponents.viewCoordinatorsCourses', compact('results','courseCode','studyName','deliveryMode','basicInformationId'));

    }

    public function viewCourse(Request $request){

        $courseId = $request->courseId;
        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->select('courses.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName','courses.CourseDescription')
            ->where('courses.ID', $courseId)
            ->first();
        
        return view('coordinator.viewCourse', compact('results'));

    }

    
}
