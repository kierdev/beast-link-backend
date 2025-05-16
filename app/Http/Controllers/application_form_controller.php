<?php

namespace App\Http\Controllers;

use App\Models\tbl_applicants;
use Illuminate\Http\Request;

class application_form_controller extends Controller
{
    //

    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'Last_Name'       => 'required|string|max:50',
            'First_Name'      => 'required|string|max:50',
            'Middle_Name'     => 'nullable|string|max:50',
            'Name_Extension'  => 'nullable|string|max:10',
            
            // Birth Details
            'Date_of_Birth'   => 'required|date',
            'Gender'          => 'required|string|max:10',
            'Religion'        => 'required|string|max:50',
            'Citizenship'     => 'required|string|max:50',
            'Civil_Status'    => 'required|string|max:50',
            'Place_of_Birth'  => 'required|string|max:50',
            'Age'             => 'required|integer',
            
            // Contact Details
            'Address'         => 'required|string|max:255',
            'Barangay'        => 'required|string|max:100',
            'City_Municipality'=> 'required|string|max:50',
            'District'        => 'required|integer',
            'Zip_Code'        => 'required|integer',
            'Mobile_Number'   => 'required|string|max:15',
            'Email'           => 'required|email|max:50',
            'Guardian_Name'   => 'required|string|max:50',
            'Guardian_Number' => 'required|string|max:15',
            'Guardian_Email'  => 'required|email|max:50',
            
            // Educational Background
            'SHS_Strand'      => 'required|string|max:50',
            'SHS_School'      => 'required|string|max:50',
            'SHS_Address'     => 'required|string|max:100',
            
            // Grades
            'GWA_12'          => 'required|double',
            'GWA_11'          => 'required|double',
            
            // Intended Course
            'First_Choice'    => 'required|string|max:50',
            'Second_Choice'   => 'required|string|max:50',
        ]);

        // Save the applicant data to the database
        $applicant = new tbl_applicants();
        $applicant->Last_Name = $validatedData['Last_Name'];
        $applicant->First_Name = $validatedData['First_Name'];
        $applicant->Middle_Name = $validatedData['Middle_Name'] ?? null;
        $applicant->Name_Extension = $validatedData['Name_Extension'] ?? null;
        $applicant->Date_of_Birth = $validatedData['Date_of_Birth'];
        $applicant->Gender = $validatedData['Gender'];
        $applicant->Religion = $validatedData['Religion'];
        $applicant->Citizenship = $validatedData['Citizenship'];
        $applicant->Civil_Status = $validatedData['Civil_Status'];
        $applicant->Place_of_Birth = $validatedData['Place_of_Birth'];
        $applicant->Age = $validatedData['Age'];
        $applicant->Address = $validatedData['Address'];
        $applicant->Barangay = $validatedData['Barangay'];
        $applicant->City_Municipality = $validatedData['City_Municipality'];
        $applicant->District = $validatedData['District'];
        $applicant->Zip_Code = $validatedData['Zip_Code'];
        $applicant->Mobile_Number = $validatedData['Mobile_Number'];
        $applicant->Email = $validatedData['Email'];
        $applicant->Guardian_Name = $validatedData['Guardian_Name'];
        $applicant->Guardian_Number = $validatedData['Guardian_Number'];
        $applicant->Guardian_Email = $validatedData['Guardian_Email'];
        $applicant->SHS_Strand = $validatedData['SHS_Strand'];
        $applicant->SHS_School = $validatedData['SHS_School'];
        $applicant->SHS_Address = $validatedData['SHS_Address'];
        $applicant->GWA_12 = $validatedData['GWA_12'];
        $applicant->GWA_11 = $validatedData['GWA_11'];
        $applicant->First_Choice = $validatedData['First_Choice'];
        $applicant->Second_Choice = $validatedData['Second_Choice'];

        // Save the record to the database
        $applicant->save();

        // Send email to the applicant with a preview of their application
     //   Mail::to($validatedData['Email'])->send(new ApplicationPreviewMail($validatedData));

        return back()->with('success', 'Application submitted! A copy has been sent to your email.');
    }
}
