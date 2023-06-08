<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Admin extends Model
{
    public static function getOrCreate($email)
    {
        $admin = Admin::where('email', $email)->first();
        if ($admin == null) {
            $admin = new Admin;
            $admin->email = $email;
            $admin->password = bcrypt(Str::random(32));
            $admin->active = false; // Disable login by default.
        }
        $admin->save();
        return $admin;
    }
}
