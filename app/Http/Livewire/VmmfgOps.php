<?php

namespace App\Http\Livewire;

use App\Models\VmmfgJob;
use App\Models\VmmfgUnit;
use Livewire\Component;

class VmmfgOps extends Component
{

    protected $paginationTheme = 'bootstrap';
    public $itemPerPage = 100;
    public $showEditModal = false;
    public $selected = [];
    public $batch_no = '';
    public $unit_no = '';
    public $job;
    public $unit;

    public function mount()
    {
        $this->jobs = VmmfgJob::all();
    }

    public function updatedBatchNo($value)
    {
        // dd($value);
        $this->job = VmmfgJob::find($value);
    }

    public function updatedUnitNo($value)
    {
        $this->unit = VmmfgUnit::find($value);
    }

    public function rules()
    {
        return [
            // 'form.id' => 'sometimes|same:id',
            'form.batch_no' => 'required',
            'form.model' => 'sometimes',
            'form.order_date' => 'required',
        ];
    }


    public function render()
    {
        return view('livewire.vmmfg-ops');
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

    public function resetFilters()
    {
        $this->reset('batch_no');
        $this->reset('unit_no');
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }
}
