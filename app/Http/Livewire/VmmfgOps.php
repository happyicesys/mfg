<?php

namespace App\Http\Livewire;

use App\Models\Attachment;
use App\Models\User;
use App\Models\VmmfgItem;
use App\Models\VmmfgJob;
use App\Models\VmmfgScope;
use App\Models\VmmfgTask;
use App\Models\VmmfgUnit;
use App\Traits\HasDateControl;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Storage;


class VmmfgOps extends Component
{
    use HasDateControl, WithFileUploads;

    protected $paginationTheme = 'bootstrap';
    public $itemPerPage = 100;
    public $showEditModal = false;
    public $selected = [];
    public $job_id = '';
    public $unit_id = '';
    public $is_incomplete = false;
    public $job;
    // public $unit;
    public $form = [
        'job_id' => '',
        'unit_id' => '',
        'item_id' => '',
        'date_from' => '',
        'date_to' => '',
        'user_id' => '',
        'is_incomplete' => '',
    ];
    public $editArea = '';
    public $file;
    public $zoomPictureUrl = '';
    public $users;

    public function mount()
    {
        $this->jobs = VmmfgJob::orderBy('order_date', 'desc')->get();
        $this->users = User::whereHas('roles', function($query) {
            $query->whereNotIn('name', ['superadmin']);
        })->orderBy('name', 'asc')->get();
    }

    public function updatedJobId($value)
    {
        if($value) {
            $this->job = VmmfgJob::find($value);
            $this->form['job_id'] = $this->job_id;
        }
        $this->reset('unit_id');
        $this->form['unit_id'] = '';
    }

    public function updatedUnitId($value)
    {
        if($value) {
            // $this->unit = VmmfgUnit::find($value);
            $this->form['unit_id'] = $this->unit_id;
        }
    }

    public function updatedIsIncomplete($value)
    {
        if($value) {
            $this->form['is_incomplete'] = $this->is_incomplete;
        }
    }

    public function rules()
    {
        return [
            'form.job_id' => 'sometimes',
            'form.unit_id' => 'sometimes',
        ];
    }


    public function render()
    {
        $userId = $this->form['user_id'];
        $unitId = $this->form['unit_id'];
        $dateFrom = $this->form['date_from'];
        $dateTo = $this->form['date_to'];
        $isIncomplete = $this->form['is_incomplete'] ? true : false;
        $vmmfgUnit = '';

        if($unitId) {
            $vmmfgUnit = VmmfgUnit::query();
            $vmmfgUnit = $vmmfgUnit
                        ->with([
                            'vmmfgScope',
                            'vmmfgScope.vmmfgTitles',
                            'vmmfgScope.vmmfgTitles.vmmfgItems',
                                // => function($query) use ,
                            'vmmfgScope.vmmfgTitles.vmmfgItems.attachments',
                            'vmmfgScope.vmmfgTitles.vmmfgItems.vmmfgTasks'
                                => function($query) use ($unitId, $isIncomplete){
                                    $query->when($unitId, fn($query, $input) => $query->search('vmmfg_unit_id', $input));
                                },
                            'vmmfgScope.vmmfgTitles.vmmfgItems.vmmfgTasks.attachments',
                        ])
                        ->whereHas('vmmfgScope.vmmfgTitles.vmmfgItems.vmmfgTasks', function($query) use ($isIncomplete){
                            // $query->when($isIncomplete, fn($query, $input) =>
                            //         $query->search('is_done', '!=', 1)
                            // );
                        })
                        // ->whereHas('vmmfgScope.vmmfgTitles.vmmfgItems.vmmfgTasks', function($query) use ($userId, $dateFrom, $dateTo){
                        //     $query->when($userId, fn($query, $input) =>
                        //             $query->search('done_by', $input)
                        //                 ->orSearch('checked_by', $input)
                        //                 ->orSearch('undo_done_by', $input))
                        //         ->when($dateFrom, fn($query, $input) =>
                        //             $query->searchFromDate('done_time', $input)
                        //                 ->orSearchFromDate('checked_time', $input)
                        //                 ->orSearchFromDate('undo_done_time', $input))
                        //         ->when($dateTo, fn($query, $input) =>
                        //             $query->searchToDate('done_time', $input)
                        //                 ->orSearchToDate('checked_time', $input)
                        //                 ->orSearchToDate('undo_done_time', $input)
                        //     );
                        // })
                        ->when($unitId, fn($query, $input) => $query->search('id', $input))
                        ->get();
                        // dd($this->form, $vmmfgUnit->toArray());
        }


        return view('livewire.vmmfg-ops', ['vmmfgUnit' => $vmmfgUnit]);
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
        $this->reset('job_id');
        $this->reset('unit_id');
        $this->reset('form');
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
            'vmmfg_unit_id' => $this->form['unit_id'],
        ], [
            'is_done' => 1,
            'done_by' => auth()->user()->id,
            'done_time' => Carbon::now(),
            'status' => VmmfgTask::STATUS_DONE,
            'undo_done_by' => null,
            'undo_done_time' => null,
        ]);

        // $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }

    public function onUndoClicked(VmmfgTask $task)
    {
        // if($task->attachments()) {
            $task->update([
                'is_done' => 0,
                'undo_done_by' => auth()->user()->id,
                'undo_done_time' => Carbon::now(),
                'status' => VmmfgTask::STATUS_UNDONE,
            ]);
        // }else {
        //     $task->delete();
        // }

        // $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }

    public function uploadAttachment($itemId)
    {
        $this->validate([
            'file' => 'sometimes',
        ]);
        $task = VmmfgTask::where('vmmfg_item_id', $itemId)->where('vmmfg_unit_id', $this->form['unit_id'])->first();

        if(!$task) {
            $task = VmmfgTask::create([
                'vmmfg_item_id' => $itemId,
                'vmmfg_unit_id' => $this->form['unit_id'],
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

    public function onCancelledClicked(VmmfgItem $item)
    {
        VmmfgTask::updateOrCreate([
            'vmmfg_item_id' => $item->id,
            'vmmfg_unit_id' => $this->form['unit_id'],
        ], [
            'status' => VmmfgTask::STATUS_CANCELLED,
            'cancelled_time' => Carbon::now(),
            'cancelled_by' => auth()->user()->id,
        ]);
        session()->flash('success', 'Your entry has been updated');
    }

    public function onUndoCancelledClicked(VmmfgTask $task)
    {
        $task->delete();
        session()->flash('success', 'Your entry has been updated');
    }

    public function onZoomPictureClicked(Attachment $attachment)
    {
        $this->zoomPictureUrl = $attachment->full_url;
    }
}
