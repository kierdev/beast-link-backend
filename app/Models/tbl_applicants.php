<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tbl_applicants extends Model
{
    //

    protected $table = 'tbl_applicants'; 
    protected $fillable = [
        'Last_Name',
        'First_Name',
        'Middle_Name',
        'Name_Extension',
        'Date_of_Birth',
        'Gender',
        'Religion',
        'Citizenship',
        'Civil_Status',
        'Place_of_Birth',
        'Age',
        'Address',
        'Barangay',
        'City_Municipality',
        'District',
        'Zip_Code',
        'Mobile_Number',
        'Email',
        'Guardian_Name',
        'Guardian_Number',
        'Guardian_Email',
        'SHS_Strand',
        'SHS_School',
        'SHS_Address',
        'GWA_12',
        'GWA_11',
        'First_Choice',
        'Second_Choice',
    ];
}
