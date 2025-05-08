<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentList extends Model
{
    /** @use HasFactory<\Database\Factories\DocumentListFactory> */
    use HasFactory;

    protected $fillable = [
        'document_name',
    ];
}
