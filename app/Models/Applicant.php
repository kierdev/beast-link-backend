<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    protected $table = 'tbl_applicants';
    protected $primaryKey = 'applicant_id';

    public $timestamps = false;

    public function applications()
    {
        return $this->hasMany(Application::class, 'applicant_id', 'applicant_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'applicant_id', 'applicant_id');
    }
}