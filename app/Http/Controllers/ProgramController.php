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
            'no_interviewer' => 'required_if:workflow,Interview Only|integer',
            'passing_rate' => 'required_if:workflow,Interview Only|integer',
            'max_score' => 'required_if:workflow,Test Only|integer',
            'passing_score' => 'required_if:workflow,Test Only|integer',
            'interview_description' => 'nullable|string',
            'document_type' => ['required', 'array'],
            'document_type.*' => ['string'],
        ], [
            'program_name.required' => 'The program name field is required.',
            'program_name.string' => 'The program name must be a string.',
            'program_college.required' => 'The program college field is required.',
            'program_college.in' => 'The program college must be one of the following: COT, CBA, COED.',
            'program_details.required' => 'The program details field is required.',
            'program_code.required' => 'The program code field is required.',
            'program_code.unique' => 'The program code must be unique.',
            'program_active.required' => 'The program active field is required.',
            'program_active.boolean' => 'The program active field must be true or false.',
            'workflow.required' => 'The workflow field is required.',
            'workflow.in' => 'The workflow must be one of the following: Interview to Test, Test to Interview, Interview Only, Test Only.',
            'no_interviewer.required_if' => 'The number of interviewers is required when the workflow is Interview Only.',
            'passing_rate.required_if' => 'The passing rate is required when the workflow is Interview Only.',
            'max_score.required_if' => 'The maximum score is required when the workflow is Test Only.',
            'passing_score.required_if' => 'The passing score is required when the workflow is Test Only.',
            'document_type.required' => 'The document type field is required.',
            'document_type.array' => 'The document type must be an array.',
            'document_type.*.string' => 'Each document type must be a string.',
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
            'no_interviewer' => 'required_if:workflow,Interview Only|integer',
            'passing_rate' => 'required_if:workflow,Interview Only|integer',
            'max_score' => 'required_if:workflow,Test Only|integer',
            'passing_score' => 'required_if:workflow,Test Only|integer',
            'interview_description' => 'nullable|string',
            'document_type' => ['required', 'array'],
            'document_type.*' => ['string'],
        ], [
            'program_name.required' => 'The program name field is required.',
            'program_name.string' => 'The program name must be a string.',
            'program_college.required' => 'The program college field is required.',
            'program_college.in' => 'The program college must be one of the following: COT, CBA, COED.',
            'program_details.required' => 'The program details field is required.',
            'program_code.required' => 'The program code field is required.',
            'program_code.unique' => 'The program code must be unique.',
            'program_active.required' => 'The program active field is required.',
            'program_active.boolean' => 'The program active field must be true or false.',
            'workflow.required' => 'The workflow field is required.',
            'workflow.in' => 'The workflow must be one of the following: Interview to Test, Test to Interview, Interview Only, Test Only.',
            'no_interviewer.required_if' => 'The number of interviewers is required when the workflow is Interview Only.',
            'passing_rate.required_if' => 'The passing rate is required when the workflow is Interview Only.',
            'max_score.required_if' => 'The maximum score is required when the workflow is Test Only.',
            'passing_score.required_if' => 'The passing score is required when the workflow is Test Only.',
            'document_type.required' => 'The document type field is required.',
            'document_type.array' => 'The document type must be an array.',
            'document_type.*.string' => 'Each document type must be a string.',
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
