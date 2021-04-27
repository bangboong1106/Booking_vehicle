<?php

namespace App\Exports;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ReportExport implements FromView, ShouldAutoSize
{
    protected $_repository = null;
    protected $title, $meta, $columns, $showTotalColumns, $entities;


    public function __construct($title, $meta, $columns, $showTotalColumns, $entities)
    {
        $this->title = $title;
        $this->meta = $meta;
        $this->columns = $columns;
        $this->showTotalColumns = $showTotalColumns;
        $this->entities = $entities;
    }

    public function view(): View
    {
        return view('backend.report.general_excel_template.blade.php', [
            'title' => $this->title,
            'meta' => $this->meta,
            'columns' => $this->columns,
            'showTotalColumns' => $this->showTotalColumns,
            'entities' => $this->entities
        ]);
    }
}
