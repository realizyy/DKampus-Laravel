<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailOtp extends Model
{
    public const PURPOSE_REGISTER = 'register';

    public const PURPOSE_RESET = 'reset';

    protected $fillable = ['email', 'purpose', 'code_hash', 'attempts', 'expires_at', 'sent_at'];

    protected $casts = [
        'expires_at' => 'datetime',
        'sent_at' => 'datetime',
        'attempts' => 'integer',
    ];
}
