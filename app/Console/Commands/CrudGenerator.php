<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CrudGenerator extends Command
{
    protected $files;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:generator
    {name : Class (singular) for example User}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD operations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = app()['composer'];
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //model Name
        $modelName = Str::studly(class_basename($this->argument('name')));

        //path for the logic class
        $logicPath = $this->qualifyClass($this->getNameInput());
        //path for the Model Class
        $ModelPath = $this->qualifyClass($this->getNameInput(),true);

        $logicnamespace = $this->getNamespace($logicPath);
        $modelnamespace = $this->getNamespace($ModelPath);
         
        // Next, we will generate the path to the location where this class file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($logicPath);
        $this->makeDirectory($ModelPath);

        
        $this->model($modelnamespace,$modelName,$ModelPath);
        $this->line("<info>Model {$modelName} Generted Successfully</info>"); 
        $this->logic($logicnamespace,$modelnamespace,$modelName,$logicPath);
        $this->line("<info>Logic {$modelName}Logic Generted Successfully</info>"); 
                
        //add permission
        $route = strtolower($modelName);
        $permissionFile = fopen(base_path('App/SystemPermissionArray.php'), "c");
        fseek($permissionFile, -6, SEEK_END);
        fwrite($permissionFile, "\n\t\t\t'{$route}' => [\n\t\t\t\t'view_{$route}',\n\t\t\t\t'create_{$route}',\n\t\t\t\t'edit_{$route}',\n\t\t\t\t'delete_{$route}',\n\t\t\t\t'export',\n\t\t\t\t'import'\n\t\t\t],\n\t\t]];\n");
        fclose($permissionFile);
        
        $this->line("<info>Permision for {$route} Added Successfully</info>"); 
          
        //add routes      
        // $file = fopen(base_path('routes/routeArray.php'), "c");
        // fseek($file, -3, SEEK_END);
        // fwrite($file, " \t'{$route}/{$route}', \n ];");
        // fclose($file);
        
        $arrayFilename = 'routes/routeArray.php'; // the file to change
        $arraySearch = '//dont remove this comment'; // the content after which you want to insert new stuff
        $arrayInsert = "'{$route}/{$route}',"; // code
        $arrayReplace =  $arrayInsert. "\n\t".$arraySearch;
        file_put_contents($arrayFilename, str_replace($arraySearch, $arrayReplace, file_get_contents($arrayFilename)));

        $this->line("<info>Route {$route}/{$route} Added Successfully</info>"); 

        $filename = 'App\Http\Controllers\MainController.php'; // the file to change
        $search = '//dont remove this comment'; // the content after which you want to insert new stuff
        $insert = 'use '.$logicPath.'Logic;'; // code
        $replace =  $insert. "\n".$search;
        file_put_contents($filename, str_replace($search, $replace, file_get_contents($filename)));
        
        $searchInConstructor = '//also dont remove this comment'; // the content after which you want to insert new stuff
        $insertInConstructor = ','.$modelName.'Logic $'.$route; // code
        $replaceInConstructor =  $insertInConstructor. "\n\t\t".$searchInConstructor;
        file_put_contents($filename, str_replace($searchInConstructor, $replaceInConstructor, file_get_contents($filename)));
   
        // \File::append(base_path('App\Http\Controllers\MainController.php'), "use ".$logicPath."Logic;\n");
        
        $this->line("<info>{$modelName}Logic imported Successfully</info>"); 
        $this->createMigration();
        $this->composer->dumpAutoloads();
    }

    protected function getStub($type)
    {
        return file_get_contents(base_path("theStubs/$type.stub"));
    }
    protected function model($modelnamespace,$modelName,$ModelPath)
    {
        $modelTemplate = str_replace(
            [
                '{{modelnamespace}}',
                '{{modelName}}',
                '{{route}}'
            ],
            [
                $modelnamespace,
                $modelName,
                strtolower($modelName)
            ],
            $this->getStub('Model')
        );

        file_put_contents("{$ModelPath}.php", $modelTemplate);
    }
    protected function logic($logicnamespace,$modelnamespace,$modelName,$logicPath)
    {
        $controllerTemplate = str_replace(
            [
                '{{logicnamespace}}',
                '{{modelnamespace}}',
                '{{modelName}}',
                '{{route}}'
            ],
            [
                $logicnamespace,
                $modelnamespace,
                $modelName,
                strtolower($modelName)
            ],
            $this->getStub('Logic')
        );

        file_put_contents("{$logicPath}Logic.php", $controllerTemplate);
    }
    
    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    } 
    protected function qualifyClass($name,$model = null)
    {
        $name = ltrim($name, '\\/');

        $name = str_replace('/', '\\', $name);

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        if($model)
        {
            return $this->qualifyClass(
                trim($rootNamespace, '\\').'\\Model\\'.$name
            );
        }else{
            return $this->qualifyClass(
                $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name
            );
        }

    }    
    protected function rootNamespace()
    {
        return $this->laravel->getNamespace();
    }
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Controllers\Logic';
    }
    protected function getNameInput()
    {
        return trim($this->argument('name'));
    }
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }
    protected function createMigration()
    {
        $table = Str::snake(Str::pluralStudly(class_basename($this->argument('name'))));

        $this->call('make:migration', [
            'name' => "create_{$table}_table",
            '--create' => $table,
        ]);
    }

    protected function getPath($name)
    {
        $path = ($this->option('path'))
            ? base_path().$this->option('path').'/'.date('Y_m_d_His').'_'.$name.'.php'
            : base_path().'/database/migrations/'.date('Y_m_d_His').'_'.$name.'.php';

        return $path;
    }
}
