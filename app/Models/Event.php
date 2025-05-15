<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'tbl_events';

    protected $fillable = [
        'title',
        'event_date',
        'event_type',
    ];

    public $timestamps = true;
}