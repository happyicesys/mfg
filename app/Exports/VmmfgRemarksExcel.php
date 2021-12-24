<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class VmmfgRemarksExcel implements FromView
{
    protected $scope;
    protected $filters;


    public function __construct($scope, $filters)
    {
        $this->scope = $scope;
        $this->filters = $filters;
    }

    public function view(): View
    {
        return view('excel.remarks', [
            'scope' => $this->scope,
            'filters' => $this->filters,
        ]);
    }
}
