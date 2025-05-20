<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $table = 'tbl_program';
    protected $primaryKey = 'program_id';
    protected $fillable = [
        'program_name',
        'program_code',
        'description',
        'duration_years'
    ];

    public function admissionResults()
    {
        return $this->hasMany(AdmissionResult::class, 'program_id');
    }
}
