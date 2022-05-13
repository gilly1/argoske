<?php

namespace App\Http\Controllers;

use App\Model\ContractItems;
use App\Model\InquiryItems;
use App\StockMaster;
use App\Model\StockMove;
use Illuminate\Http\Request;

class ItemsController extends Controller
{

    public function getStock()
    {
        return StockMaster::where('no_sale',0)->orderBy('description','asc')->get();
    }
    public function populateFields($id)
    {
        return InquiryItems::where('inquiry_id',$id)->get();
    }
    public function populateContractFields($id)
    {
        return ContractItems::where('contract_id',$id)->get();
    }
}
