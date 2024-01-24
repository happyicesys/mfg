<?php

namespace App\Http\Livewire;

use App\Models\VmmfgTitleCategory;
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
        'vend_id' => '',
        'is_completed' => '0',
    ];
    public $vmmfgTitleCategories;

    public VmmfgUnit $unitForm;

    public function rules()
    {
        return [
            'unitForm.unit_no' => 'required',
        ];
    }

    public function mount()
    {
        $this->vmmfgTitleCategories = VmmfgTitleCategory::oldest()->get();
    }

    public function render()
    {
        $units = VmmfgUnit::with([
            // 'vmmfgJob',
            'vmmfgScope',
            // 'vmmfgScope.vmmfgTitles' => function($query) {
            //     $query->withCount('vmmfgItems');
            // },
            // 'vmmfgScope.vmmfgTitles.vmmfgItems.vmmfgTitle',
            // 'vmmfgTasks.vmmfgItem.vmmfgTitle.vmmfgItems',
            ])
            // ->withCount('vmmfgTasks')
            ->leftJoin('vmmfg_jobs', 'vmmfg_jobs.id', '=', 'vmmfg_units.vmmfg_job_id')
            ->leftJoin('vmmfg_scopes', 'vmmfg_scopes.id', '=', 'vmmfg_units.vmmfg_scope_id');

        // advance search
        $units = $units
                ->when($this->filters['unit_no'], fn($query, $input) => $query->searchLike('vmmfg_units.unit_no', $input))
                ->when($this->filters['vend_id'], fn($query, $input) => $query->searchLike('vmmfg_units.vend_id', $input));

        if($input = $this->filters['batch_no']) {
            $units = $units->whereHas('vmmfgJob', function($query) use ($input) {
                $query->searchLike('batch_no', $input);
            });
        }



        // $units = $units
        //         ->when($this->filters['unit_no'], fn($query, $input) => $query->searchLike('unit_no', $input));

        // if($batchNo = $this->filters['batch_no']) {
        //     $units = $units->whereHas('vmmfgJob', function($query) use ($batchNo) {
        //         $query->searchLike('batch_no', $batchNo);
        //     });
        // }

        if($model = $this->filters['model']) {
            $units = $units->searchLike('vmmfg_units.model', $model);
            // $units = $units->whereHas('vmmfgJob', function($query) use ($model) {
            //     $query->searchLike('model', $model);
            // });
        }

        if($this->filters['is_completed'] !== '') {
            $isCompleted = $this->filters['is_completed'];
            $units = $units->where(function($query) use ($isCompleted) {
                if($isCompleted == 1) {
                    $query->whereNotNull('vmmfg_units.completion_date');
                }else {
                    $query->whereNull('vmmfg_units.completion_date');
                }
            });
        }


        if($sortKey = $this->sortKey) {
            $units = $units->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
        }else {
            $units = $units->orderBy(DB::raw('DATE(vmmfg_units.order_date)'), 'asc')->orderBy('batch_no')->orderBy('unit_no', 'asc');
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
