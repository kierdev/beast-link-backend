<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    protected $table = 'tbl_applicants';
    protected $primaryKey = 'applicant_id';
    public $timestamps = false;

    // your existing fillable…

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
        'College',
        'First_Choice',
        'Second_Choice',
        'Academic_Year',
        'Timestamp',
    ];

    // 1-1 to tbl_applications (holds status & program)
    public function application()
    {
        return $this->hasOne(
            Application::class,
            'applicant_id',
            'applicant_id'
        );
    }

    // 1-1 to tbl_admission_results (stores letter_path, etc.)
    public function admissionResult()
    {
        return $this->hasOne(
            AdmissionResult::class,
            'applicant_id',
            'applicant_id'
        );
    }
}
