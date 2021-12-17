<div>
    @inject('vmmfgTask', 'App\Models\VmmfgTask')
    <div>
        <div>
            <x-flash></x-flash>
            <h2>QA/QC</h2>
            <hr>

            <div class="">
                <div>
                    {{-- <div class="p-3" style="background-color: #d8d8d8;">
                        <label for="filter" class="font-weight-bold"><u>Filters</u></label>
                        <div class="form-row">
                            <div class="form-group col-md-4 col-xs-12">
                                <label>
                                    User
                                </label>
                                <select name="user_id" wire:model="form.user_id" class="select form-control">
                                    <option value="">Select...</option>
                                    @foreach($users as $user)
                                        <option value="{{$user->id}}">
                                            {{$user->name}} ({{$user->roles[0]->name}})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-xs-12">
                                <label>
                                    Date From
                                </label>

                                <input wire:model="form.date_from" type="date" class="form-control" placeholder="Date From">
                            </div>
                            <div class="form-group col-md-4 col-xs-12">
                                <label>
                                    Date To
                                </label>
                                <input wire:model="form.date_to" type="date" class="form-control" placeholder="Date To">
                            </div>
                        </div>
                    </div> --}}

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
                                <select name="batch_no" wire:model="job_id" class="select form-control">
                                    <option value="">Select..</option>
                                    @foreach($jobs as $job)
                                        <option value="{{$job->id}}">
                                            #{{$job->batch_no}} - {{$job->model}}
                                            @if($job->vmmfgUnits) ({{count($job->vmmfgUnits)}} units) @endif
                                            @if($job->order_date) (Start: {{$job->order_date}}) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- @dd($this->batch_no) --}}

                            @if($this->form['job_id'])
                                <div class="form-group">
                                    <label>
                                        Unit No
                                    </label>
                                    <select name="unit_no" wire:model="unit_id" class="select form-control">
                                        <option value="">Select..</option>
                                        @foreach($this->job->vmmfgUnits as $unit)
                                            @if(!$unit->completion_date)
                                                <option value="{{$unit->id}}">
                                                    #{{$unit->unit_no}}
                                                    @if($unit->vend_id)
                                                        [{{$unit->vend_id}}]
                                                    @endif
                                                </option>
                                            @endif
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
{{--
                                @if($vmmfgUnit)
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model="is_incomplete">
                                        <label class="form-check-label" for="is_incomplete">
                                            Show Incomplete
                                        </label>
                                    </div>
                                </div>
                                @endif --}}
                            @endif

                        {{-- </div> --}}
                        <div class="form-group">
                            {{-- <div class="btn-group"> --}}
                                <button wire:click.prevent="resetFilters()" class="btn btn-outline-dark btn-block">Reset</button>
                            {{-- </div> --}}
                        </div>
                    </div>
                </div>
            </div>

            @if($vmmfgUnit)
            {{-- @dd($vmmfgUnit->toArray()) --}}
                <ul class="list-group" wire:key="unit-list-{{$vmmfgUnit->first()->id}}">
                    @forelse($vmmfgUnit->first()->vmmfgScope->vmmfgTitles as $index => $title)
                        @php
                            $sumItem = 0;
                            $sumDoneTask = 0;
                            $sumItem = count($title->vmmfgItems);
                            if($title->vmmfgItems) {
                                // @dd($title->vmmfgItems->toArray());
                                foreach($title->vmmfgItems as $item) {
                                    if($item->vmmfgTasks) {
                                        foreach($item->vmmfgTasks as $task) {
                                            if(
                                                $task->status === $vmmfgTask::STATUS_DONE
                                                or $task->status === $vmmfgTask::STATUS_CHECKED
                                                or $task->status === $vmmfgTask::STATUS_CANCELLED
                                            ) {
                                                $sumDoneTask += 1;
                                            }
                                        }
                                    }
                                }
                            }
                        @endphp
                    <li class="list-group-item mt-2" style="background-color: #9bc2cf;" wire:key="title-{{$index}}">
                        <div class="row">
                        <span class="mr-auto">
                            {{$title->sequence}}.  {{$title->name}}
                            @if($title->vmmfgTitleCategory)
                                <span class="badge badge-warning">
                                    {{$title->vmmfgTitleCategory->name}}
                                </span>
                            @endif
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
                        @foreach($title->vmmfgItems as $index => $item)

                            @php
                                $showDone = false;
                                $showUndo = false;
                                $showChecked = false;
                                $showDoneTimeDoneBy = false;
                                $showCheckedBy = false;
                                $adminClickable = false;
                                $showUndoDoneBy = false;
                                $showCancelledBy = false;
                                $doneTime = '';
                                // $isShowIncomplete = $this->

                                // dd($item->vmmfgTasks);
                                $task = $item->vmmfgTasks ? $item->vmmfgTasks->first() : null;
                                if($task) {
                                    $doneBy = $task->doneBy ? $task->doneBy->name : null;
                                    $doneTime = \Carbon\Carbon::parse($task->done_time)->format('Y-m-d h:ia');
                                    $checkedBy = $task->checkedBy ? $task->checkedBy->name : null;
                                    $checkedTime = \Carbon\Carbon::parse($task->checked_time)->format('Y-m-d h:ia');
                                    $undoDoneBy = $task->undoDoneBy ? $task->undoDoneBy->name : null;
                                    $undoDoneTime = \Carbon\Carbon::parse($task->undo_done_time)->format('Y-m-d h:ia');
                                    $cancelledBy = $task->cancelledBy ? $task->cancelledBy->name : null;
                                    $cancelledTime = \Carbon\Carbon::parse($task->cancelled_time)->format('Y-m-d h:ia');

                                    $status = $task->status;
                                    switch($status) {
                                        case $vmmfgTask::STATUS_NEW:
                                            $showDone = true;
                                            break;
                                        case $vmmfgTask::STATUS_DONE:
                                            $showUndo = true;
                                            $showChecked = true;
                                            $showDoneTimeDoneBy = true;
                                            break;
                                        case $vmmfgTask::STATUS_CHECKED:
                                            $showDoneTimeDoneBy = true;
                                            $showCheckedBy = true;
                                            break;
                                        case $vmmfgTask::STATUS_UNDONE:
                                            $showDone = true;
                                            $showDoneTimeDoneBy = true;
                                            $showUndoDoneBy = true;
                                            break;
                                        case $vmmfgTask::STATUS_CANCELLED:
                                            $showCancelledBy = true;
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
                        {{-- @if((!$this->form['user_id'] or ($this->form['user_id'] and $task)) and ((!$this->form['date_from'] and !$this->form['date_to']) or ($this->form['date_from'] or $this->form['date_to']) and $task)) --}}
                        {{-- @if((($this->form['is_incomplete'] and !$task) or ($this->form['is_incomplete'] and $task and !$task->is_done)) or !$this->form['is_incomplete']) --}}
                        <li class="list-group-item ml-2 clearfix" style="background-color: #e6f3f7;" wire:key="item-{{$index}}">
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
                                            {{-- @if($this->editArea !== $item->id) --}}
                                            @if(!array_search($item->id, $editArea, true))
                                                <span class="" style="font-size: 13px;">
                                                    @if($showDoneTimeDoneBy)
                                                        @if(!$showUndoDoneBy)
                                                            @if($adminClickable)
                                                                <a href="" class="badge badge-success" style="font-size: 13px;" onclick="return confirm('Are you sure you want to Undo the Task?') || event.stopImmediatePropagation()" wire:click.prevent="onUndoClicked({{$task}})">
                                                                    <i class="fas fa-check-circle"></i>
                                                                    Done
                                                                </a>
                                                            @else
                                                                <span class="badge badge-success" style="font-size: 13px;">
                                                                    <i class="fas fa-check-circle"></i>
                                                                    Done
                                                                </span>
                                                            @endif
                                                        @else
                                                            P.Done
                                                        @endif
                                                        By: <span class="font-weight-bold">{{$doneBy}}</span> <br>
                                                        On: <span class="font-weight-bold">{{$doneTime}}</span> <br>
                                                    @endif
                                                    @if($showCheckedBy)
                                                        @if($adminClickable)
                                                            <a href="" class="badge badge-primary" style="font-size: 13px;" wire:click.prevent="onUndoCheckedClicked({{$task}})">
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
                                                    @if($showCancelledBy)
                                                        @if($adminClickable)
                                                            <a href="" class="badge badge-danger" style="font-size: 13px;" wire:click.prevent="onUndoCancelledClicked({{$task}})">
                                                                <i class="far fa-times-circle"></i>
                                                                Cancelled
                                                            </a>
                                                        @else
                                                            <span class="badge badge-danger" style="font-size: 13px;">
                                                                <i class="far fa-times-circle"></i>
                                                                Cancelled
                                                            </span>
                                                        @endif
                                                        By: <span class="font-weight-bold">{{$cancelledBy}}</span> <br>
                                                        On: <span class="font-weight-bold">{{$cancelledTime}}</span> <br>
                                                    @endif
                                                </span>
                                            @endif
                                            <div class="btn-group float-right">
                                                @if($showChecked)
                                                    <button class="btn btn-info btn-xs-block" wire:key="item-check-{{$item->id}}" wire:click.prevent="onCheckedClicked({{$task}})">
                                                        <i class="fas fa-check-double"></i>
                                                    </button>
                                                @endif
                                                <a href="#item-dropdown-{{$item->id}}" class="btn btn-secondary" wire:key="item-area-{{$item->id}}" wire:click.prevent="showEditArea({{$item->id}})">
                                                    {{-- @if($this->editArea === $item->id) --}}
                                                    @if(array_search($item->id, $editArea))
                                                        <i class="fas fa-caret-right"></i>
                                                    @else
                                                        <i class="fas fa-caret-down"></i>
                                                    @endif
                                                </a>
                                            </div>

                                        @endif
                                    </span>
                                </div>
                                @if(!$item->attachments()->exists())
                                <div class="row pt-1" >
                                    <span class="ml-auto" style="font-size: 13px;">
                                        @if($showDoneTimeDoneBy)
                                            @if(!$showUndoDoneBy)
                                                @if($adminClickable)
                                                    <a href="" class="badge badge-success" style="font-size: 13px;" onclick="return confirm('Are you sure you want to Undo the Task?') || event.stopImmediatePropagation()" wire:click.prevent="onUndoClicked({{$task}})">
                                                        <i class="fas fa-check-circle"></i>
                                                        Done
                                                    </a>
                                                @else
                                                    <span class="badge badge-success" style="font-size: 13px;">
                                                        <i class="fas fa-check-circle"></i>
                                                        Done
                                                    </span>
                                                @endif
                                            @else
                                                P.Done
                                            @endif
                                            By: <span class="font-weight-bold">{{$doneBy}}</span> <br>
                                            On: <span class="font-weight-bold">{{$doneTime}}</span> <br>
                                        @endif
                                        @if($showCheckedBy)
                                            @if($adminClickable)
                                                <a href="" class="badge badge-primary" style="font-size: 13px;" wire:click.prevent="onUndoCheckedClicked({{$task}})">
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
                                        @if($showCancelledBy)
                                            @if($adminClickable)
                                                <a href="" class="badge badge-danger" style="font-size: 13px;" wire:click.prevent="onUndoCancelledClicked({{$task}})">
                                                    <i class="far fa-times-circle"></i>
                                                    Cancelled
                                                </a>
                                            @else
                                                <span class="badge badge-danger" style="font-size: 13px;">
                                                    <i class="far fa-times-circle"></i>
                                                    Cancelled
                                                </span>
                                            @endif
                                            By: <span class="font-weight-bold">{{$cancelledBy}}</span> <br>
                                            On: <span class="font-weight-bold">{{$cancelledTime}}</span> <br>
                                        @endif
                                    </span>
                                </div>
                                <div class="row">
                                    <div class="btn-group ml-auto">
                                        @if($showDone)
                                            <button class="btn btn-outline-dark btn-xs-block" wire:key="item-done-{{$item->id}}" wire:click.prevent="onDoneClicked({{$item}})">
                                                Done?
                                            </button>
                                            <button class="btn btn-danger btn-sm btn-xs-block" wire:key="item-cancelled-{{$item->id}}" wire:click.prevent="onCancelledClicked({{$item}})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        @if($showUndo)
                                            <button class="btn btn-warning btn-xs-block" onclick="return confirm('Are you sure you want to Undo the Task?') || event.stopImmediatePropagation()" wire:key="item-undo-{{$item->id}}" wire:click.prevent="onUndoClicked({{$task}})">
                                                <i class="fas fa-undo-alt"></i>
                                            </button>
                                        @endif
                                        @if($showChecked)
                                            <button class="btn btn-info btn-xs-block" wire:key="item-check-{{$item->id}}" wire:click.prevent="onCheckedClicked({{$task}})">
                                                <i class="fas fa-check-double"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                @endif

                            {{-- </div> --}}
                            {{-- @if($this->editArea === $item->id) --}}
                            @if(array_search($item->id, $editArea, true))
                                <div class="form-group" id="item-dropdown-{{$item->id}}">
                                    @if($item->attachments)
                                        @foreach($item->attachments as $attachment)
                                            <div class="row">
                                            @php
                                                $ext = pathinfo($attachment->full_url, PATHINFO_EXTENSION);
                                            @endphp
                                            @if($ext === 'pdf')
                                                <embed src="{{$attachment->full_url}}" type="application/pdf" class="" style="min-height: 500px;" class="border border-dark">
                                            @elseif($ext === 'mov' or $ext === 'mp4')
                                                <div class="embed-responsive embed-responsive-16by9">
                                                    <video class=" embed-responsive-item video-js" controls>
                                                        <source src="{{$attachment->full_url}}">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                </div>
                                            @else
                                                <img class="img-fluid border border-dark" src="{{$attachment->full_url}}" alt="" wire:click.prevent="onZoomPictureClicked({{$attachment}})"  data-toggle="modal" data-target="#zoom-picture-modal">
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
                                                            @elseif($ext === 'mov' or $ext === 'mp4')
                                                                <div class="embed-responsive embed-responsive-16by9">
                                                                    <video class=" embed-responsive-item video-js" controls>
                                                                        <source src="{{$attachment->full_url}}">
                                                                        Your browser does not support the video tag.
                                                                    </video>
                                                                </div>
                                                            @else
                                                                <img class="card-img-top" src="{{$attachment->full_url}}" alt="" wire:click.prevent="onZoomPictureClicked({{$attachment}})"  data-toggle="modal" data-target="#zoom-picture-modal">
                                                            @endif
                                                            <div class="card-body">
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-warning btn-xs-block" wire:click.prevent="downloadAttachment({{$attachment}})">
                                                                        <i class="fas fa-cloud-download-alt"></i>
                                                                    </button>
                                                                    @if(!$task->is_done)
                                                                    <button type="button" class="btn btn-danger btn-xs-block" wire:click.prevent="deleteAttachment({{$attachment}})">
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
                                                @if(!$showUndoDoneBy)
                                                    @if($adminClickable)
                                                        <a href="#" class="badge badge-success" style="font-size: 13px;" onclick="return confirm('Are you sure you want to Undo the Task?') || event.stopImmediatePropagation()" wire:click.prevent="onUndoClicked({{$task}})">
                                                            <i class="fas fa-check-circle"></i>
                                                            Done
                                                        </a>
                                                    @else
                                                        <span class="badge badge-success" style="font-size: 13px;">
                                                            <i class="fas fa-check-circle"></i>
                                                            Done
                                                        </span>
                                                    @endif
                                                @else
                                                    P.Done
                                                @endif
                                                By: <span class="font-weight-bold">{{$doneBy}}</span> <br>
                                                On: <span class="font-weight-bold">{{$doneTime}}</span> <br>
                                            @endif
                                            @if($showCheckedBy)
                                                @if($adminClickable)
                                                    <a href="" class="badge badge-primary" style="font-size: 13px;" wire:click.prevent="onUndoCheckedClicked({{$task}})">
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
                                           @if($showCancelledBy)
                                                @if($adminClickable)
                                                    <a href="#" class="badge badge-danger" style="font-size: 13px;" wire:click.prevent="onUndoCancelledClicked({{$task}})">
                                                        <i class="far fa-times-circle"></i>
                                                        Cancelled
                                                    </a>
                                                @else
                                                    <span class="badge badge-danger" style="font-size: 13px;">
                                                        <i class="far fa-times-circle"></i>
                                                        Cancelled
                                                    </span>
                                                @endif
                                                By: <span class="font-weight-bold">{{$cancelledBy}}</span> <br>
                                                On: <span class="font-weight-bold">{{$cancelledTime}}</span> <br>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="row">
                                        <span class="btn-group ml-auto">
                                            @if($showDone)
                                                <button class="btn btn-outline-dark btn-xs-block" wire:key="item-done-normal-{{$item->id}}" wire:click.prevent="onDoneClicked({{$item}})" {{$task && $task->attachments()->exists() ? '' : 'disabled'}}>
                                                    Done?
                                                </button>
                                                <button class="btn btn-danger btn-sm btn-xs-block" wire:key="item-cancelled-{{$item->id}}" wire:click.prevent="onCancelledClicked({{$item}})">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                            @if($showUndo)
                                                <button class="btn btn-warning btn-xs-block" onclick="return confirm('Are you sure you want to Undo the Task?') || event.stopImmediatePropagation()" wire:key="item-undo-{{$item->id}}" wire:click.prevent="onUndoClicked({{$task}})">
                                                    <i class="fas fa-undo-alt"></i>
                                                </button>
                                            @endif
                                            @if($showChecked)
                                                <button class="btn btn-info btn-xs-block" wire:key="item-check-normal-{{$item->id}}" wire:click.prevent="onCheckedClicked({{$task}})">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                            @endif

                                        </span>
                                    </div>

                                </div>
                            @endif
                        </li>
                        {{-- @endif --}}
                        @endforeach
                    @empty
                        <li class="list-group-item text-center">
                            No Scope Attached to this Unit.
                        </li>
                    @endforelse
                </ul>
            @else
                @if($vmmfgUnit)
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
