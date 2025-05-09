<?php

namespace App\Http\Controllers;

use App\Mail\StatusMail;
use App\Models\Applicant;
use Illuminate\Http\Request;
use Mail;

class ApplicantController extends Controller
{
    public function test()
    {
        return response()->json(['message' => 'test'], 200);
    }
    
    // Get all applicants
    public function index()
    {
        $applicants = Applicant::with('status')->get()->map(function ($applicant) {
            $data = $applicant->toArray();
            unset($data['status']); // Remove the nested status relationship 
            $data['status'] = $applicant->status ? $applicant->status->status : null;
            $data['remarks'] = $applicant->status ? $applicant->status->remarks : null;
            return $data;
        });

        return response()->json($applicants, 200);
    }

    // Filter applicants by name, email, course1, course2, academic_year, status    
    public function filter(Request $request)
    {
        $query = Applicant::with('status');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('course1')) {
            $query->where('course1', $request->course1);
        }

        if ($request->filled('course2')) {
            $query->where('course2', $request->course2);
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->filled('status')) {
            $query->whereHas('status', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        $applicants = $query->get()->map(function ($applicant) {
            $data = $applicant->toArray();
            unset($data['status']);
            $data['status'] = $applicant->status ? $applicant->status->status : null;
            $data['remarks'] = $applicant->status ? $applicant->status->remarks : null;
            return $data;
        });

        return response()->json($applicants, 200);
    }

    // Get applicant by id
    public function show(string $id)
    {
        $applicant = Applicant::with('status')->findOrFail($id);

        $data = $applicant->toArray();
        unset($data['status']); // Remove the nested status relationship
        $data['status'] = $applicant->status ? $applicant->status->status : null;
        $data['remarks'] = $applicant->status ? $applicant->status->remarks : null;

        return response()->json($data);
    }

    // Create a new applicant
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:applicants',
            'academic_year' => 'required|string|max:255',
            'course1' => 'required|string|max:255',
            'course2' => 'nullable|string|max:255',
        ]);

        $applicant = Applicant::create($validatedData);
        // For applicant
        $applicant->notifications()->create([
            'applicant_id' => $applicant->id,
            'title' => 'Application Received',
            'message' => "Your application for the $applicant->course1 program has been successfully submitted. You will receive updates on your document verification and interview schedule soon.",
        ]);
        // For admin
        $applicant->notifications()->create([
            'applicant_id' => $applicant->id,
            'title' => 'New Applicant Submission',
            'message' => "A new applicant $applicant->name, has submitted an application for the $applicant->course1 program. Please review the submitted documents and verify the application status.",
            'for_admin' => true,
        ]);
        $applicant->status()->create([
            'applicant_id' => $applicant->id,
            'status' => 'Pending',
            'remarks' => 'Your application is under review.',
        ]);

        Mail::to($applicant->email)->send(new StatusMail(
            [
                'applicant_id' => $applicant->id,
                'applicant_name' => $applicant->name,
                'status' => 'Pending',
                'remarks' => 'Your application is under review.',
            ]
        ));

        return response()->json($applicant, 201);
    }

    // Update applicant data
    public function update(Request $request, string $id)
    {

        $applicant = Applicant::findOrFail($id);
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:applicants,email,' . $applicant->id,
            'academic_year' => 'nullable|string|max:255',
            'course1' => 'nullable|string|max:255',
            'course2' => 'nullable|string|max:255',
        ]);

        $applicant->update($validatedData);

        return response()->json(['message' => 'Applicant updated'], 200);
    }

    // Delete applicant
    public function destroy(string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $applicant->status()->delete();
        $applicant->notifications()->delete();
        $applicant->delete();
        return response()->json([
            'message' => 'Applicant deleted'
        ], 200);
    }
}
