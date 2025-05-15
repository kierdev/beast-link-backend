<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $table = 'tbl_exams';  // Explicitly specify the table name
    protected $primaryKey = 'exam_id';  // Primary key

    // Disable timestamps
    public $timestamps = false;

    // Define relationship with applications
    public function applications()
    {
        return $this->hasMany(Application::class, 'exam_id', 'exam_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_code', 'program_code');
    }
}