<?php

namespace App\Imports;

use App\User;
use App\Helpers\AppHelper;
use App\Notifications\tellAdmin;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ToModelImport implements ToModel,WithHeadingRow,WithBatchInserts,WithChunkReading,WithValidation,WithUpserts,WithEvents
{
    
    private 
    $model,$uniqueBy;

    function __construct($model,$modelName,$uniqueBy,$columns,$rules = [],$errorMessage = []) 
    {
        $this->model = $model;
        $this->rules = $rules;
        $this->columns = $columns;
        $this->uniqueBy = $uniqueBy;
        $this->modelName = $modelName;
        $this->errorMessage = $errorMessage;
        $this->data = [];
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        foreach($this->columns as  $column)
        {
            $this->data[$column] = $row[$column];
        }
       

        return new $this->model(
            $this->data
        );
        

    }
    public function uniqueBy()
    {    
        $this->uniqueBy;
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
         return $this->rules;
    }
    public function customValidationMessages()
    {
        return $this->errorMessage;
    }
    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            AfterImport::class => function(AfterImport $event) {
                $message = AppHelper::logfunction('Excel ',$this->modelName,'Imported');
                Notification::send(AppHelper::admin(),new tellAdmin('info',$message,$this->modelName));
                AppHelper::logs('critical',$message);
            },
                        
        ];
    }
}
