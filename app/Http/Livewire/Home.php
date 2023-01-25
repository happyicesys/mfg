<?php

namespace App\Http\Livewire;

use App\Models\VmmfgJob;
use App\Models\VmmfgUnit;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Carbon\Carbon;
use DB;
use Livewire\Component;

class Home extends Component
{
    public $firstLoadingChart = true;

    public function render()
    {
        $thisYear = Carbon::now()->year;
        $years = [
            Carbon::now()->year,
            Carbon::now()->subYear()->year,
        ];
        $months = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'July',
            8 => 'Aug',
            9 => 'Sept',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec',
        ];
        $dataArr = [];

        foreach($years as $year) {
            // $dataArr[$year] = [
            //     'totalOrder' => 0,
            //     'totalComplete' => 0,
            // ];
            $jobs = VmmfgUnit::select(
                            DB::raw('MONTH(order_date) AS order_month'),
                            DB::raw('MONTH(completion_date) AS completion_month'),
                            DB::raw('YEAR(order_date) AS order_year'),
                            DB::raw('YEAR(completion_date) AS completion_year'),
                            'refer_completion_unit_id'
                        )
                        ->where(function($query) use ($year) {
                            $query->whereYear('order_date', '=', $year)
                                    ->orWhereYear('completion_date', '=', $year);
                        })
                        // dd($jobs->toSql());
                        ->get();


            foreach($months as $index => $month) {
                $dataArr[$year][$index] = [
                    'year' => $year,
                    'name' => $month,
                    'order' => 0,
                    'completion' => 0,
                ];
                foreach($jobs as $job) {
                    if($job->order_month === $index and $job->order_year === $year) {
                        $dataArr[$year][$index]['order'] += 1;
                        // $dataArr[$year]['totalOrder'] += 1;
                    }
                    if($job->completion_month === $index and $job->refer_completion_unit_id === null and $job->completion_year === $year) {
                        $dataArr[$year][$index]['completion'] += 1;
                        // $dataArr[$year]['totalComplete'] += 1;
                    }
                }
            }
        }



        return view('livewire.home', [
            'years' => $years,
            'dataArr' => $dataArr,
        ]);
    }
}
