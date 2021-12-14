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

        $jobs = VmmfgUnit::leftJoin('vmmfg_jobs', 'vmmfg_jobs.id', '=', 'vmmfg_units.vmmfg_job_id')
                    ->select(DB::raw('COUNT(vmmfg_units.id) AS count'), DB::raw('MONTH(order_date) AS order_month'), DB::raw('MONTH(vmmfg_jobs.completion_date) AS completion_month'))
                    ->where(function($query) use ($thisYear) {
                        $query->whereYear('order_date', '=', $thisYear)->orWhereYear('completion_date', '=', $thisYear);
                    })
                    ->groupBy(DB::raw('MONTH(order_date)'))
                    ->groupBy(DB::raw('MONTH(vmmfg_jobs.completion_date)'))
                    ->get();

        foreach($months as $index => $month) {
            $dataArr[$index] = [
                'name' => $month,
                'order' => 0,
                'completion' => 0,
            ];
            foreach($jobs as $job) {
                if($job->order_month === $index) {
                    $dataArr[$index]['order'] = $job->count;
                }
                if($job->completion_month === $index) {
                    $dataArr[$index]['completion'] = $job->count;
                }
            }
        }

        $multiLineChartModel = LivewireCharts::multiLineChartModel()
                //->setTitle('Expenses by Type')
                ->setAnimated($this->firstLoadingChart)
                ->multiLine()
                ->setDataLabelsEnabled(true)
                ->sparklined()
                ->withDataLabels()
                ->withLegend()
                ->setXAxisCategories($months);
                // ->setColors(['#b01a1b', '#d41b2c', '#ec3c3b', '#f66665']);
                foreach($dataArr as $data) {
                    $multiLineChartModel = $multiLineChartModel->addSeriesPoint('Start', $data['name'], $data['order']);
                    $multiLineChartModel = $multiLineChartModel->addSeriesPoint('Completion', $data['name'], $data['completion']);
                }

        return view('livewire.home', [
            'year' => $thisYear,
            'dataArr' => $dataArr,
            'multiLineChartModel' => $multiLineChartModel
        ]);
    }
}
