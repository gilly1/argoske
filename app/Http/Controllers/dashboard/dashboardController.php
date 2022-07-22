<?php

namespace App\Http\Controllers\dashboard;

use Dotenv\Dotenv;
use Illuminate\Support\Str;
use App\CrudGenerator\Generator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Packages\Brotzka\DotenvEditor;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;

class dashboardController extends Controller
{
    
    public function dashboard($subdomain)
    {
        return view('dashboard/dashboard')->with('subdomain',$subdomain);
    }
    public function main_dashboard()
    {
        return view('dashboard/dashboard');
    }
    
    public static function copy($subdomain,$companyName,$dbhost,$dbUser,$dbPass)
    {    
        ini_set('max_execution_time', 3000);
        
        //hash database
        $database = self::createHash($subdomain);

        //store company data

        //change subdomain to accept domain and filter to look like in-ke.com wizag.biz pixelinke.domain.com
        $domainName = env('APP_URL_NAME','tenancy.test');
        
        // if($domain != env('APP_URL_NAME','tenancy.test')){
        //     return redirect()->back()->with('error','Kindly contact the Administrator') ;
        // }
        $domain = $subdomain.'.'.$domainName;

        $path = base_path($domain);
        if(!\File::exists($path)) {
    
            \File::makeDirectory($path, 0755, true, true);
        }

        $file = base_path($domain.'\.env');
        if(!\File::exists($file)) {
            \File::copy(base_path('.env.empty'), base_path($domain.'\.env'));
        }
        $subdomain = 'http://'.$domain;
    
        $env = new DotenvEditor($file);
        if(!$env->keyExists("TESTKEY2")) {
            $env->addData(
                self::dotenvValues($companyName,'http://tenancy.test',$domainName,$subdomain,$dbhost,$database,$dbUser,$dbPass)
            );
        }  

        
        
        // self::changeDotEnv($domain);


        //create database        
        DB::statement('CREATE DATABASE ' . $database);
        
        // first change the configuration        
        $defaults = Config::get('database.connections.' . env('DB_CONNECTION', 'mysql'));
        $defaults['database'] = $database;
        $defaults['host'] = $dbhost;
        $defaults['username'] = $dbUser;
        $defaults['password'] = $dbPass;
        Config::set('database.connections.' . $database, $defaults);
        
        DB::disconnect(); 
        Config::set('database.connections.mysql', $defaults);  
        DB::reconnect();

        //migrate and seed
        Artisan::call('migrate', ['--database' => $database, '--force' => true, '--seed' => true]);
     
        // self::changeDotEnv('');
    
        return 'go and confirm';
    }

    private static function changeDotEnv($domain)
    {
        
        $dotenv = Dotenv::createImmutable(base_path(), $domain.'\.env');
    
        try {
            $dotenv->load();
        } catch (\Dotenv\Exception\InvalidPathException $e) {dd($e);
            // No custom .env file found for this domain
            $dotenv = Dotenv::createImmutable(base_path(), '.env');
        }
    }

    private static function dotenvValues($name,$domain,$domainName,$subdomain,$dbhost,$dbName,$dbUser,$dbPass)
    {
        return [
            'APP_NAME' => $name,
            'APP_ENV' => 'local',
            'APP_KEY' => 'base64:PnWXc5OWnzPj725RvQ29MJoTaYtkRW9DxYhObX52xO8',
            'APP_DEBUG' => 'false',
            'APP_URL' => $domain,
            'APP_URL_NAME' => $domainName,
            'SUB_APP_URL_NAME' => $subdomain,

            'LOG_CHANNEL' => 'stack',

            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $dbhost,
            'DB_PORT' => '3306',
            'DB_DATABASE' => $dbName,
            'DB_USERNAME' => $dbUser,
            'DB_PASSWORD' => $dbPass,

            'BROADCAST_DRIVER' => 'log',
            'CACHE_DRIVER' => 'file',
            'QUEUE_CONNECTION' => 'database',
            'SESSION_DRIVER' => 'file',
            'SESSION_LIFETIME' => '120',

            'REDIS_HOST' => '127.0.0.1',
            'REDIS_PASSWORD' => 'null',
            'REDIS_PORT' => '6379',

            'MAIL_MAILER' => 'smtp',
            'MAIL_HOST' => 'smtp.mailtrap.io',
            'MAIL_PORT' => '2525',
            'MAIL_USERNAME' => 'null',
            'MAIL_PASSWORD' => 'null',
            'MAIL_ENCRYPTION' => 'null',
            'MAIL_FROM_ADDRESS' => 'null',
            'MAIL_FROM_NAME' => '"${APP_NAME}"',

            'AWS_ACCESS_KEY_ID' => '',
            'AWS_SECRET_ACCESS_KEY' => '',
            'AWS_DEFAULT_REGION' => 'us-east-1',
            'AWS_BUCKET' => '',

            'PUSHER_APP_ID' => '',
            'PUSHER_APP_KEY' => '',
            'PUSHER_APP_SECRET' => '',
            'PUSHER_APP_CLUSTER' => 'mt1',

            'MIX_PUSHER_APP_KEY' => '"${PUSHER_APP_KEY}"',
            'MIX_PUSHER_APP_CLUSTER' => '"${PUSHER_APP_CLUSTER}"'
        ];
    }

    private static function createHash($name)
    {
        $hash = Str::random(2) . time();
        $prefix = str_replace('-', '', self::str_slug($name));
        $start = rand(0, strlen($prefix) - 6);

        return 'gil_tenancy_test_' . substr($prefix, $start, $start + 5) . $hash;
    }

    private static function str_slug($title, $separator = '-', $language = 'en')
    {
        return Str::slug($title, $separator, $language);
    }
}
