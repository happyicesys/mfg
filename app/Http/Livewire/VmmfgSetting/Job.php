<?php

namespace App\Http\Livewire\VmmfgSetting;

use App\Models\VmmfgJob;
use App\Models\VmmfgScope;
use App\Models\VmmfgUnit;
use Carbon\Carbon;
use DB;
use Livewire\Component;
use Livewire\WithPagination;

class Job extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $itemPerPage = 100;
    public $sortKey = '';
    public $sortAscending = true;
    public $showEditModal = false;
    public $showFilters = false;
    public $showBatchGenerateUnits = false;
    public $selected = [];
    public $filters = [
        'search' => '',
        'batch_no' => '',
        'model' => '',
        'order_date_from' => '',
        'order_date_to' => '',
    ];
    public $unitForm = [
        'unit_quantity' => '',
        'unit_number' => '',
        'vmmfg_scope_id' => '',
    ];

    public VmmfgJob $form;
    public $units;
    public $scopes;

    public function rules()
    {
        return [
            // 'form.id' => 'sometimes|same:id',
            'form.batch_no' => 'required',
            'form.model' => 'sometimes',
            'form.order_date' => 'required',
            'form.completion_date' => 'sometimes',
            'form.remarks' => 'sometimes',
        ];
    }

    public function mount()
    {
        $this->scopes = VmmfgScope::latest()->get();
    }

    public function render()
    {
        $jobs = VmmfgJob::with('vmmfgUnits');

        // advance search
        $jobs = $jobs
                ->when($this->filters['batch_no'], fn($query, $input) => $query->searchLike('batch_no', $input))
                ->when($this->filters['model'], fn($query, $input) => $query->searchLike('model', $input))
                ->when($this->filters['order_date_from'], fn($query, $input) => $query->searchFromDate('order_date', $input))
                ->when($this->filters['order_date_to'], fn($query, $input) => $query->searchToDate('order_date', $input))
                ->when($this->filters['search'], fn($query, $input) => $query->searchLike('batch_no', $input)->orSearchLike('model', $input)->orSearchLike('order_date', $input));
                // ->when($this->filters['status'], fn($query, $input) => $query->where('status', 'LIKE', '%'.$input.'%'));

        if($sortKey = $this->sortKey) {
            $jobs = $jobs->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
        }else {
            $jobs = $jobs->orderBy('order_date', 'desc');
        }

        $jobs = $jobs->paginate($this->itemPerPage);

        return view('livewire.vmmfg-setting.job', ['jobs' => $jobs]);
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

    public function edit(VmmfgJob $job)
    {
        $this->form = $job;
        $this->units = $job->vmmfgUnits;
    }

    public function create()
    {
        $this->form = new VmmfgJob;
    }

    public function save()
    {
        $this->validate();
        $this->form->save();
        $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }

    public function delete(VmmfgJob $job)
    {
        $job->vmmfgUnits()->delete();
        $job->delete();
        $this->emit('updated');
        session()->flash('success', 'Your entry has been removed');
    }

    public function generateUnits()
    {
        // dd($this->form->id);
        $this->validate([
            'unitForm.unit_quantity' => 'required|integer',
            'unitForm.unit_number' => 'required|integer',
            'unitForm.vmmfg_scope_id' => 'sometimes',
        ]);

        for($i = 0; $i < $this->unitForm['unit_quantity']; $i++) {
            VmmfgUnit::create([
                'unit_no' => $this->unitForm['unit_number'] + $i,
                'vmmfg_job_id' => $this->form->id,
                'vmmfg_scope_id' => $this->unitForm['vmmfg_scope_id'],
            ]);
        }

        $this->reset('unitForm');
        $this->showBatchGenerateUnits = false;
        $this->emit('updated');
        session()->flash('success', 'All the units has been created');
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
