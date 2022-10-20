<?php

namespace App\Http\Livewire\VmmfgSetting;

use App\Models\Attachment;
use App\Models\VmmfgScope;
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
        'code' => '',
        'unit_no' => '',
        'batch_no' => '',
        'vend_id' => '',
        'date_from' => '',
        'date_to' => '',
        'is_completed' => '0',
    ];
    public $scopes;
    public $unitSelections;

    public VmmfgUnit $unitForm;

    protected $listeners = [
        'refresh' => '$refresh',
    ];

    public function rules()
    {
        return [
            'unitForm.unit_no' => 'required',
            'unitForm.vend_id' => 'sometimes',
            'unitForm.completion_date' => 'sometimes',
            'unitForm.model' => 'sometimes',
            'unitForm.vmmfg_scope_id' => 'sometimes',
            'unitForm.order_date' => 'sometimes',
            'unitForm.refer_completion_unit_id' => 'sometimes',
        ];
    }

    public function mount()
    {
        $this->scopes = VmmfgScope::latest()->get();
        $this->unitSelections = [];
    }

    public function render()
    {
        // $units = VmmfgUnit::query()
        //                 ->leftJoin('vmmfg_jobs', 'vmmfg_jobs.id', '=', 'vmmfg_units.vmmfg_job_id')
        //                 ->leftJoin('vmmfg_scopes', 'vmmfg_scopes.id', '=', 'vmmfg_units.vmmfg_scope_id')
        //                 ->leftJoin('vmmfg_units AS refer_completion_units', 'refer_completion_units.id', '=', 'vmmfg_units.refer_completion_unit_id')
        //                 ->leftJoin('vmmfg_jobs AS refer_completion_unit_jobs', 'refer_completion_unit_jobs.id', '=', 'refer_completion_units.vmmfg_job_id')
        //                 ->select(
        //                     '*',
        //                     'vmmfg_units.id AS id',
        //                     'vmmfg_units.completion_date',
        //                     'vmmfg_units.model AS model',
        //                     'vmmfg_units.order_date AS order_date',
        //                     'vmmfg_units.unit_no',
        //                     'vmmfg_units.vend_id',
        //                     'vmmfg_jobs.batch_no',
        //                     'vmmfg_scopes.name AS scope_name'
        //                 );

        $units = VmmfgUnit::with('vmmfgJob', 'referCompletionUnit')
                        ->leftJoin('vmmfg_jobs', 'vmmfg_jobs.id', '=', 'vmmfg_units.vmmfg_job_id')
                        ->select(
                            '*',
                            'vmmfg_units.id AS id',
                            'vmmfg_units.completion_date AS completion_date',
                            'vmmfg_units.model AS model',
                            'vmmfg_units.order_date AS order_date'
                        );

        // advance search
        $units = $units
                ->when($this->filters['code'], fn($query, $input) => $query->searchLike('vmmfg_units.code', $input))
                ->when($this->filters['unit_no'], fn($query, $input) => $query->searchLike('vmmfg_units.unit_no', $input))
                ->when($this->filters['vend_id'], fn($query, $input) => $query->searchLike('vmmfg_units.vend_id', $input));

        if($input = $this->filters['batch_no']) {
            $units = $units->whereHas('vmmfgJob', function($query) use ($input) {
                $query->searchLike('batch_no', $input);
            });
        }

        if($dateFrom = $this->filters['date_from']) {
            $units = $units->where(function($query) use ($dateFrom) {
                $query->searchFromDate('vmmfg_units.order_date', $dateFrom);
            });
        }
        if($dateTo = $this->filters['date_to']) {
            $units = $units->where(function($query) use ($dateTo) {
                $query->searchToDate('vmmfg_units.order_date', $dateTo);
            });
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
            $units = $units->orderBy('vmmfg_units.order_date')->orderBy('batch_no')->orderBy('vmmfg_units.unit_no');
        }

        $units = $units->paginate($this->itemPerPage);


        // dd($units->toArray());
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
        $this->unitSelections = VmmfgUnit::query()
                                        ->leftJoin('vmmfg_jobs', 'vmmfg_jobs.id', '=', 'vmmfg_units.vmmfg_job_id')
                                        ->where('vmmfg_units.id', '<>', $unit->id)
                                        ->select(
                                            '*',
                                            'vmmfg_units.id'
                                            )
                                        ->orderBy('vmmfg_units.order_date', 'desc')
                                        ->orderBy('batch_no', 'desc')
                                        ->orderBy('unit_no', 'desc')
                                        ->get();
    }

    public function save()
    {
        // dd($this->unitForm->toArray());
        $this->validate();
        $this->unitForm->save();
        $this->unitForm->update(['code' => $this->unitForm->vmmfgJob->batch_no.'-'.$this->unitForm->unit_no]);
        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }

    public function delete()
    {
        if($this->unitForm->vmmfgTasks()->exists()) {
            foreach($this->unitForm->vmmfgTasks() as $task) {
                if($task->attachments()->exists()) {
                    foreach($task->attachments() as $attachment) {
                        $this->deleteAttachment($attachment);

                        // Storage::disk('digitaloceanspaces')->delete($attachment->url);
                        // $attachment->delete();
                    }
                }
                $task->delete();
            }
        }
        $this->unitForm->delete();
        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been deleted');
    }

    public function deleteAttachment(Attachment $attachment)
    {
        if(Attachment::where('full_url', $attachment->full_url)->count() === 1) {
            Storage::disk('digitaloceanspaces')->delete($attachment->url);
        }
        $attachment->delete();

        $this->emit('updated');
        session()->flash('success', 'Entry has been removed');
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
