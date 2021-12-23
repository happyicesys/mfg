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
use PDF;
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
    public $unit_id = '';
    public $is_incomplete = false;
    public $job;
    // public $unit;
    public $form = [
        'job_id' => '',
        // 'unit_id' => '',
        'item_id' => '',
        'date_from' => '',
        'date_to' => '',
        'user_id' => '',
        'is_incomplete' => '',
        'remarks' => [],
    ];
    public $editArea = [0];
    public $file;
    public $remarks;
    public $zoomPictureUrl = '';
    public $units;
    public $users;

    protected $listeners = [
        'refresh' => '$refresh',
    ];

    protected $queryString = [
        'unit_id',
    ];

    public function mount()
    {
        $this->users = User::whereHas('roles', function($query) {
            $query->whereNotIn('name', ['superadmin']);
        })->orderBy('name', 'asc')->get();
        $this->units = VmmfgUnit::with('vmmfgJob')->leftJoin('vmmfg_jobs', 'vmmfg_jobs.id', '=', 'vmmfg_units.vmmfg_job_id')->select('*', 'vmmfg_units.id AS id', 'vmmfg_units.model AS model')->orderBy('order_date')->orderBy('batch_no')->orderBy('unit_no')->get();
    }

    // public function updatedJobId($value)
    // {
    //     if($value) {
    //         $this->job = VmmfgJob::find($value);
    //         $this->form['job_id'] = $this->job_id;
    //     }
    //     $this->reset('unit_id');
    //     $this->form['unit_id'] = '';
    //     $this->reset('editArea');
    // }

    // public function updatedUnitId($value)
    // {
    //     // if($value) {
    //         // $this->unit = VmmfgUnit::find($value);
    //         $this->form['unit_id'] = $this->unit_id;
    //         $this->reset('editArea');
    //     // }
    // }

    // public function updated($value)
    // {
    //     dd($value);
    // }

    public function updatedIsIncomplete($value)
    {
        $this->form['is_incomplete'] = $this->is_incomplete;
        $this->emit('refresh');
    }

    public function rules()
    {
        return [
            'form.job_id' => 'sometimes',
            'form.unit_id' => 'sometimes',
            'form.remarks.*' => 'sometimes',
        ];
    }


    public function render()
    {
        $vmmfgUnit = $this->mainCollections($this->form, $this->unit_id);

        if($vmmfgUnit) {
            if($vmmfgUnit->first()->vmmfgTasks) {
                foreach($vmmfgUnit->first()->vmmfgTasks as $task) {
                    $this->form['remarks'][$task->vmmfgItem->id] = $task->remarks;
                }
            }
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
        $this->reset(['unit_id', 'form']);
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function showEditArea($itemId)
    {

        if(array_search($itemId, $this->editArea)) {
            foreach(array_keys($this->editArea, $itemId, true) as $key) {
                unset($this->editArea[$key]);
            }
        }else {
            array_push($this->editArea, $itemId);
        }
        $this->reset('remarks');
        $this->emit('refresh');
        // if($this->editArea === $itemId) {
        //     $this->editArea = '';
        // }else {
            // $this->editArea = $itemId;
        // }
    }

    public function onDoneClicked(VmmfgItem $item)
    {
        if($item->is_required) {
            $this->validate([
                'form.remarks.'.$item->id => 'required',
            ], [
                'form.remarks.'.$item->id.'.required' => 'This remarks field is compulsory'
            ]);
        }

        VmmfgTask::updateOrCreate([
            'vmmfg_item_id' => $item->id,
            'vmmfg_unit_id' => $this->unit_id,
        ], [
            'is_done' => 1,
            'done_by' => auth()->user()->id,
            'done_time' => Carbon::now(),
            'status' => VmmfgTask::STATUS_DONE,
            'remarks' => isset($this->form['remarks'][$item->id]) ? $this->form['remarks'][$item->id] : null,
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
        $task = VmmfgTask::where('vmmfg_item_id', $itemId)->where('vmmfg_unit_id', $this->unit_id)->first();

        if(!$task) {
            $task = VmmfgTask::create([
                'vmmfg_item_id' => $itemId,
                'vmmfg_unit_id' => $this->unit_id,
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
            'vmmfg_unit_id' => $this->unit_id,
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

    public function exportPdf()
    {
        $vmmfgUnit = $this->mainCollections($this->form, $this->unit_id);

        $pdf = PDF::loadView('pdf.vmmfg.ops', [
            'vmmfgUnit' => $vmmfgUnit,
            'filtersData' => $this->getFilterInfo(),
        ])->output();

        return response()->streamDownload(
            fn () => print($pdf),
            "qaqc_".Carbon::now()->format('ymdHis').".pdf"
       );
    }

    public function getFilterInfo()
    {
        $filtersData = [
            'jobBatchNo' => $this->form['job_id'] ? VmmfgJob::find($this->form['job_id'])->batch_no : '',
            'unitNo' => $this->unit_id ? VmmfgUnit::find($this->unit_id)->unit_no : '',
        ];

        return $filtersData;
    }

    private function mainCollections($filters, $unitId)
    {
        $userId = $filters['user_id'];
        $dateFrom = $filters['date_from'];
        $dateTo = $filters['date_to'];
        $isIncomplete = $filters['is_incomplete'] ? true : false;
        $vmmfgUnit = '';

        if($unitId) {
            $vmmfgUnit = VmmfgUnit::query();
            $vmmfgUnit = $vmmfgUnit
                        ->with([
                            'vmmfgJob',
                            'vmmfgTasks',
                            'vmmfgScope',
                            'vmmfgScope.vmmfgTitles',
                            'vmmfgScope.vmmfgTitles.vmmfgItems'
                                => function($query) use ($isIncomplete){
                                    if($isIncomplete === true) {
                                        $query->whereHas('vmmfgTasks', function($query) use ($isIncomplete) {
                                            $query->where('is_done', '!=', 1);
                                        });
                                    }
                                },
                            'vmmfgScope.vmmfgTitles.vmmfgItems.attachments',
                            'vmmfgScope.vmmfgTitles.vmmfgItems.vmmfgTasks'
                                => function($query) use ($unitId, $isIncomplete){
                                    $query->when($unitId, fn($query, $input) => $query->search('vmmfg_unit_id', $input));
                                    if($isIncomplete === true) {
                                        $query->where('is_done', '!=', 1);
                                    }
                                },
                            'vmmfgScope.vmmfgTitles.vmmfgItems.vmmfgTasks.attachments',
                        ])
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
        }

        return $vmmfgUnit;
    }
}
