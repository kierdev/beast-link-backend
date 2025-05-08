<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    /** @use HasFactory<\Database\Factories\ProgramFactory> */
    use HasFactory;

    protected $fillable = [
        'program_name',
        'program_college',
        'program_details',
        'program_code',
        'program_active',
        'description',
        'workflow',
        'no_interviewer',
        'passing_rate',
        'interview_description',
        'max_score',
        'passing_score',
        'document_type',
    ];

    protected $casts = [
        'document_type' => 'array',
    ];
}
