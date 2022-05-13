<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class crmPerson extends Model
{
    use HasFactory;

    protected $table = 'crm_persons';
    public $timestamps = false;
}
