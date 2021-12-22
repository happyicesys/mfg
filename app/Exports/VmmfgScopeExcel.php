<?php

namespace App\Exports;

use App\Models\VmmfgScope;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class VmmfgScopeExcel implements FromView
{

    public function __construct(VmmfgScope $vmmfgScope)
    {
        $this->vmmfgScope = $vmmfgScope;
    }

    public function view(): View
    {
        return view('excel.scope', [
            'scope' => $this->vmmfgScope
        ]);
    }
}
