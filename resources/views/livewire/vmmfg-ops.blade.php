<div>
    <div>
        <div>
            <x-flash></x-flash>
            <h2>QA/QC</h2>
            <hr>

            <div class="">
                <div>
                    <div class="bg-light p-3">
                        {{-- <div class="form-row"> --}}
                            <div class="form-group">
                                <label>
                                    Batch No
                                </label>
{{--
                                <x-input-select2 wire:model.defer="batch_no">
                                    <option value="">Select...</option>
                                    @foreach($jobs as $job)
                                        <option value="{{$job->id}}">
                                            #{{$job->batch_no}} - {{$job->model}}
                                        </option>
                                    @endforeach
                                </x-input-select2> --}}
                                <select name="batch_no" wire:model="batch_no" class="select form-control">
                                    <option value="">Select...</option>
                                    @foreach($jobs as $job)
                                        <option value="{{$job->id}}">
                                            #{{$job->batch_no}} - {{$job->model}}
                                            @if($job->vmmfgUnits) ({{count($job->vmmfgUnits)}} units) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- @dd($this->batch_no) --}}

                            @if($this->batch_no)
                                <div class="form-group">
                                    <label>
                                        Unit No
                                    </label>
                                    <select name="unit_no" wire:model="unit_no" class="select form-control">
                                        <option value="">Select...</option>
                                        @foreach($this->job->vmmfgUnits as $unit)
                                            <option value="{{$unit->id}}">
                                                #{{$unit->unit_no}}
                                            </option>
                                        @endforeach
                                    </select>
                                    {{-- <x-input-select2 wire:model.defer="unit_no">
                                        <option value="">Select...</option>
                                        @foreach($this->job->vmmfgUnits as $unit)
                                            <option value="{{$unit->id}}">
                                                #{{$unit->unit_no}}
                                            </option>
                                        @endforeach
                                    </x-input-select2> --}}
                                </div>
                            @endif

                        {{-- </div> --}}
                        <div class="form-group">
                            {{-- <div class="btn-group"> --}}
                                <button wire:click="resetFilters()" class="btn btn-outline-dark btn-block">Reset</button>
                            {{-- </div> --}}
                        </div>
                    </div>
                </div>
            </div>

            @if($this->unit_no and $this->unit->vmmfgScope)
                <ul class="list-group">
                    @forelse($this->unit->vmmfgScope->vmmfgTitles as $title)
                        @php
                            $sumItem = 0;
                            $sumDoneTask = 0;
                            $sumItem = count($title->vmmfgItems);
                            if($title->vmmfgItems) {
                                foreach($title->vmmfgItems as $item) {
                                    if($task = $item->vmmfgTasks()->whereVmmfgUnitId($this->unit->id)->first() and ($task->status === 1 or $task->status === 2)) {
                                        $sumDoneTask += 1;
                                    }
                                }
                            }
                        @endphp
                    <li class="list-group-item mt-2" style="background-color: #9bc2cf;">
                        <div class="row">
                        <span class="mr-auto">
                            {{$title->sequence}}.  {{$title->name}}
                        </span>
                        <span class="ml-auto" style="font-size: 18px;">
                            @if($sumItem === $sumDoneTask)
                                <span class="badge badge-pill badge-success">
                                    {{$sumDoneTask}}/{{$sumItem}}
                                </span>
                            @else
                                <span class="badge badge-pill badge-warning">
                                    {{$sumDoneTask}}/{{$sumItem}}
                                </span>
                            @endif
                        </span>
                        </div>
                    </li>
                        @foreach($title->vmmfgItems as $item)

                            @php
                                $showDone = false;
                                $showUndo = false;
                                $showChecked = false;
                                $showDoneTimeDoneBy = false;
                                $showCheckedBy = false;
                                $adminClickable = false;
                                $showUndoDoneBy = false;
                                $doneTime = '';

                                $task = $item->vmmfgTasks()->whereVmmfgUnitId($this->unit->id)->first();
                                if($task) {
                                    $doneBy = $task->doneBy ? $task->doneBy->name : null;
                                    $doneTime = \Carbon\Carbon::parse($task->done_time)->format('Y-m-d h:ia');
                                    $checkedBy = $task->checkedBy ? $task->checkedBy->name : null;
                                    $checkedTime = \Carbon\Carbon::parse($task->checked_time)->format('Y-m-d h:ia');
                                    $undoDoneBy = $task->undoDoneBy ? $task->undoDoneBy->name : null;
                                    $undoDoneTime = \Carbon\Carbon::parse($task->undo_done_time)->format('Y-m-d h:ia');

                                    $status = $task->status;
                                    switch($status) {
                                        case 0:
                                            $showDone = true;
                                            break;
                                        case 1:
                                            $showUndo = true;
                                            $showChecked = true;
                                            $showDoneTimeDoneBy = true;
                                            break;
                                        case 2:
                                            $showDoneTimeDoneBy = true;
                                            $showCheckedBy = true;
                                            break;
                                        case 99:
                                            $showDone = true;
                                            $showUndoDoneBy = true;
                                            break;
                                    }
                                }else {
                                    $showDone = true;
                                    $showUndo = false;
                                    $showChecked = false;
                                    $showDoneTimeDoneBy = false;
                                    $showCheckedBy = false;
                                }
                                if(!auth()->user()->hasPermissionTo('vmmfg-ops-checker')) {
                                    // $showChecked = false;
                                    $showUndoChecked = false;
                                }else {
                                    $showUndo = false;
                                    $adminClickable = true;
                                }

                            @endphp
                        <li class="list-group-item ml-2 clearfix" style="background-color: #e6f3f7;">
                            {{-- <div class="form-group"> --}}
                                <div class="row">
                                    <span class="mr-auto">
                                        {{$item->sequence}}.  {{$item->name}}
                                        <br>
                                        @if($item->remarks)
                                            <div class="p-2 mb-1 bg-light">
                                                <p>{{$item->remarks}}</p>
                                            </div>
                                        @endif
                                    </span>
                                    <span class="ml-auto">
                                        @if($item->attachments()->exists())
                                            <button class="btn btn-secondary" wire:key="item-area-{{$item->id}}" wire:click="showEditArea({{$item->id}})">
                                                @if($editArea === $item->id)
                                                    <i class="fas fa-caret-right"></i>
                                                @else
                                                    <i class="fas fa-caret-down"></i>
                                                @endif
                                            </button>
                                        @endif
                                    </span>
                                </div>
                                @if(!$item->attachments()->exists())
                                <div class="row pt-1">
                                    <span class="ml-auto" style="font-size: 13px;">
                                        @if($showDoneTimeDoneBy)
                                            @if($adminClickable)
                                                <a href="#" class="badge badge-success" style="font-size: 13px;" onclick="return confirm('Are you sure you want to Undo the Task?');" wire:click="onUndoClicked({{$task}})">
                                                    <i class="fas fa-check-circle"></i>
                                                    Done
                                                </a>
                                            @else
                                                <span class="badge badge-success" style="font-size: 13px;">
                                                    <i class="fas fa-check-circle"></i>
                                                    Done
                                                </span>
                                            @endif
                                            By: <span class="font-weight-bold">{{$doneBy}}</span> <br>
                                            On: <span class="font-weight-bold">{{$doneTime}}</span> <br>
                                        @endif
                                        @if($showCheckedBy)
                                            @if($adminClickable)
                                                <a href="#" class="badge badge-primary" style="font-size: 13px;" wire:click="onUndoCheckedClicked({{$task}})">
                                                    <i class="fas fa-check-circle"></i>
                                                    Checked
                                                </a>
                                            @else
                                                <span class="badge badge-primary" style="font-size: 13px;">
                                                    <i class="fas fa-check-circle"></i>
                                                    Checked
                                                </span>
                                            @endif
                                            By: <span class="font-weight-bold">{{$checkedBy}}</span> <br>
                                            On: <span class="font-weight-bold">{{$checkedTime}}</span> <br>
                                        @endif
                                        @if($showUndoDoneBy)
                                            <span class="badge badge-warning" style="font-size: 13px;">
                                                Undo
                                            </span>
                                            By: <span class="font-weight-bold">{{$undoDoneBy}}</span> <br>
                                            On: <span class="font-weight-bold">{{$undoDoneTime}}</span> <br>
                                        @endif
                                    </span>
                                </div>
                                <div class="row">
                                    <div class="btn-group ml-auto">
                                        @if($showDone)
                                            <button class="btn btn-outline-dark btn-xs-block float-right" wire:key="item-done-{{$item->id}}" wire:click="onDoneClicked({{$item}})">
                                                Done?
                                            </button>
                                        @endif
                                        @if($showUndo)
                                            <button class="btn btn-warning btn-xs-block" onclick="return confirm('Are you sure you want to Undo the Task?');" wire:key="item-undo-{{$item->id}}" wire:click="onUndoClicked({{$task}})">
                                                <i class="fas fa-undo-alt"></i>
                                            </button>
                                        @endif
                                        @if($showChecked)
                                            <button class="btn btn-info btn-xs-block" wire:key="item-check-{{$item->id}}" wire:click="onCheckedClicked({{$task}})">
                                                <i class="fas fa-check-double"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                @endif

                            {{-- </div> --}}
                            @if($editArea === $item->id)
                                <div class="form-group">
                                    @if($item->attachments)
                                        @foreach($item->attachments as $attachment)
                                            <div class="row">
                                            @php
                                                $ext = pathinfo($attachment->full_url, PATHINFO_EXTENSION);
                                            @endphp
                                            @if($ext === 'pdf')
                                                <embed src="{{$attachment->full_url}}" type="application/pdf" class="" style="min-height: 500px;" class="border border-dark">
                                            @else
                                                <img class="img-fluid border border-dark" src="{{$attachment->full_url}}" alt="" wire:click="onZoomPictureClicked({{$attachment}})"  data-toggle="modal" data-target="#zoom-picture-modal">
                                            @endif
                                            </div>
                                        @endforeach

                                        <div class="row">
                                            <div class="form-group pt-2">
                                                <form wire:submit.prevent="uploadAttachment({{$item->id}})" enctype="multipart/form-data">
                                                    <label for="file">
                                                        Upload File(s)
                                                    </label>
                                                    <input type="file" class="form-control-file" wire:model="file">
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fas fa-cloud-upload-alt"></i>
                                                    </button>
                                                </form>
                                                {{-- <x-input-file wire:model="file" multiple></x-input-file> --}}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            @if($task and $task->attachments)
                                                @foreach($task->attachments as $attachment)
                                                    <div class="row">
                                                        <div class="card" style="max-width:600px;width:100%;" wire:key="attachment-{{$attachment->id}}">
                                                            @php
                                                                $ext = pathinfo($attachment->full_url, PATHINFO_EXTENSION);
                                                            @endphp
                                                            @if($ext === 'pdf')
                                                                <embed src="{{$attachment->full_url}}" type="application/pdf" class="card-img-top" style="min-height: 500px;">
                                                            @else
                                                                <img class="card-img-top" src="{{$attachment->full_url}}" alt="" wire:click="onZoomPictureClicked({{$attachment}})"  data-toggle="modal" data-target="#zoom-picture-modal">
                                                            @endif
                                                            <div class="card-body">
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-warning btn-xs-block" wire:click="downloadAttachment({{$attachment}})">
                                                                        <i class="fas fa-cloud-download-alt"></i>
                                                                    </button>
                                                                    @if(!$task->is_done)
                                                                    <button type="button" class="btn btn-danger btn-xs-block" wire:click="deleteAttachment({{$attachment}})">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    @endif
                                    <div class="row pt-2">
                                        <span class="ml-auto" style="font-size: 13px;">
                                            @if($showDoneTimeDoneBy)
                                                @if($adminClickable)
                                                    <a href="#" class="badge badge-success" style="font-size: 13px;" onclick="return confirm('Are you sure you want to Undo the Task?');" wire:click="onUndoClicked({{$task}})">
                                                        <i class="fas fa-check-circle"></i>
                                                        Done
                                                    </a>
                                                @else
                                                    <span class="badge badge-success" style="font-size: 13px;">
                                                        <i class="fas fa-check-circle"></i>
                                                        Done
                                                    </span>
                                                @endif
                                                By: <span class="font-weight-bold">{{$doneBy}}</span> <br>
                                                On: <span class="font-weight-bold">{{$doneTime}}</span> <br>
                                            @endif
                                            @if($showCheckedBy)
                                                @if($adminClickable)
                                                    <a href="#" class="badge badge-primary" style="font-size: 13px;" wire:click="onUndoCheckedClicked({{$task}})">
                                                        <i class="fas fa-check-circle"></i>
                                                        Checked
                                                    </a>
                                                @else
                                                    <span class="badge badge-primary" style="font-size: 13px;">
                                                        <i class="fas fa-check-circle"></i>
                                                        Checked
                                                    </span>
                                                @endif
                                                By: <span class="font-weight-bold">{{$checkedBy}}</span> <br>
                                                On: <span class="font-weight-bold">{{$checkedTime}}</span> <br>
                                            @endif
                                            @if($showUndoDoneBy)
                                                <span class="badge badge-warning" style="font-size: 13px;">
                                                    Undo
                                                </span>
                                                By: <span class="font-weight-bold">{{$undoDoneBy}}</span> <br>
                                                On: <span class="font-weight-bold">{{$undoDoneTime}}</span> <br>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="row">
                                        <span class="btn-group ml-auto">
                                            @if($showDone)
                                                <button class="btn btn-outline-dark btn-xs-block" wire:key="item-done-normal-{{$item->id}}" wire:click="onDoneClicked({{$item}})" {{$task && $task->attachments()->exists() ? '' : 'disabled'}}>
                                                    Done?
                                                </button>
                                            @endif
                                            @if($showUndo)
                                                <button class="btn btn-warning btn-xs-block" onclick="return confirm('Are you sure you want to Undo the Task?');" wire:key="item-undo-{{$item->id}}" wire:click="onUndoClicked({{$task}})">
                                                    <i class="fas fa-undo-alt"></i>
                                                </button>
                                            @endif
                                            @if($showChecked)
                                                <button class="btn btn-info btn-xs-block" wire:key="item-check-normal-{{$item->id}}" wire:click="onCheckedClicked({{$task}})">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                            @endif
                                        </span>
                                    </div>

                                </div>
                            @endif
                        </li>
                        @endforeach
                    @empty
                        <li class="list-group-item text-center">
                            No Scope Attached to this Unit.
                        </li>
                    @endforelse
                </ul>
            @else
                @if($this->unit_no)
                    <ul class="list-group">
                        <li class="list-group-item text-center">
                            No Scope Attached to this Unit.
                        </li>
                    </ul>
                @endif
            @endif
            <x-zoom-modal id="zoom-picture-modal">
                <x-slot name="content">
                    <img class="img-fluid border border-dark" src="{{$zoomPictureUrl}}" alt="">
                </x-slot>
            </x-zoom-modal>
        </div>
    </div>

</div>
