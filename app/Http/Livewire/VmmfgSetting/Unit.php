<?php

namespace App\Http\Livewire\VmmfgSetting;

use App\Models\VmmfgUnit;
use DB;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Unit extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $itemPerPage = 100;
    public $sortKey = '';
    public $sortAscending = true;
    public $showEditModal = false;
    public $selected = [];
    public $filters = [
        'search' => '',
        'unit_no' => '',
        'batch_no' => '',
        'vend_id' => '',
        'date_from' => '',
        'date_to' => '',
    ];

    public VmmfgUnit $unitForm;

    public function rules()
    {
        return [
            'unitForm.unit_no' => 'required',
            'unitForm.vend_id' => 'sometimes',
            'unitForm.completion_date' => 'sometimes',
        ];
    }


    public function render()
    {
        $units = VmmfgUnit::with('vmmfgJob')
                        ->leftJoin('vmmfg_jobs', 'vmmfg_jobs.id', '=', 'vmmfg_units.vmmfg_job_id')
                        ->select('*', 'vmmfg_units.id AS id', 'vmmfg_units.completion_date AS completion_date');

        // advance search
        $units = $units
                ->when($this->filters['unit_no'], fn($query, $input) => $query->searchLike('unit_no', $input))
                ->when($this->filters['vend_id'], fn($query, $input) => $query->searchLike('vend_id', $input));

        if($input = $this->filters['batch_no']) {
            $units = $units->whereHas('vmmfgJob', function($query) use ($input) {
                $query->searchLike('batch_no', $input);
            });
        }

        if($dateFrom = $this->filters['date_from']) {
            $units = $units->where(function($query) use ($dateFrom) {
                $query->searchFromDate('order_date', $dateFrom);
            });
        }
        if($dateTo = $this->filters['date_to']) {
            $units = $units->where(function($query) use ($dateTo) {
                $query->searchToDate('order_date', $dateTo);
            });
        }

        if($sortKey = $this->sortKey) {
            $units = $units->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
        }else {
            $units = $units->orderBy('order_date')->orderBy('batch_no')->orderBy('unit_no');
        }

        $units = $units->paginate($this->itemPerPage);

        return view('livewire.vmmfg-setting.unit', ['units' => $units]);
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

    public function edit(VmmfgUnit $unit)
    {
        $this->unitForm = $unit;

    }

    public function save()
    {
        $this->validate();
        $this->unitForm->save();
        $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function updatedFilters()
    {
        $this->resetPage();
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
}
