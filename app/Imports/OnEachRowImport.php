<?php

namespace App\Imports;

use App\Imports\Models;
use App\Helpers\AppHelper;
use Maatwebsite\Excel\Row;
use App\Notifications\tellAdmin;
use App\Http\Controllers\MainController;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class OnEachRowImport implements OnEachRow,WithHeadingRow,WithBatchInserts,WithChunkReading,WithValidation,WithUpserts,WithEvents
{
    
    private 
    $route,$models;

    function __construct() 
    {
        $this->route = MainController::route();
        $this->models = new Models;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row      = $row->toArray();


        $this->models->models($row,$this->route);
        

    }
    public function uniqueBy()
    {    
        return $this->models->uniqueBy($this->route);
    }
    public function batchSize(): int
    {
        return 500;
    }
    public function chunkSize(): int
    {
        return 500;
    }
    public function rules(): array
    {
        return  $this->models->validation($this->route);
    }
    public function customValidationMessages()
    {
        return  $this->models->validationError($this->route);
    }
    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            AfterImport::class => function(AfterImport $event) { 
                $message = AppHelper::logfunction('Excel ',$this->route,'Imported');
                Notification::send(AppHelper::admin(),new tellAdmin('info',$message,$this->route));
                AppHelper::logs('critical',$message);
            },
                        
        ];
    }

    
}
