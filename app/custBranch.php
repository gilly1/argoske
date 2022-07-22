<?php

namespace App;

use App\debtorsMaster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class custBranch extends Model
{
    use HasFactory;
    protected $table = 'cust_branch';
    protected $primary_key = 'branch_code';
    public $timestamps = false;
    protected $fillable = [
        'debtor_no'

    ];


    public function debtorsMaster()
    {
        return $this->belongsTo(debtorsMaster::class, 'debtor_no', 'debtor_no');
    }
}
