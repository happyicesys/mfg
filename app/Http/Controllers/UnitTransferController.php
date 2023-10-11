<?php

namespace App\Http\Controllers;

use App\Models\UnitTransferDestination;
use App\Models\VmmfgUnit;
use Illuminate\Http\Request;

class UnitTransferController extends Controller
{
    public function store(Request $request)
    {
        // dd($request->all());
        VmmfgUnit::create([
            'current' => $request->current,
            'destination' => null,
            'origin' => $request->current,
            'origin_ref_id' => $request->id,
            'origin_vmmfg_job_json' => $request->vmmfg_job_json,
            'origin_vmmfg_scope_json' => $request->vmmfg_scope_json,
            'unit_no' => $request->unit_no ? $request->unit_no : 0,
            'vmmfg_job_id' => null,
            'vmmfg_job_json' => null,
            'serial_no' => $request->serial_no,
            'vmmfg_scope_id' => null,
            'vmmfg_scope_json' => null,
            'vend_id' => $request->vend_id,
            'completion_date' => null,
            'model' => $request->model,
            'order_date' => null,
            'refer_completion_unit_id' => null,
            'code' => $request->code,
        ]);

        return true;
    }

    public function delete($vmmfgUnitId)
    {
        $vmmfgUnit = VmmfgUnit::findorFail($vmmfgUnitId);
        $vmmfgUnit->update([
            'destination' => null,
        ]);

        return true;
    }
}
