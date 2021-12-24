<?php

namespace App\Http\Livewire;

use App\Exports\VmmfgRemarksExcel;
use App\Models\User;
use App\Models\VmmfgJob;
use App\Models\VmmfgScope;
use Carbon\Carbon;
use DB;
use Livewire\Component;
use Livewire\WithPagination;
use PDF;
use Excel;

class VmmfgReport extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $itemPerPage = 100;
    public $sortKey = '';
    public $sortAscending = true;
    public $showEditModal = false;
    public $selected = [];
    public $filters = [
        'is_completed' => '',
        'date_from' => '',
        'date_to' => '',
        'vmmfg_scope_id' => '',
    ];

    public $scopes;
    // public $scope;

    protected $listeners = [
        'refresh' => '$refresh',
    ];

    public function mount()
    {
        $this->scopes = VmmfgScope::orderBy('remarks', 'desc')->get();
        $this->filters['date_from'] = Carbon::today()->toDateString();
        $this->filters['date_to'] = Carbon::today()->toDateString();
        $this->filters['is_completed'] = '1';
    }

    public function render()
    {
        $scope = $this->mainQuery();
        $scope = $this->queryFilter($scope, $this->filters);
        $scope = $scope->first();
        $this->scope = $scope;

        return view('livewire.vmmfg-report', ['scope' => $scope]);
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

    public function exportExcel()
    {
        $this->validate([
            'filters.vmmfg_scope_id' => 'required',
        ], [
            'filters.vmmfg_scope_id.required' => 'Please select the scope'
        ]);

        $scope = $this->mainQuery();
        $scope = $this->queryFilter($scope, $this->filters);
        $scope = $scope->first();

        return Excel::download(new VmmfgRemarksExcel($scope, $this->filters), 'remarks_'.Carbon::now()->format('ymdHis').'.xlsx');
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    private function mainQuery()
    {
        $vmmfgScope = VmmfgScope::query();

        return $vmmfgScope;
    }

    private function queryFilter($query, $filters)
    {
        $query = $query->with([
            'vmmfgTitles',
            'vmmfgTitles.vmmfgTitleCategory',
            'vmmfgTitles.vmmfgItems',
            'vmmfgUnits'
                => function($query) use ($filters) {
                    if($dateFrom = $filters['date_from']) {
                        $query->whereDate('order_date', '>=', $dateFrom);
                    }
                    if($dateTo = $filters['date_to']) {
                        $query->whereDate('order_date', '<=', $dateTo);
                    }
                    if($filters['is_completed'] !== '') {
                        $isCompleted = $filters['is_completed'];
                        $query = $query->where(function($query) use ($isCompleted) {
                            if($isCompleted == 1) {
                                $query->whereNotNull('completion_date');
                            }else {
                                $query->whereNull('completion_date');
                            }
                        });
                    }

                },
            'vmmfgUnits.vmmfgTasks',
            'vmmfgUnits.vmmfgJob'
        ]);

        if($scopeId = $filters['vmmfg_scope_id']) {
            $query = $query->where('id', $scopeId);
        }

        return $query;
    }
}
