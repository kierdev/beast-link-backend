<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = [
        'applicant_id',
        'title',
        'message',
        'for_admin'
    ];

    protected $casts = [
        'for_admin' => 'boolean',
    ];
    

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}