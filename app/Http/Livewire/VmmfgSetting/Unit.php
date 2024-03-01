<?php

namespace App\Http\Livewire\VmmfgSetting;

use App\Models\Attachment;
use App\Models\UnitTransferDestination;
use App\Models\VmmfgScope;
use App\Models\VmmfgUnit;
use App\Traits\HasProgress;
use DB;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Http;

class Unit extends Component
{
    use HasProgress, WithPagination;

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
    public $editUnitSelections;
    public $unitTransferDestinationOptions;

    public VmmfgUnit $previousUnitForm;
    public VmmfgUnit $unitForm;

    protected $listeners = [
        'refresh' => '$refresh',
    ];

    protected $queryString = [
        'filters'
    ];

    public function rules()
    {
        return [
            'unitForm.code' => 'sometimes',
            'unitForm.unit_no' => 'required',
            'unitForm.vend_id' => 'sometimes',
            'unitForm.completion_date' => 'sometimes',
            'unitForm.model' => 'sometimes',
            'unitForm.vmmfg_scope_id' => 'sometimes',
            'unitForm.order_date' => 'sometimes',
            'unitForm.refer_completion_unit_id' => 'sometimes',
            'unitForm.destination' => 'sometimes',
            'unitForm.origin' => 'sometimes',
        ];
    }

    public function mount()
    {
        $this->scopes = VmmfgScope::latest()->get();
        $this->editUnitSelections = [];
        $this->unitTransferDestinationOptions = UnitTransferDestination::OPTIONS;
    }

