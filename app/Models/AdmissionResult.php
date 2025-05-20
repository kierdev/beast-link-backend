<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionResult extends Model
{
    use HasFactory;

    protected $table = 'tbl_admission_results';
    protected $primaryKey = 'result_id';

    protected $fillable = [
        'applicant_id',
        'program_id',
        'admission_status',
        'letter_status',
        'letter_path',
        'sent_at',
    ];

    protected $dates = ['sent_at'];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }

    public function program()
    {
        return $this->belongsTo(Applicant::class, 'program_id');
    }
}
