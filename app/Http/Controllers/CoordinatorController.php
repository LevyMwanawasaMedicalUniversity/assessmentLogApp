<?php

namespace App\Http\Controllers;

use App\Models\EduroleStudy;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Http\Request;

class CoordinatorController extends Controller
{
    public function uploadCa(Request $request){

        $courseId = $request->courseIdValue;
        $statusId = $request->statusId;

        // return $courseId;

        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->select('courses.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName','courses.CourseDescription')
            ->where('courses.ID', $courseId)
            ->first();
        return view('coordinator.uploadCa', compact('results', 'statusId'));
    }

    public function importCAFromExcelSheet(Request $request){
        set_time_limit(1200000);
        // Validate the form data
        $request->validate([
            'excelFile' => 'required|mimes:xls,xlsx,csv',
            'academicYear' => 'required'            
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

                    $studentNumber = $row->getCellAtIndex(0)->getValue();
                    // Assuming the repeat courses are in the second column (index 2)
                    // $repeatCourses = $row->getCellAtIndex(0)->getValue();

                    $mark = $row->getCellAtIndex(1)->getValue();
                    $courseCode = $row->getCellAtIndex(2)->getValue();

                    $data[] = [
                        'student_number' => $studentNumber,
                        'mark' => $mark,
                        'course_code' => $courseCode
                    ];
                }
                
            }
            $reader->close();

            try{
                foreach ($data as $entry){
                    
                }
            }catch(\Exception $e){
                return back()->with('error', 'An error occurred while importing the data. Please try again.');
            }

        }
    }
}
