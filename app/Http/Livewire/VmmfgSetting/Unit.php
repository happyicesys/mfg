<?php

namespace App\Http\Livewire\VmmfgSetting;

use App\Models\VmmfgUnit;
use DB;
use Livewire\Component;
use Livewire\WithPagination;

class Unit extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $itemPerPage = 100;
    public $sortKey = 'unit_no';
    public $sortAscending = true;
    public $showEditModal = false;
    public $showFilters = false;
    public $selected = [];
    public $filters = [
        'search' => '',
        'unit_no' => '',
        'batch_no' => '',
    ];

    public VmmfgUnit $form;

    public function rules()
    {
        return [
            'form.unit_no' => 'required',
        ];
    }


    public function render()
    {
        $units = VmmfgUnit::with('vmmfgJob')
                        ->leftJoin('vmmfg_jobs', 'vmmfg_jobs.id', '=', 'vmmfg_units.vmmfg_job_id');

        // advance search
        $units = $units
                ->when($this->filters['unit_no'], fn($query, $input) => $query->searchLike('unit_no', $input))
                ->when($this->filters['search'], fn($query, $input) => $query->searchLike('unit_no', $input)->orWhereHas('vmmfgJob', fn($query, $input) => $query->searchLike('batch_no', $input)));

        if($input = $this->filters['batch_no']) {
            $units = $units->whereHas('vmmfgJob', function($query) use ($input) {
                $query->searchLike('batch_no', $input);
            });
        }

        if($sortKey = $this->sortKey) {
            $units = $units->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
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

    public function edit(VmmfgUnit $units)
    {
        $this->form = $units;

    }

    public function save()
    {
        $this->validate();
        $this->form->save();
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
