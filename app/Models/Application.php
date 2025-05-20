<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = 'tbl_applications';
    protected $primaryKey = 'application_id';
    public $timestamps = false;

    protected $fillable = [
        'applicant_id',
        'program_code',
        'exam_id',
        'application_date',
        'status',
        'exam_score'
    ];

    // back to the applicant
    public function applicant()
    {
        return $this->belongsTo(
            Applicant::class,
            'applicant_id',
            'applicant_id'
        );
    }

    // link by program_code → tbl_program.program_code
    public function program()
    {
        return $this->belongsTo(
            Program::class,
            'program_code',
            'program_code'
        );
    }
}
