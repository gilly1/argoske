<?php

namespace App\Http\Controllers\CrudGenerator;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithProperties;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelGenerator implements WithHeadings,WithProperties,WithStyles
{
    use Exportable;
    
    public function __construct($headings = [])
    {
        $this->headings = $headings;
    }
    public function headings(): array
    {
        return [
            $this->headings,
        ];
    }
    public function styles(Worksheet $sheet)
    {
        return [ 
            '1'   => ['font' => ['bold' => true]],
        ];
        
    }
    public function properties(): array
    {
        return [
            'creator'        => 'Gilly',
            'lastModifiedBy' => 'Gilbert Muia',
            'title'          => ' Export',
            'description'    => 'Latest',
            'subject'        => 'Excel',
            'keywords'       => 'export,spreadsheet',
            'category'       => 'spreadsheet',
            'manager'        => 'Gilbert Muia',
            'company'        => 'Pixelinke Solutions',
        ];
    }
}