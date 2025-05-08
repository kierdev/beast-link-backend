<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programs = Program::all();
        return [ 'programs' => $programs, 'message' => 'Programs retrieved successfully' ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_name' => 'required|string',
            'program_college' => 'required|in:COT,CBA,COED',
            'program_details' => 'required|string',
            'program_code' => 'required|string|unique:programs,program_code,',
            'program_active' => 'required|boolean',
            'description' => 'nullable|string',
            'workflow' => 'required|in:Interview to Test,Test to Interview,Interview Only,Test Only',
            'no_interviewer' => 'required|integer',
            'passing_rate' => 'required|integer',
            'interview_description' => 'nullable|string',
            'max_score' => 'required|integer',
            'passing_score' => 'required|integer',
            'document_type' => ['required', 'array'],
            'document_type.*' => ['string'],
        ]);

        $program = Program::create($validated);
        return ['program' => $program , 'message' => 'Program created successfully' ];  
    }

    public function show($id)
    {
        $program = Program::findOrFail($id);
        return ['program' => $program, 'message' => 'Program retrieved successfully'];
    }


    public function update(Request $request, Program $program)
    {
        $fields = $request->validate([
            'program_name' => 'required|string',
            'program_college' => 'required|in:COT,CBA,COED',
            'program_details' => 'required|string',
            'program_code' => 'required|string|unique:programs,program_code,' . $program->id,
            'program_active' => 'required|boolean',
            'description' => 'nullable|string',
            'workflow' => 'required|in:Interview to Test,Test to Interview,Interview Only,Test Only',
            'no_interviewer' => 'required|integer',
            'passing_rate' => 'required|integer',
            'interview_description' => 'nullable|string',
            'max_score' => 'required|integer',
            'passing_score' => 'required|integer',
            'document_type' => ['required', 'array'],
            'document_type.*' => ['string'],
        ]);

        $program->update($fields);
        return ['program' => $program, 'message' => 'Program updated successfully' ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        $program->delete();
        return [ 'message' => 'Program deleted successfully' ]; 
    }
}
