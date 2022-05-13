<?php

namespace App\Exports\Payments;

use App\User;
use App\Helpers\AppHelper;
use App\Notifications\tellAdmin;
use App\Http\Controllers\MainController;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\BeforeWriting;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithProperties;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class PaymentExport implements FromView, WithColumnFormatting, WithHeadings, WithStyles, WithProperties, WithCustomStartCell, WithEvents
{
    use Exportable;

    public function __construct($model, $format = [], $styles = [], $headings = [], $exportFormat)
    {
        $this->model = $model;
        $this->format = $format;
        $this->styles = $styles;
        $this->headings = $headings;
        $this->exportFormat = $exportFormat;
        $this->route = MainController::route();
    }

    public function view(): View
    {
        return view('argos/payment_table', [
            'data' => $this->model
        ]);
    }

    public function columnFormats(): array
    {
        return $this->format;
    }
    public function styles(Worksheet $sheet)
    {
        return $this->styles;
    }
    public function headings(): array
    {
        return [
            ['Pixelinke Solutions Limited', '', ''],
            ['in-ke.com'],
            [],
            $this->headings,
            ['Total Entries', count($this->model)]
        ];
    }
    public function properties(): array
    {
        return [
            'creator'        => env('APP_NAME', 'Gilly'),
            'lastModifiedBy' => AppHelper::loggedUser(),
            'title'          => ' Export',
            'description'    => 'Latest',
            'subject'        => 'Excel',
            'keywords'       => 'export,spreadsheet',
            'category'       => 'spreadsheet',
            'manager'        => env('APP_NAME', 'Gilly'),
            'company'        => env('APP_NAME', 'Gilly'),
        ];
    }
    // public function drawings()
    // {

    //     $drawing = new Drawing();
    //     $drawing->setName('Company Logo');
    //     $drawing->setDescription('Company Logo');
    //     $drawing->setPath(public_path('images/jpg.png'));
    //     $drawing->setHeight(50);
    //     $drawing->setCoordinates('C1');

    //     return $drawing;
    // }

    public function startCell(): string
    {
        return 'A1';
    }


    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            BeforeWriting::class => function (BeforeWriting $event) {
                $message = AppHelper::logfunction($this->exportFormat, $this->route, 'Exported');
                Notification::send(AppHelper::admin(), new tellAdmin('info', $message, $this->route));
                AppHelper::logs('critical', $message);
            }
        ];
    }
}
