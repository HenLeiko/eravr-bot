<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserState extends Model
{
    protected $table = 'user_state';
    protected $fillable = ['chat_id', 'state', 'data'];

    /**
     * Преобразование атрибутов к нужному типу
     *
     * @var string[]
     */
    protected $casts = [
        'data' => 'array',
    ];
}
