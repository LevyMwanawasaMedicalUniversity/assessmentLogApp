<?php

namespace App\Http\Controllers;

use App\Models\CourseComponent;
use Illuminate\Http\Request;

class CourseComponentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courseComponents = CourseComponent::all();
        
        return view('admin.courseComponents.index', compact('courseComponents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.courseComponents.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'courseComponentName' => 'required|string'
            
            // Add more validation rules as per your requirements
        ]);

        CourseComponent::create([
            'component_name' => $request->courseComponentName
        ]);
        


        return redirect()->route('courseComponents.index')
            ->withSuccess(__('Course Component created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($courseComponentId)
    {
        $courseComponents = CourseComponent::find($courseComponentId);

        return view('admin.courseComponents.edit', compact('courseComponents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $courseComponentId)
    {
        $request->validate([
            'courseComponentName' => 'required',
        ]);

        $courseComponents = CourseComponent::find($courseComponentId);
        $courseComponents->component_name = $request->courseComponentName;
        $courseComponents->save();
        

        return redirect()->route('courseComponents.index')
            ->withSuccess(__('Component updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ourseComponentId)
    {
        $ourseComponents = CourseComponent::find($ourseComponentId);
        $ourseComponents->delete();

        return redirect()->route('courseComponents.index')
            ->withSuccess(__('Component deleted successfully.'));
    }
}
