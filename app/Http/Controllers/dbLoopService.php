<?php
namespace App\Http\Controllers;

class dbLoopService
{
    public function main($model,$request,$values)
    {
        
        foreach($values as  $value)
        {
            $model->$value=$request->$value;
        }
    }
    
    public function date($model,$request,$values)
    {
        
        foreach($values as $value)
        {
            $model->$value=gmdate( "Y-m-d",strtotime( $request->value ) );
        }
    }

    
}