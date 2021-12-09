<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\User;
use App\Models\VmmfgJob;
use App\Models\VmmfgTask;
use App\Models\VmmfgUnit;
use App\Traits\HasDateControl;
use DB;
use Livewire\Component;
use Livewire\WithPagination;
use PDF;
use Excel;

class VmmfgDailyreport extends Component
{
    use HasDateControl, WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $itemPerPage = 100;
    public $sortKey = '';
    public $sortAscending = true;
    public $showEditModal = false;
    public $selected = [];
    public $filters = [
        'is_done' => '',
        'is_checked' => '',
        'user_id' => '',
        'date_from' => '',
        'date_to' => '',
        'job_id' => '',
        'unit_id' => '',
    ];
    public $units;
    public $jobs;

    public function mount()
    {
        $this->jobs = VmmfgJob::latest()->get();
        $this->units = VmmfgUnit::latest()->get();
        $this->users = User::whereHas('roles', function($query) {
                            // $query->whereNotIn('name', ['superadmin']);
                        })->orderBy('name', 'asc')->get();
        $this->filters['date_from'] = Carbon::today()->toDateString();
        $this->filters['date_to'] = Carbon::today()->toDateString();
        $this->filters['user_id'] = auth()->user()->hasRole('staff') ? auth()->user()->id : '';
        $this->filters['is_done'] = 1;
    }

    public function render()
    {

        $tasks = $this->mainQuery();

        $tasks = $this->queryFilter($tasks, $this->filters);

        if($sortKey = $this->sortKey) {
            $tasks = $tasks->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
        }else {
            $tasks = $tasks->orderBy('batch_no')->orderBy('unit_no')->orderBy('done_time');
        }

        $tasks = $tasks->paginate($this->itemPerPage);
// dd($tasks->toArray());
        return view('livewire.vmmfg-dailyreport', ['tasks' => $tasks]);
    }


    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function sortBy($key)
    {
        if($this->sortKey === $key) {
            $this->sortAscending = !$this->sortAscending;
        }else {
            $this->sortAscending = true;
        }

        $this->sortKey = $key;
    }

    public function exportPdf()
    {
        $tasks = $this->mainQuery();

        $tasks = $this->queryFilter($tasks, $this->filters);

        if($sortKey = $this->sortKey) {
            $tasks = $tasks->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
        }

        $tasks = $tasks->get();

        $pdf = PDF::loadView('pdf.vmmfg.dailyreport', [
            'tasks' => $tasks,
            'filtersData' => $this->getFilterInfo(),
        ])->output();

        return response()->streamDownload(
            fn () => print($pdf),
            Carbon::now()->format('ymdHis')."_dailyreport.pdf"
       );
    }

    public function getFilterInfo()
    {
        $filtersData = [
            'isDone' => $this->filters['is_done'] == 1 ? 'Yes' : 'No',
            'isChecked' => $this->filters['is_checked'] == 1 ? 'Yes' : 'No',
            'userName' => $this->filters['user_id'] ? User::find($this->filters['user_id'])->name : '',
            'dateFrom' => $this->filters['date_from'],
            'dateTo' => $this->filters['date_to'],
            'jobBatchNo' => $this->filters['job_id'] ? VmmfgJob::find($this->filters['job_id'])->batch_no : '',
            'unitNo' => $this->filters['unit_id'] ? VmmfgUnit::find($this->filters['unit_id'])->unit_no : '',
        ];

        return $filtersData;
    }

    private function mainQuery()
    {
        $query = VmmfgTask::with([
                            'attachments',
                            'vmmfgUnit',
                            'vmmfgUnit.vmmfgJob',
                            'vmmfgItem',
                            'vmmfgItem.attachments',
                            'vmmfgItem.vmmfgTitle',
                            'doneBy',
                            'checkedBy',
                            'undoDoneBy'
                ]);
        $query = $query
                ->leftJoin('vmmfg_units', 'vmmfg_units.id', '=', 'vmmfg_tasks.vmmfg_unit_id')
                ->leftJoin('vmmfg_jobs', 'vmmfg_jobs.id', '=', 'vmmfg_units.vmmfg_job_id')
                ->leftJoin('vmmfg_items', 'vmmfg_items.id', '=', 'vmmfg_tasks.vmmfg_item_id')
                ->leftJoin('vmmfg_titles', 'vmmfg_titles.id', '=', 'vmmfg_items.vmmfg_title_id')
                ->leftJoin('users as done_users', 'done_users.id', '=', 'vmmfg_tasks.done_by')
                ->leftJoin('users as checked_users', 'checked_users.id', '=', 'vmmfg_tasks.checked_by')
                ->leftJoin('users as undo_done_users', 'undo_done_users.id', '=', 'vmmfg_tasks.undo_done_by');

        return $query;
    }

    public function onPrevNextDateClicked($direction, $model)
    {
        $date = Carbon::now();
        if($model) {
            $date = Carbon::parse($this->filters[$model]);
        }
        if($direction > 0) {
            $this->filters[$model] = $date->addDay()->toDateString();
        }else {
            $this->filters[$model] = $date->subDay()->toDateString();
        }
    }

    private function queryFilter($query, $filters)
    {
        $tasks = $query;

        if($filters['is_done'] !== '') {
            $isDone = $filters['is_done'];
            $tasks = $tasks->search('is_done', $isDone);
        }
        if($filters['is_checked'] !== '') {
            $isChecked = $filters['is_checked'];
            $tasks = $tasks->search('is_checked', $isChecked);
        }

        if($jobId = $filters['job_id']) {
            $tasks = $tasks->whereHas('vmmfgUnit', function($query) use ($jobId) {
                $query->search('vmmfg_job_id', $jobId);
            });
        }
        if($unitId = $filters['unit_id']) {
            $tasks = $tasks->whereHas('vmmfgUnit', function($query) use ($unitId) {
                $query->search('id', $unitId);
            });
        }
        if($userId = $filters['user_id']) {
            $tasks = $tasks->where(function($query) use ($userId) {
                $query->search('done_by', $userId)->orSearch('checked_by', $userId)->orSearch('undo_done_by', $userId);
            });
        }
        // dd($filters['date_from'], $filters['date_to']);
        if($dateFrom = $filters['date_from']) {
            $tasks = $tasks->where(function($query) use ($dateFrom) {
                $query->searchFromDate('done_time', $dateFrom)->orSearchFromDate('checked_time', $dateFrom)->orSearchFromDate('undo_done_time', $dateFrom);
            });
        }
        if($dateTo = $filters['date_to']) {
            $tasks = $tasks->where(function($query) use ($dateTo) {
                $query->searchToDate('done_time', $dateTo)->orSearchToDate('checked_time', $dateTo)->orSearchToDate('undo_done_time', $dateTo);
            });
        }

        return $tasks;
    }
}
