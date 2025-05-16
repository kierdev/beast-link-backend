<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'tbl_program';
    protected $primaryKey = 'program_id';
    public $timestamps = false;

    public function applications()
    {
        return $this->hasMany(Application::class, 'program_code', 'program_code');
    }
}