<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicantStatus extends Model
{
    use HasFactory;
    protected $fillable = [
        'applicant_id',
        'status',
        'remarks',
        // 'for_admin'
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}