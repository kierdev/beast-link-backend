<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = 'tbl_applications';
    protected $primaryKey = 'application_id';
    public $timestamps = false;

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_code', 'program_code');
    }
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }
}