    public function render()
    {
        $units = VmmfgUnit::query()
            ->with([
                // 'children.vmmfgScope',
                // 'children.vmmfgJob',
                'vmmfgJob',
                'vmmfgScope',
                'referCompletionUnit'
            ])
            ->leftJoin('vmmfg_jobs', 'vmmfg_jobs.id', '=', 'vmmfg_units.vmmfg_job_id')
            ->select(
                '*',
                'vmmfg_units.id AS id',
                'vmmfg_units.completion_date AS completion_date',
                'vmmfg_units.model AS model',
                'vmmfg_units.order_date AS order_date',
                'vmmfg_units.refer_completion_unit_id AS refer_completion_unit_id',
                'vmmfg_units.origin',
                'vmmfg_units.destination',
                'vmmfg_units.vend_id'
            );

        $units = $units
                ->when(isset($this->filters['code']) ? $this->filters['code'] : false, fn($query, $input) => $query->searchLike('vmmfg_units.code', $input))
                ->when(isset($this->filters['unit_no']) ? $this->filters['unit_no'] : false, fn($query, $input) => $query->searchLike('vmmfg_units.unit_no', $input))
                ->when(isset($this->filters['vend_id']) ? $this->filters['vend_id'] : false, fn($query, $input) => $query->searchLike('vmmfg_units.vend_id', $input));

        if($input = isset($this->filters['batch_no']) ? $this->filters['batch_no'] : false) {
            $units = $units->whereHas('vmmfgJob', function($query) use ($input) {
                $query->searchLike('batch_no', $input);
            });
        }
        if($dateFrom = isset($this->filters['date_from']) ? $this->filters['date_from'] : false) {
            $units = $units->where(function($query) use ($dateFrom) {
                $query->searchFromDate('vmmfg_units.order_date', $dateFrom);
            });
        }
        if($dateTo = isset($this->filters['date_to']) ? $this->filters['date_to'] : false) {
            $units = $units->where(function($query) use ($dateTo) {
                $query->searchToDate('vmmfg_units.order_date', $dateTo);
            });
        }

        if(isset($this->filters['is_completed']) and ($this->filters['is_completed'] !== '')) {
            $isCompleted = $this->filters['is_completed'];
            $units = $units->where(function($query) use ($isCompleted) {
                if($isCompleted == 1) {
                    $query->whereNotNull('vmmfg_units.completion_date');
                }else {
                    $query->whereNull('vmmfg_units.completion_date');
                }
            });
        }

        // exclude children
        // $units = $units->whereNull('vmmfg_units.parent_id');

        if($sortKey = $this->sortKey) {
            $units = $units->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
        }else {
            $units = $units->orderBy('vmmfg_units.order_date')->orderBy('batch_no')->orderBy('vmmfg_units.unit_no');
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
        $this->unitForm = new VmmfgUnit();
        $this->previousUnitForm = new VmmfgUnit();
        $this->editUnitSelections = [];

        $this->unitForm = $unit;
        $this->previousUnitForm = $unit;

        $this->editUnitSelections = VmmfgUnit::query()
            ->with('vmmfgJob')
            ->leftJoin('vmmfg_jobs', 'vmmfg_jobs.id', '=', 'vmmfg_units.vmmfg_job_id')
            ->doesntHave('referCompletionUnit')
            ->whereDoesntHave('bindedCompletionUnit', function($query) use ($unit) {
                $query->where('id', '<>', $unit->id);
            })
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
        $this->validate();
        $this->unitForm->save();

        // save form
        $this->unitForm->update([
            'code' => $this->unitForm->code ? $this->unitForm->code : $this->unitForm->vmmfgJob->batch_no.'-'.$this->unitForm->unit_no,
            'vmmfg_job_json' => $this->unitForm->vmmfgJob,
            'vmmfg_scope_json' => $this->unitForm->vmmfgScope,
            'origin' => $this->unitForm->origin ? $this->unitForm->origin : null,
            'destination' => $this->unitForm->destination ? $this->unitForm->destination : null,
            'current' => $this->unitForm->current ? $this->unitForm->current : env('APP_CURRENT_LOCATION'),
            'refer_completion_unit_id' => $this->unitForm->refer_completion_unit_id ? $this->unitForm->refer_completion_unit_id : null,
        ]);

        // store children json at parent
        // if($this->unitForm->children()->exists()) {
        //     $this->unitForm->update([
        //         'children_json' => $this->unitForm->children()->get()
        //     ]);
        // }

        // send unit to another mfg upon option chosen
        if($this->unitForm->destination and ($this->unitForm->destination != $this->previousUnitForm->destination)) {
            $response = $this->createUnitTransfer();
            if($response->failed()) {
                $this->emit('refresh');
                $this->emit('updated');
                session()->flash('error', 'This Unit has Failed to Transfer');
            }
        }

        // record timestamp when unit is rework or retired
        if($this->unitForm->is_rework or $this->unitForm->is_retired) {
            $this->unitForm->update([
                'status_datetime' => Carbon::now()
            ]);
        }

        $this->syncProgress($this->unitForm);

        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }

    public function delete()
    {
        // delete tasks and attachments if exists
        if($this->unitForm->vmmfgTasks()->exists()) {
            foreach($this->unitForm->vmmfgTasks() as $task) {
                if($task->attachments()->exists()) {
                    foreach($task->attachments() as $attachment) {
                        $this->deleteAttachment($attachment);
                    }
                }
                $task->delete();
            }
        }
        // delete children if exists
        // if($this->unitForm->children()->exists()) {
        //     foreach($this->unitForm->children() as $child) {
        //         $child->delete();
        //     }
        // }
        // notify origin to delete unit transfer record
        if($this->unitForm->origin_ref_id) {
            $this->revokeUnitTransfer();
        }
        $this->unitForm->delete();

        // refresh form after delete (avoid livewire error)
        $this->unitForm = new VmmfgUnit;
        $this->previousUnitForm = new VmmfgUnit;
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

    public function rework()
    {
        $reworkUnit = $this->unitForm->replicate()->fill([
            'is_rework' => true,
            // 'parent_id' => $this->unitForm->id,
            'refer_completion_unit_id' => null,
            'completion_date' => null,
            'order_date' => Carbon::now(),
            'status_datetime' => Carbon::now(),
            'origin' => null,
            'destination' => null,
            'children_json' => null,
            'origin_ref_id' => null,
            'origin_vmmfg_job_json' => null,
            'origin_vmmfg_scope_json' => null,
            'vmmfg_job_json' => null,
            'vmmfg_scope_json' => null,
        ]);
        $reworkUnit->save();

        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }

    public function retire()
    {
        $this->unitForm->update([
            'is_retired' => true,
            'status_datetime' => Carbon::now(),
        ]);

        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }

    public function undoRetire()
    {
        $this->unitForm->update([
            'is_retired' => false,
            'status_datetime' => null,
        ]);

        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }

    private function createUnitTransfer()
    {
        $response = Http::post(
            UnitTransferDestination::OPTIONS[$this->unitForm->destination] . UnitTransferDestination::CREATE_DIRECTORY,
            $this->unitForm->toArray()
        );
        return $response;
    }

    private function revokeUnitTransfer()
    {
        $response = Http::post(UnitTransferDestination::OPTIONS[$this->unitForm->origin] . UnitTransferDestination::DELETE_DIRECTORY . '/' . $this->unitForm->origin_ref_id);
        return $response;
    }
}
