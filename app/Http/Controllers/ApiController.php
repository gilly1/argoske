<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Logic\Users\UserLogic;
//dont remove this comment
 
class ApiController extends Controller
{

    //try passing arrey in th constructor 
    // private $modelLogics = [UserLogic $user,RolesLogic $roles,PasswordLogic $changePassword,TestLogic $test];
    
    function __construct(
        //passing variables in a constructor                
        UserLogic $users,
		//also dont remove this comment
    ) 
    {
        $path = self::route();
        $this->$path = $$path;
        
        // $this->user = $user;
        // $this->roles = $roles;
    }

    public function index($subdomain)
    {
        $net = self::route();
        return $this->$net->table($subdomain);
    }

    public function create($subdomain)
    {
        $net = self::route();

        return $this->$net->view($subdomain,null);
    }

    public function store($subdomain,Request $request)
    {
        $net = self::route();
        return $this->$net->save($subdomain,$request, null,'save');
    }

    public function show($subdomain,$id)
    {
        $net = self::route();
        return $this->$net->show($subdomain,$id);
    }

    public function edit($subdomain,$id)
    {
        $net = self::route();
        return $this->$net->view($subdomain,$id);
    }

    public function update($subdomain, Request $request, $id)
    {
        $net = self::route();
        return $this->$net->save($subdomain,$request, $id,'update');
    }

    public function destroy($subdomain,$id)
    {
        $net = self::route();
        return $this->$net->delete($subdomain,$id);
    }

    public static function route()
    {
        $path = Route::currentRouteName();

        preg_match('/^(.*)\./', $path, $output_array); // adviced by zaq mugo

        return $output_array[1];

    }
}
