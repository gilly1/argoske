<?php

namespace App\Http\Controllers\Logic;

use App\Model\Dotenv;
use App\Helpers\AppHelper;
use App\Http\Controllers\Logic\MainLogic;
//import class

class GitLogic
{
    const route = 'git';
    const redirect = 'git/git';

    public $model;

    function __construct(
        Dotenv $model
    ){
        $this->model = $model;
    }

    public  function table($subdomain)
    {
        
        $vue = '<gits/>';
        return MainLogic::view($subdomain,null,self::route,self::fields(null),$vue);
        
    }

    public  function view($subdomain,$data)
    {
        return  \Artisan::call($data);
    }
    public  function show($subdomain,$data)
    {
        return self::gitCommands($data,$subdomain);
    }

    public  function save($subdomain,$request,$data,$state)
    {      
        $command = ($request->commands);

        $response = self::gitCommands($command,$subdomain);
            
        if($response){
            return view('errors/404')->with('response',$response)->with('subdomain',$subdomain);
        }
        return redirect(self::redirect);
 
    }

    public static function gitCommands($command,$subdomain)
    {
        $base_path = base_path();

        return shell_exec("cd ".$base_path ." && ".$command);

    }

    public  function delete($subdomain,$data)
    {
        //
    }

    public static  function validated($request){ //variable

        $request->validate([        
			
			
			
			
			//validate
        ]);
    }

    public  function tableColumns() //variable
    {
        return [
            'commands'=>'commands',
			
			//table columns
        ];
    }

    public  function dbfields() //variable
    {
        return [
            'commands'
			
        ];

    }

    public  function fields($forEdit) //variable
    {    
        

        $values = [
            // 'commands'=>
			// [
			// 	'text','text','Commands',12,true,true,'commands','commands','Enter git commands'
			// ]
			
			//input fields
        ];

        return AppHelper::inputValues($values); 
    }
    public function import($subdomain,$request) 
    {  //
    }
    public function export($subdomain,$formatType) 
    {     
        //
    }
}
