<?php

namespace App\Http\Controllers\CrudGenerator;

use App\Model\SideBar;
use App\Helpers\AppHelper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Logic\MainLogic;
use App\Http\Controllers\CrudGenerator\NameParser;
use App\Http\Controllers\CrudGenerator\SyntaxBuilder;
use App\Http\Controllers\CrudGenerator\ExcelGenerator;

class Generator
{
    const route = 'main';
    public function __construct(Filesystem $files = null)
    {
        $this->files = $files;
        $this->composer = app()['composer'];
    }

    public function index($subdomain)
    {  
        
        $vue = '<fields/>';
        return MainLogic::view($subdomain,null,self::route,self::fields(null),$vue);
    }

    public  function fields($forEdit) //variable
    {    
        $values = [
            'name'=>
            [
                'text','text','Model Name',8,true,true,'name','name','Enter model Name'
            ],
            'icon'=>
            [
                'text','text','Icon Name',4,true,true,'icon','icon','Enter Icon Name'
            ]
        ];

        return AppHelper::inputValues($values); 
    }

    public function store($subdomain,Request $request)
    {   
        $model = $request->name;
        $schema = '';
        
        $iterationCount = 0;
        foreach($request->fields as $field)
        {     
            if($field['db_column'] == null)       
            {
                continue;
            }
            if ($iterationCount > 0) $schema .= ',';
            $schema .= $field['field_name'];
            $schema .= ':'.$field['db_column'];
            $schema .= ':'.$field['type'];
            $schema .= isset($field['size']) ? '('.$field['size'].')' : '';
            $schema .= isset($field['validation']) ? ':'.$field['validation'] : '';            
            
            $iterationCount++;
        }

        
        //model Name
        $modelName = Str::studly(class_basename($model));
        //path for the logic class
        $logicPath = $this->qualifyClass($this->getNameInput($modelName));
        //path for the Model Class
        $ModelPath = $this->qualifyClass($this->getNameInput($modelName),true); 
        
        // $this->createMigration($model,$schema,$logicPath);
        
        if ($this->files->exists( "{$logicPath}Logic.php" )) {
            session()->flash('error',$modelName . 'Logic already exists!');
            return redirect()->back()->withInput();
        }

        if ($this->files->exists( "{$ModelPath}.php" )) {
            session()->flash('error',$modelName . ' already exists!');
            return redirect()->back()->withInput();
        }

        $logicnamespace = $this->getNamespace($logicPath);
        $modelnamespace = $this->getNamespace($ModelPath);

        // create directories
        $this->makeDirectory($logicPath);
        $this->makeDirectory($ModelPath);
        //Generate Model
        $this->model($modelnamespace,$modelName,$ModelPath);
        //Generate Logic
        $this->logic($logicnamespace,$modelnamespace,$modelName,$logicPath);
        
        //add permission
        $this->addPermission($modelName);        
        //add link in side bar
        $this->addRouteLink($modelName,$request);
        //add routeArray
        $this->addRouteArray($modelName);
        //add to mainController.php
        $this->addInMainController($modelName,$logicPath);
        
        $this->createMigration($model,$schema,$logicPath,$modelnamespace,$modelName);        
        //migrate and seed
        Artisan::call('migrate', ['--seed' => true]);
        $this->composer->dumpAutoloads();

        session()->flash('success',' Created!');
        return back();
    }
    protected function addPermission($modelName)
    {
        $route = $route = Str::snake(Str::pluralStudly(class_basename($modelName)));        
        $arrayFilename = base_path('App/SystemPermission.php'); // the file to change
        $arraySearch = '//permissions Array'; // the content after which you want to insert new stuff
        $arrayInsert = "\n\t\t\t'{$route}' => [\n\t\t\t\t'view_{$route}',\n\t\t\t\t'create_{$route}',\n\t\t\t\t'edit_{$route}',\n\t\t\t\t'delete_{$route}',\n\t\t\t\t'export_{$route}',\n\t\t\t\t'import_{$route}'\n\t\t\t],"; // code
        $arrayReplace =  $arrayInsert. "\n\t".$arraySearch;
        file_put_contents($arrayFilename, str_replace($arraySearch, $arrayReplace, file_get_contents($arrayFilename)));
    }
    protected function addRouteArray($modelName)
    {
        $route = Str::snake(Str::pluralStudly(class_basename($modelName)));
        $arrayFilename = base_path('routes/routeArray.php'); // the file to change
        $arraySearch = '//dont remove this comment'; // the content after which you want to insert new stuff
        $arrayInsert = "'{$route}/{$route}',"; // code
        $arrayReplace =  $arrayInsert. "\n\t".$arraySearch;
        file_put_contents($arrayFilename, str_replace($arraySearch, $arrayReplace, file_get_contents($arrayFilename)));
    }
    protected function addRouteLink($modelName,$request)
    {

        $arrayFilename = base_path('resources\views\inc\navigation\aside.blade.php'); // the file to change
        $arraySearch = '{{-- add link --}}'; // the content after which you want to insert new stuff
        
        //parent_id
        $parent_id = 0;
        //route
        $route =  Str::snake(Str::pluralStudly(class_basename($modelName)));
        $url = "{$route}.index";
        //title
        $title = ucwords(implode(' ',preg_split('/(?=[A-Z])/', ucfirst(str_replace('_', ' ', $modelName)))));
        //menu_order
        $menu_order = 1;
        //icon
        $icon = $request->icon;
        //description
        //custom_title
        //permission
        $permission = "view_{$route}";

        
        $data = new SideBar;
        $data->parent_id = $parent_id;
        $data->url = $url;
        $data->title = $title;
        $data->menu_order = $menu_order;
        $data->icon = $icon;
        $data->permission = $permission;
        $data->save();

        $arrayInsert = "@can('view_{$route}')\n\t\t\t\t\t<li class=\"nav-item\">\n\t\t\t\t\t<a href=\"{{route('{$route}.index',[\$subdomain])}}\" class=\"nav-link {{Request::is('{$route}/{$route}*') ? 'active' : '' }}\">\n\t\t\t\t\t\t<i class=\"far fa-circle nav-icon\"></i>\n\t\t\t\t\t\t
                            <p>{$title}</p>\n\t\t\t\t\t\t</a>\n\t\t\t\t\t\t</li>\n\t\t\t\t\t\t@endcan";
        $arrayReplace =  $arrayInsert. "\n\t\t\t\t\t\t".$arraySearch;
        file_put_contents($arrayFilename, str_replace($arraySearch, $arrayReplace, file_get_contents($arrayFilename)));
    }
    protected function addInMainController($modelName,$logicPath)
    {
        $route =  Str::snake(Str::pluralStudly(class_basename($modelName)));;
        $filename = base_path('App\Http\Controllers\MainController.php'); // the file to change
        $search = '//dont remove this comment'; // the content after which you want to insert new stuff
        $insert = 'use '.$logicPath.'Logic;'; // code
        $replace =  $insert. "\n".$search;
        file_put_contents($filename, str_replace($search, $replace, file_get_contents($filename)));
        
        $searchInConstructor = '//also dont remove this comment'; // the content after which you want to insert new stuff
        $insertInConstructor = ','.$modelName.'Logic $'.$route; // code
        $replaceInConstructor =  $insertInConstructor. "\n\t\t".$searchInConstructor;
        file_put_contents($filename, str_replace($searchInConstructor, $replaceInConstructor, file_get_contents($filename)));
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
                $route = Str::snake(Str::pluralStudly(class_basename($modelName))),
            ],
            $this->getStub('Model')
        );

        file_put_contents(base_path("{$ModelPath}.php"), $modelTemplate);
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
                $route = Str::snake(Str::pluralStudly(class_basename($modelName))),
            ],
            $this->getStub('Logic')
        );

        file_put_contents(base_path("{$logicPath}Logic.php"), $controllerTemplate);
    }
    protected function createMigration($model,$schema,$logicPath,$modelnamespace,$modelName)
    {
        $table = Str::snake(Str::pluralStudly(class_basename($model)));
        $name = "create_{$table}_table";

        if ($this->files->exists($path = $this->getPath($name))) {
            session()->flash('error',$name . 'Logic already exists!');
            return redirect()->back()->withInput();
        }
        $this->makeDirectory($path);

        // $this->files->put($path, $this->setUp());
        file_put_contents($path, $this->setUp($name,$schema,$logicPath,$table,$modelnamespace,$modelName));

    }
    public function setUp($name,$schema,$logicPath,$table,$modelnamespace,$modelName)
    {
        $stub = $this->getStub('Migration');

        $this->replaceClassName($stub,$name)
            ->replaceSchema($stub,$name,$schema,$logicPath,$table,$modelnamespace,$modelName)
            ->replaceTableName($stub);

        return $stub;

    }
    protected function replaceClassName(&$stub,$name)
    {
        $className = ucwords(Str::camel($name));

        $stub = str_replace('{{class}}', $className, $stub);

        return $this;
    }

    public function replaceSchema(&$stub,$name,$schemas,$logicPath,$table,$modelnamespace,$modelName)
    {
        $this->meta = (new NameParser)->parse($name);
        $schema = $this->parse($schemas);

        $this->addFieldsData($schema,$logicPath,$table,$modelnamespace,$modelName);     


        $schema = (new SyntaxBuilder)->create($schema, $this->meta);


        $stub = str_replace(['{{schema_up}}', '{{schema_down}}'], $schema, $stub);

        return $this;

    }
    protected function addFieldsData($schema,$logicPath,$table,$modelnamespace,$modelName)
    {
        $arrayFilename = base_path("{$logicPath}Logic.php"); // the file to change
        
        $arraySearchTableColumns = '//table columns'; // the content after which you want to insert new stuff
        $arrayInsertTableColumns = "";

        $arraySearchDbFields = '//db fields'; // the content after which you want to insert new stuff
        $arrayInsertDbFields = "";

        $arrayInsertDbFieldsExcel = "";

        $arraySearchDates = '//dates'; // the content after which you want to insert new stuff
        $arrayInsertDates = "";
        
        $arraySearchHeadings = '//headings'; // the content after which you want to insert new stuff
        $arrayInsertHeadings = "";
        
        $arraySearchInputFields = '//input fields'; // the content after which you want to insert new stuff
        $arrayInsertInputFields = "";

        $arraySearchExcelMain = '//save from excel'; // the content after which you want to insert new stuff
        $arrayInsertExcel = "";
        
        $arraySearchModelImport = '//import class'; // the content after which you want to insert new stuff
        $arrayInsertModelImport = "";

        $arraySearchModelRelationship = '//relationships'; // the content after which you want to insert new stuff
        $arrayInsertModelRelationship = "";

        
        $arraySearchSelectValue = ""; // for select value incase it does not exist
        $arrayInsertSelectValue = "";

        $iterationCount = 0;
        $iterationDateCount = 0;
        $modelPath = null;

        foreach($schema as $schema)
        {
            
            if($schema['type'] == 'foreign')
            {
                continue;
            }
            if(is_array($schema['options']))
            {
                $arraySearchValidate = '//validate'; // the content after which you want to insert new stuff
                $arrayInsertValidate = "";
                $type = $schema['type'];
                foreach($schema['options'] as $option)
                {
                    $max = isset($schema[0]['arguments']) ? "'max:$schema[0]['arguments']'" : "";
                    $required = isset($option['nullable']) ? "" : "'required'";
                    $arrayInsertValidate = $arrayInsertValidate."'{$schema['name']}' => [{$required} , {$max}],";
                }
                $arrayReplaceValidate =  $arrayInsertValidate. "\n\t\t\t".$arraySearchValidate;
                file_put_contents($arrayFilename, str_replace($arraySearchValidate, $arrayReplaceValidate, file_get_contents($arrayFilename)));
            }
            
            $name = ucfirst(str_replace('_', ' ', $schema['field_name']));
            $with = explode('_', $schema['name'])[0];//get the relationship Class
            $selectModel = ucfirst($with);
            $relationshipName = lcfirst($with);
            if ($iterationCount < 5){
                if(strpos($schema['type'],"unsignedBigInteger") !== false){
                    $arrayInsertTableColumns .= "'{$name}'=>['relationship','{$relationshipName}','name'],\n\t\t\t";
                }else{
                    $arrayInsertTableColumns .= "'{$name}'=>'{$schema['name']}',\n\t\t\t";
                }
            }
                                    
            if ($iterationCount > 0) $arrayInsertDbFields .= ',';
            $arrayInsertDbFields .="'{$schema['name']}'";
            
            if ($iterationCount > 0) $arrayInsertDbFieldsExcel .= ',';
            $arrayInsertDbFieldsExcel .=$name;

            if ($iterationCount > 0) $arrayInsertHeadings .= ',';
            $arrayInsertHeadings .="'{$name}'";

            $arrayInsertExcel .= "'{$schema['name']}'=>\$row['{$schema['field_name']}'],\n\t\t\t";
            
            $text = 'text';
            $id = $schema['name'];
            $col = 6;

            if(strpos($schema['type'],"date") !== false)
            {                
                if ($iterationDateCount > 0) $arrayInsertDates .= ',';
                $arrayInsertDates .="'{$schema['name']}'";
                $id = 'date'.$iterationDateCount;

                $text = $schema['type'];
                $iterationDateCount++;
            }
            if(strpos($schema['type'],"ext") !== false)
            {
                $text = 'textarea';
                $col = 12;
                $schema['type'] = 'text';
            }
            if(strpos($schema['type'],"unsignedBigInteger") !== false)
            {  
                //display relationship name on table 


                // $with = explode('_', $schema['name'])[0];//get the relationship Class
                // $selectModel = ucfirst($with);
                // $relationshipName = lcfirst($with);
                $arrayInsertInputFields = $arrayInsertInputFields."'{$schema['name']}'=>\n\t\t\t[\n\t\t\t\t'select','select','Choose {$name}',{$col},true,true,'{$id}','{$id}','Select {$schema['name']}','',{$selectModel}::all(),isset(\$forEdit) ? {$modelName}::with('{$relationshipName}')->where('id',\$forEdit->id)->first()->{$relationshipName} : ''\n\t\t\t],\n\t\t\t";
                
                $result = $this->rglob(base_path('app/Model') . "/{$modelName}.php");
                if(count($result)>0){
                    $modelPath = $result[0];                    
                    $removeBasePath = str_replace(base_path('app'), 'use App', $result[0] ); 
                    $replaceSlashes = str_replace('/', '\\', $removeBasePath ); 
                    $replaceDotPhp = str_replace('.php', '', $replaceSlashes ); 
                    $arrayInsertModelImport .= str_replace(class_basename($replaceDotPhp), $selectModel, $replaceDotPhp ).';'; 
                                 
                }
                
                $arraySearchSelectValue = "'{$name}'=>'{$schema['name']}',"; // the content after which you want to insert new stuff
                $arrayInsertSelectValue .= "'{$with}'=>['relationship','{$relationshipName}','name'],";
                $arrayReplaceSelectValue =  $arrayInsertSelectValue;
                file_put_contents($arrayFilename, str_replace($arraySearchSelectValue, $arrayReplaceSelectValue, file_get_contents($arrayFilename)));
                $arraySearchSelectValue = "";
                $arrayInsertSelectValue = "";

                $arrayInsertModelRelationship .= "public function {$relationshipName}()\n\t{\n\t\treturn \$this->belongsTo({$selectModel}::class,'{$schema['name']}');\n\t}\n";
            }else{
                $arrayInsertInputFields = $arrayInsertInputFields."'{$schema['name']}'=>\n\t\t\t[\n\t\t\t\t'{$text}','{$schema['type']}','{$name}',{$col},true,true,'{$id}','{$schema['name']}','Enter {$name}'\n\t\t\t],\n\t\t\t";                
            }

            
            $iterationCount++;
        }

        if($modelPath)
        {            
            $arrayReplaceModelImport =  $arrayInsertModelImport. "\n".$arraySearchModelImport;
            file_put_contents($modelPath, str_replace($arraySearchModelImport, $arrayReplaceModelImport, file_get_contents($modelPath)));
            
            $arrayReplaceModelImport =  $arrayInsertModelImport. "\n".$arraySearchModelImport;
            file_put_contents($arrayFilename, str_replace($arraySearchModelImport, $arrayReplaceModelImport, file_get_contents($arrayFilename)));
            
            $arrayReplaceModelRelationship =  $arrayInsertModelRelationship. "\n\t".$arraySearchModelRelationship;
            file_put_contents($modelPath, str_replace($arraySearchModelRelationship, $arrayReplaceModelRelationship, file_get_contents($modelPath)));
        }
        $arrayReplaceTableColumns =  $arrayInsertTableColumns. "\n\t\t\t".$arraySearchTableColumns;
        file_put_contents($arrayFilename, str_replace($arraySearchTableColumns, $arrayReplaceTableColumns, file_get_contents($arrayFilename)));

        $arrayReplaceDbFields =  $arrayInsertDbFields. "\n\t\t\t";
        file_put_contents($arrayFilename, str_replace($arraySearchDbFields, $arrayReplaceDbFields, file_get_contents($arrayFilename)));

        // $arrayReplaceSelectValue =  $arrayInsertSelectValue. "\n\t\t\t";
        // file_put_contents($arrayFilename, str_replace($arraySearchSelectValue, $arrayReplaceSelectValue, file_get_contents($arrayFilename)));
                
        $arrayReplaceHeadings =  $arrayInsertHeadings. "\n\t\t\t".$arraySearchHeadings;
        file_put_contents($arrayFilename, str_replace($arraySearchHeadings, $arrayReplaceHeadings, file_get_contents($arrayFilename)));
        
        $arrayReplaceInputFields =  $arrayInsertInputFields. "\n\t\t\t".$arraySearchInputFields;
        file_put_contents($arrayFilename, str_replace($arraySearchInputFields, $arrayReplaceInputFields, file_get_contents($arrayFilename)));
        
        
        $arrayModelFilename = base_path("App\Imports\Models.php"); // the file to change
        $arrayInsertExcelMain = "elseif(\$route == '{$table}')\n\t\t{\n\t\t\treturn {$modelName}::firstOrCreate([\n\t\t\t\t{$arrayInsertExcel}\n\t\t\t]);\n\t\t}";
        $arrayReplaceExcelMain =  $arrayInsertExcelMain. "\n\t\t\t".$arraySearchExcelMain;
        file_put_contents($arrayModelFilename, str_replace($arraySearchExcelMain, $arrayReplaceExcelMain, file_get_contents($arrayModelFilename)));
       
        $arraySearchImportClass = '//add import class'; // the content after which you want to insert new stuff        
        $arrayInsertImportClass = "use {$modelnamespace}\\{$modelName};";
        $arrayReplaceImportClass =  $arrayInsertImportClass. "\n".$arraySearchImportClass;
        file_put_contents($arrayModelFilename, str_replace($arraySearchImportClass, $arrayReplaceImportClass, file_get_contents($arrayModelFilename)));
        
        $arrayReplaceModelFillables = "";
        $arrayModelFilename = base_path($modelnamespace.'/'.$modelName.'.php'); // the file to change
        file_put_contents($arrayModelFilename, str_replace($arraySearchDbFields, $arrayReplaceDbFields, file_get_contents($arrayModelFilename)));
        
        $arrayReplaceDates =  $arrayInsertDates. "\n\t\t\t";
        file_put_contents($arrayModelFilename, str_replace($arraySearchDates, $arrayReplaceDates, file_get_contents($arrayModelFilename)));

        
        $route = Str::snake(Str::pluralStudly(class_basename($modelName)));

        return Excel::store(new ExcelGenerator(explode(",",$arrayInsertDbFieldsExcel)), $route.'.xlsx','public_path');
    }
    protected function replaceTableName(&$stub)
    {
        $table = $this->meta['table'];

        $stub = str_replace('{{table}}', $table, $stub);

        return $this;
    }

    private function parse($schema)
    {

        $fields = $this->splitIntoFields($schema);

        foreach ($fields as $field) {
            $segments = $this->parseSegments($field);

            // return $segments;
            //format my data needs to be in;

            if ($this->fieldNeedsForeignConstraint($segments)) {
                unset($segments['options']['foreign']);

                // If the user wants a foreign constraint, then
                // we'll first add the regular field.
                $this->addField($segments);

                // And then add another field for the constraint.
                $this->addForeignConstraint($segments);

                continue;
            }

            $this->addField($segments);
        }

        return $this->schema;

    }    
    private function splitIntoFields($schema)
    {
        return preg_split('/,\s?(?![^()]*\))/', $schema);
    }
    // private function parseSegments($field)
    // {
    //     $segments = explode(':', $field);

    //     $name = array_shift($segments);
    //     $type = array_shift($segments);
    //     $arguments = [];
    //     $options = $this->parseOptions($segments);

    //     // Do we have arguments being used here?
    //     // Like: string(100)
    //     if (preg_match('/(.+?)\(([^)]+)\)/', $type, $matches)) {
    //         $type = $matches[1];
    //         $arguments = explode(',', $matches[2]);
    //     }

    //     return compact('name', 'type', 'arguments', 'options');
    // }
    
    private function parseSegments($field)
    {
        $segments = explode(':', $field);

        $field_name = array_shift($segments);
        $name = array_shift($segments);
        $type = array_shift($segments);
        $arguments = [];
        $options = $this->parseOptions($segments);

        // Do we have arguments being used here?
        // Like: string(100)
        if (preg_match('/(.+?)\(([^)]+)\)/', $type, $matches)) {
            $type = $matches[1];
            $arguments = explode(',', $matches[2]);
        }

        return compact('field_name','name', 'type', 'arguments', 'options');
    }
    private function parseOptions($options)
    {
        if (empty($options)) return [];

        foreach ($options as $option) {
            if (Str::contains($option, '(')) {
                preg_match('/([a-z]+)\(([^\)]+)\)/i', $option, $matches);

                $results[$matches[1]] = $matches[2];
            } else {
                $results[$option] = true;
            }
        }

        return $results;
    }
    
    function rglob($pattern, $flags = 0) {
        $files = glob($pattern, $flags); 
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, $this->rglob($dir.'/'.basename($pattern), $flags));
        }
        return $files;        

    }
    private function fieldNeedsForeignConstraint($segments)
    {
        return array_key_exists('foreign', $segments['options']);
    }
    private function addField($field)
    {
        $this->schema[] = $field;

        return $this;
    }
    private function addForeignConstraint($segments)
    {
        $string = sprintf(
            "%s:%s:foreign:references('id'):on('%s')",
            $segments['field_name'],
            $segments['name'],
            $this->getTableNameFromForeignKey($segments['name'])
        );

        $this->addField($this->parseSegments($string));
    }
    private function getTableNameFromForeignKey($key)
    {
        return Str::plural(str_replace('_id', '', $key));
    }
    protected function getStub($type)
    {
        return file_get_contents(__DIR__ . '/theStubs/'.$type.'.stub');
    }
    protected function getNameInput($model)
    {
        return trim($model);
    }  
    protected function rootNamespace()
    {
        return 'App';
    }
    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    } 
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Controllers\Logic';
    }
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname(base_path($path)))) {
            $this->files->makeDirectory(dirname(base_path($path)), 0777, true, true);
        }

        return $path;
    }
    protected function getPath($name)
    {
        $path = base_path().'/database/migrations/'.date('Y_m_d_His').'_'.$name.'.php';

        return $path;
    }
}
