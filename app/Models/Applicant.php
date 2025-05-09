<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'academic_year',
        'course1',
        'course2',
    ];

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
    public function status()
    {
        return $this->hasOne(ApplicantStatus::class);
    }
}