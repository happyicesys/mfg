<?php

namespace App\Http\Livewire;

use App\Models\Attachment;
use App\Models\VmmfgItem;
use App\Models\VmmfgJob;
use App\Models\VmmfgTask;
use App\Models\VmmfgUnit;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Storage;

class VmmfgOps extends Component
{
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';
    public $itemPerPage = 100;
    public $showEditModal = false;
    public $selected = [];
    public $batch_no = '';
    public $unit_no = '';
    public $job;
    public $unit;
    public $form = [
        'batch_no' => '',
        'model' => '',
        'order_date' => '',
        'item_id' => '',
    ];
    public $editArea = '';
    public $file;

    public function mount()
    {
        $this->jobs = VmmfgJob::all();
    }

    public function updatedBatchNo($value)
    {
        $this->job = VmmfgJob::find($value);
        $this->reset('unit_no');
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

    public function showEditArea($itemId)
    {
        if($this->editArea === $itemId) {
            $this->editArea = '';
        }else {
            $this->editArea = $itemId;
        }
    }

    public function onDoneClicked(VmmfgItem $item)
    {
        VmmfgTask::updateOrCreate([
            'vmmfg_item_id' => $item->id,
            'vmmfg_unit_id' => $this->unit->id,
        ], [
            'is_done' => 1,
            'done_by' => auth()->user()->id,
            'done_time' => Carbon::now(),
            'status' => VmmfgTask::STATUS_DONE,
        ]);

        // $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }

    public function onUndoClicked(VmmfgTask $task)
    {
        $task->delete();
        // $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }

    public function uploadAttachment($itemId)
    {
        $this->validate([
            'file' => 'mimes:pdf,png,jpg,jpeg',
        ]);
        $task = VmmfgTask::where('vmmfg_item_id', $itemId)->where('vmmfg_unit_id', $this->unit->id)->first();

        if(!$task) {
            $task = VmmfgTask::create([
                'vmmfg_item_id' => $itemId,
                'vmmfg_unit_id' => $this->unit->id,
                'status' => VmmfgTask::STATUS_NEW,
            ]);
        }

        $url = $this->file->storePublicly('tasks', 'digitaloceanspaces');
        $fullUrl = Storage::url($url);
        $task->attachments()->create([
            'url' => $url,
            'full_url' => $fullUrl,
        ]);
    }

    public function deleteAttachment(Attachment $attachment)
    {
        $deleteFile = Storage::disk('digitaloceanspaces')->delete($attachment->url);
        if($deleteFile){
            $attachment->delete();
        }
        $this->emit('updated');
        session()->flash('success', 'Entry has been removed');
    }

    public function downloadAttachment(Attachment $attachment)
    {
        return Storage::disk('digitaloceanspaces')->download($attachment->url);
    }

    public function onCheckedClicked(VmmfgTask $task)
    {
        $task->update([
            'is_checked' => 1,
            'checked_time' => Carbon::now(),
            'checked_by' => auth()->user()->id,
            'status' => VmmfgTask::STATUS_CHECKED,
        ]);
        session()->flash('success', 'Your entry has been updated');
    }

    public function onUndoCheckedClicked(VmmfgTask $task)
    {
        $task->update([
            'is_checked' => 0,
            'checked_time' => null,
            'checked_by' => null,
            'status' => VmmfgTask::STATUS_DONE,
        ]);
        session()->flash('success', 'Your entry has been updated');
    }
}
