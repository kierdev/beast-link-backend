<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Http\Requests\StoreAcademicYearRequest;
use App\Http\Requests\UpdateAcademicYearRequest;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $academicYears = AcademicYear::all();
        return ['academicYears' => $academicYears, 'message' => 'Academic Years retrieved successfully'];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string',
            'academic_semester' => 'required|string',
        ]);

        $academicYear = AcademicYear::create($validated);
        return ['academicYear' => $academicYear, 'message' => 'Academic Year created successfully'];
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicYear $academicYear)
    {
        return ['academicYear' => $academicYear, 'message' => 'Academic Year retrieved successfully'];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string',
            'academic_semester' => 'required|string',
        ]);

        $academicYear->update($validated);
        return ['academicYear' => $academicYear, 'message' => 'Academic Year updated successfully'];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();
        return ['message' => 'Academic Year deleted successfully'];
    }
}
