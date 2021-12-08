<?php

namespace App\Traits;
use Carbon\Carbon;

trait HasDateControl
{
    public function onPrevDateClicked($model)
    {
        $date = '';
        if($this[$model]) {
            $date = Carbon::parse($this[$model]);
        }else {
            $date = Carbon::now();
        }
        $this[$model] = $date->addDay()->toDateString;
    }

    public function onNextDateClicked($model)
    {
        dd($model);
        $date = '';
        if($this[$model]) {
            $date = Carbon::parse($this[$model]);
        }else {
            $date = Carbon::now();
        }
        $this[$model] = $date->subDay()->toDateString;
    }
}
