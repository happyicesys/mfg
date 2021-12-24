<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VmmfgRemarksExcel implements FromView, WithColumnWidths, WithStyles
{
    protected $scope;
    protected $filters;
    private $cells = ['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];


    public function __construct($scope, $filters)
    {
        $this->scope = $scope;
        $this->filters = $filters;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('B')->getAlignment()->setWrapText(true);

        foreach($this->cells as $cell) {
            $sheet->getStyle($cell)->getAlignment()->setWrapText(true);
        }
    }

    public function columnWidths(): array
    {
        $cellArr['A'] = 18;
        $cellArr['B'] = 60;

        foreach($this->cells as $cell) {
            $cellArr[$cell] = 15;
        }

        return $cellArr;
    }

    public function view(): View
    {
        return view('excel.remarks', [
            'scope' => $this->scope,
            'filters' => $this->filters,
        ]);
    }
}
