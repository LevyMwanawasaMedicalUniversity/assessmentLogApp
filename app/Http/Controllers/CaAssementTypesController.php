<?php

namespace App\Http\Controllers;

use App\Models\AssessmentTypes;
use Illuminate\Http\Request;

class CaAssementTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assessmentTypes = AssessmentTypes::all();
        return view('admin.caAssementTypes.index', compact('assessmentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.caAssementTypes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'assessmentName' => 'required|string'
            
            // Add more validation rules as per your requirements
        ]);

        AssessmentTypes::create([
            'assesment_type_name' => $request->assessmentName
        ]);
        


        return redirect()->route('caAssessmentTypes.index')
            ->withSuccess(__('Assessment Type created successfully.'));
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
    // public function edit(string $id)
    // {
    //     //
    // }

    public function edit($assessmentTypesId)
    {
        $assessmentTypes = AssessmentTypes::find($assessmentTypesId);

        return view('admin.caAssementTypes.edit', compact('assessmentTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $assessmentTypesId)
    {
        $request->validate([
            'assessmentName' => 'required',
        ]);

        $assessmentTypes = AssessmentTypes::find($assessmentTypesId);
        $assessmentTypes->assesment_type_name = $request->assessmentName;
        $assessmentTypes->save();
        // $assessmentTypes->update($request->only('assessmentName'));

        return redirect()->route('caAssessmentTypes.index')
            ->withSuccess(__('Permission updated successfully.'));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($assessmentTypesId)
    {
        $assessmentTypes = AssessmentTypes::find($assessmentTypesId);
        $assessmentTypes->delete();

        return redirect()->route('caAssessmentTypes.index')
            ->withSuccess(__('Assessment Type deleted successfully.'));
    }
}
