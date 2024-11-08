<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HubstaffTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'hubstaff_id',
        'project_id',
        'title',
        'description',
        'status',
        'due_date',
        'assignee_id',
        'lock_version'
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];
} 