<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'tbl_notifications';
    protected $primaryKey = 'notification_id';

    protected $fillable = [
        'applicant_id',
        'title',
        'message',
        'for_admin',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'for_admin' => 'boolean',
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'applicant_id');
    }
}