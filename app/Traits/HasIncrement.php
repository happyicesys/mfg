<?php

namespace App\Traits;
use Carbon\Carbon;

trait HasIncrement
{
    public function getIncrementByYearMonth($currentNumber = null, $init = '000')
    {
        $yearMonthStr = Carbon::today()->format('ym');
            if ($currentNumber == null) { // if runningNumber is null then initial current year + month + 001, eg 2205001
                $currentNumber = (int) $yearMonthStr . $init;
            } else {
                if (substr($currentNumber, 0, 4) != $yearMonthStr) { // if current running number year is not current year
                    $currentNumber = (int) $yearMonthStr . $init; // // re-initial current year + month + 001, eg 2205001
                }
            }
        return $currentNumber + 1;
    }
}
