<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contests extends Model
{
    protected $table = 'contests';
    protected $fillable = [
        'title',
        'description',
        'image',
        'tiny_description',
        'is_active',
        'started_at',
        'ended_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function isActive()
    {
        return $this->is_active && $this->ended_at->isPast();
    }
}
