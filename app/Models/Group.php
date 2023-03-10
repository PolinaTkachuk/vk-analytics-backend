<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'name',
        'url',
        'avatar',
        'description',
        'status',
        'sync_interval_in_hours',
    ];
}
