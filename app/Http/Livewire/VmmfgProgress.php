<?php

namespace App\Http\Livewire;

use App\Models\VmmfgUnit;
use DB;
use Livewire\Component;
use Livewire\WithPagination;

class VmmfgProgress extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $itemPerPage = 100;
    public $sortKey = '';
    public $sortAscending = true;
    public $showEditModal = false;
    public $showFilters = false;
    public $selected = [];
    public $filters = [
        'search' => '',
        'unit_no' => '',
        'batch_no' => '',
        'model' => '',
        'is_completed' => '0',
    ];

    public VmmfgUnit $unitForm;

    public function rules()
    {
        return [
            'unitForm.unit_no' => 'required',
        ];
    }

    public function render()
    {
        $units = VmmfgUnit::with([
            'vmmfgJob',
            'vmmfgScope.vmmfgTitles' => function($query) {
                $query->withCount('vmmfgItems');
            }
            ])
                        ->withCount('vmmfgTasks')
                        ->leftJoin('vmmfg_jobs', 'vmmfg_jobs.id', '=', 'vmmfg_units.vmmfg_job_id');

        // advance search
        $units = $units
                ->when($this->filters['unit_no'], fn($query, $input) => $query->searchLike('unit_no', $input));

        if($batchNo = $this->filters['batch_no']) {
            $units = $units->whereHas('vmmfgJob', function($query) use ($batchNo) {
                $query->searchLike('batch_no', $batchNo);
            });
        }

        if($model = $this->filters['model']) {
            $units = $units->whereHas('vmmfgJob', function($query) use ($model) {
                $query->searchLike('model', $model);
            });
        }

        if($this->filters['is_completed'] !== '') {
            $isCompleted = $this->filters['is_completed'];
            $units = $units->whereHas('vmmfgJob', function($query) use ($isCompleted) {
                if($isCompleted == 1) {
                    $query->whereNotNull('completion_date');
                }else {
                    $query->whereNull('completion_date');
                }
            });
        }


        if($sortKey = $this->sortKey) {
            $units = $units->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
        }else {
            $units = $units->orderBy('batch_no');
        }

        $units = $units->paginate($this->itemPerPage);

        return view('livewire.vmmfg-progress', ['units' => $units]);
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

    public function edit(VmmfgUnit $unitForm)
    {
        $this->unitForm = $unitForm;
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
}
