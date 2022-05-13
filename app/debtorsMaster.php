<?php

namespace App;

use App\custBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class debtorsMaster extends Model
{
    use HasFactory;
    protected $table = 'debtors_master';
    protected $primary_key = 'debtor_no';
    public $timestamps = false;

    public function custBranch()
    {
        return $this->hasMany(custBranch::class, 'debtor_no', 'debtor_no');
    }

    public function crmContact()
    {
        return $this->hasMany(crmContact::class, 'entity_id', 'debtor_no');
    }
}
