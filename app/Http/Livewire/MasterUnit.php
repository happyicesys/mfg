<?php

namespace App\Http\Livewire;

use App\Models\Attachment;
use App\Models\MasterUnit as VmmfgMasterUnit;
use DB;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Http;

class MasterUnit extends Component
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
        'container' => '',
        'batch_no' => '',
    ];

    public VmmfgMasterUnit $masterUnitForm;

    protected $listeners = [
        'refresh' => '$refresh',
    ];

    protected $queryString = [
        'filters'
    ];

    public function rules()
    {
        return [
            'masterUnitForm.batch' => 'sometimes',
            'masterUnitForm.code' => 'sometimes',
            'masterUnitForm.container' => 'sometimes',
            'masterUnitForm.name' => 'sometimes',
            'masterUnitForm.remarks' => 'sometimes',
        ];
    }

    public function render()
    {
        $masterUnits = VmmfgMasterUnit::query()
            ->with([
                'vmmfgUnits',
            ]);

        $masterUnits = $masterUnits
                ->when(isset($this->filters['code']) ? $this->filters['code'] : false, fn($query, $input) => $query->searchLike('code', $input))
                ->when(isset($this->filters['container']) ? $this->filters['container'] : false, fn($query, $input) => $query->searchLike('container', $input))
                ->when(isset($this->filters['batch_no']) ? $this->filters['batch_no'] : false, fn($query, $input) => $query->searchLike('batch_no', $input));

        if($sortKey = $this->sortKey) {
            $masterUnits = $masterUnits->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
        }else {
            $masterUnits = $masterUnits->orderBy('container', 'desc')->orderBy('code', 'asc');
        }

        $masterUnits = $masterUnits->paginate($this->itemPerPage);

        return view('livewire.master-unit', ['masterUnits' => $masterUnits]);
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

    public function edit(VmmfgMasterUnit $masterUnit)
    {
        $this->masterUnitForm = new VmmfgMasterUnit();
        $this->masterUnitForm = $masterUnit;
    }

    public function save()
    {
        $this->validate();
        $this->masterUnitForm->save();

        // record timestamp when unit is rework or retired
        if($this->unitForm->is_rework or $this->unitForm->is_retired) {
            $this->unitForm->update([
                'status_datetime' => Carbon::now()
            ]);
        }

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
        if($this->unitForm->children()->exists()) {
            foreach($this->unitForm->children() as $child) {
                $child->delete();
            }
        }
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
