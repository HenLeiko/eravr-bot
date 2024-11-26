<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserState extends Model
{
    protected $fillable = ['user_id', 'state', 'command', 'data'];
}
