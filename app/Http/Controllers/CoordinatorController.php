<?php

namespace App\Http\Controllers;

use App\Models\CourseAssessment;
use App\Models\CourseAssessmentScores;
use App\Models\EduroleStudy;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class CoordinatorController extends Controller
{
    public function uploadCa($statusId, $courseIdValue){

        $courseId = Crypt::decrypt($courseIdValue);
        $statusId = Crypt::decrypt($statusId);

        // return $courseId;

        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->select('courses.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName','courses.CourseDescription','basic-information.ID as basicInformationId')
            ->where('courses.ID', $courseId)
            ->first();
        
        return view('coordinator.uploadCa', compact('results', 'statusId','courseId'));
    }

    public function importCAFromExcelSheet(Request $request){
        set_time_limit(1200000);
        // Validate the form data
        $request->validate([
            'excelFile' => 'required|mimes:xls,xlsx,csv',
            'academicYear' => 'required',
            'status' => 'required',
            'course_id' => 'required',
            'course_code' => 'required',   
            'basicInformationId' => 'required',  
        ]);

        $newAssessment =CourseAssessment::Create([
            'course_id' => $request->course_id,
            'ca_type' => $request->status,
            'academic_year' => $request->academicYear,
            'basic_information_id' => $request->basicInformationId,
        ]);

        if ($request->hasFile('excelFile')) {
            $file = $request->file('excelFile');

            // Initialize the Box/Spout reader
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->open($file->getPathname());

            $isHeaderRow = true;
            $data = [];
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    // Skip the header row
                    if ($isHeaderRow) {
                        $isHeaderRow = false;
                        continue;
                    }
                    try{
                        $studentNumber = $row->getCellAtIndex(0)->getValue();                    
                        $mark = $row->getCellAtIndex(1)->getValue();
                        // $courseCode = $row->getCellAtIndex(2)->getValue();
                    } catch (\Exception $e) {
                        return back()->with('error', 'Please format excel sheet correctly.');
                    }

                    $data[] = [
                        'student_number' => $studentNumber,
                        'mark' => $mark,
                    ];
                }
                
            }
            $reader->close();

            try{
                foreach ($data as $entry){
                    CourseAssessmentScores::updateOrCreate([
                        'course_assessment_id' => $newAssessment->id,
                        'student_id' => trim($entry['student_number']),                        
                        'course_code' => $request->course_code,
                    ],[                        
                        'score' => $entry['mark'],                      
                    ]);
                }
            }catch(\Exception $e){
                return back()->with('error', 'An error occurred while importing the data. Please try again.');
            }

        }

        // return redirect()->back()->with('success', 'Data imported successfully');
        // return redirect()->route('coordinator.uploadCa', ['courseIdValue' => $request->course_id, 'statusId' => $request->status])->with('success', 'Data imported successfully');
    }
}
