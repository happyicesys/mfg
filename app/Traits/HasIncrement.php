<?php

namespace App\Traits;
use Carbon\Carbon;

trait HasIncrement
{
    public function getIncrementByYearMonth($currentNumber = null)
    {
        $yearMonthStr = Carbon::today()->format('ym');
            if ($currentNumber == null) { // if runningNumber is null then initial current year + month + 001, eg 2205001
                $currentNumber = (int) $yearMonthStr . '000';
            } else {
                if (substr($currentNumber, 0, 4) != $yearMonthStr) { // if current running number year is not current year
                    $currentNumber = (int) $yearMonthStr . '000'; // // re-initial current year + month + 001, eg 2205001
                }
            }
        return $currentNumber + 1;
    }
}
