<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserEvent extends Model
{
    protected $table = 'user_events';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
