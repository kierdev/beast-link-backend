<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table      = 'tbl_program';
    protected $primaryKey = 'program_id';
    public    $timestamps = false;

    protected $fillable = [
      'program_name','program_category',
      'program_description','program_code'
    ];
}
