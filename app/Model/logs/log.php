<?php

namespace App\Model\logs;

use App\User;
use Illuminate\Database\Eloquent\Model;

class log extends Model
{
    public function users()
    {
        return $this->hasMany(User::class,'id','user_id');
    }
}
