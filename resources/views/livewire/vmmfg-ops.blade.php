<div>
    @inject('vmmfgTask', 'App\Models\VmmfgTask')
    @php
        $profile = \App\Models\Profile::with('profileSetting')->where('is_primary', 1)->first();
    @endphp
    <div>
        <div>
            <x-flash></x-flash>
            <h2>QA/QC</h2>
            <hr>

            <div class="p-3 bg-light">
                <div class="row p-1 bg-white">
                    <div class="form-group col-xs-12 col-md-6 col-sm-6">
                        <label>
                            [Filter] Is Completed?
                        </label>
                        <select name="is_completed" wire:model="is_completed" class="select form-control">
                            <option value="">All</option>
                            <option value="1">Yes</option>
                            <option value="2">No</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>
                        {{$profile->profileSetting ? $profile->profileSetting->vmmfg_job_batch_no_title : 'Batch No'}} -
                        {{$profile->profileSetting ? $profile->profileSetting->vmmfg_unit_vend_id_title : 'Vend ID'}}
                        - #Unit No
                        (Model)
                        - Scope
                        (Start Date)
                        (Completion Date)
                    </label>
                    <select name="unit_id" wire:model="unit_id"  class="select form-control">
                        <option value="">Select..</option>
                        @foreach($units as $unit)
                            {{-- @if(!$unit->completion_date) --}}
                            <option value="{{$unit->id}}">
                                {{$unit->vmmfgJob->batch_no}}
                                @if($unit->vend_id)
                                    - {{$unit->vend_id}}
                                @endif
                                @if($unit->unit_no)
                                    - #{{$unit->unit_no}}
                                @endif
                                @if($unit->model)
                                    ({{$unit->model}})
                                @endif
                                @if($unit->vmmfgScope)
                                    - {{$unit->vmmfgScope->name}}
                                @endif
                                @if($unit->order_date)
                                    (Start: {{$unit->order_date}})
                                @endif
                                @if($unit->completion_date)
                                    (Complete: {{$unit->completion_date}})
                                @endif
                            </option>
                            {{-- @endif --}}
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    @if($this->unit_id)
                        <button class="btn btn-success btn-block" wire:click="exportPdf">
                            <i class="far fa-file-pdf"></i>
                            Export PDF
                        </button>
                    @endif
                    <button wire:click.prevent="resetFilters()" class="btn btn-outline-dark btn-block">Reset</button>
                </div>
            </div>

            @if($vmmfgUnit)

            @if($vmmfgUnit->first()->bindedCompletionUnit)
            <div style="padding: 5px 0px 10px 0px;">
                This binded Unit depends on the current unit completion date
                <br>
                {{-- <a class="btn btn-success btn-block" href="/vmmfg-ops?unit_id={{$vmmfgUnit->first()->bindedCompletionUnit->id}}"> --}}
                <a class="btn btn-success btn-block" href="/vmmfg-ops?unit_id={{$vmmfgUnit->first()->bindedCompletionUnit->id}}&is_completed=''">
                    {{$vmmfgUnit->first()->bindedCompletionUnit->vmmfgJob->batch_no}} ({{$vmmfgUnit->first()->bindedCompletionUnit->unit_no}})
                    <br> {{$vmmfgUnit->first()->bindedCompletionUnit->vend_id}}
                    <br> {{$vmmfgUnit->first()->bindedCompletionUnit->model}}
                </a>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <tr class="table-secondary">
                        <th class="text-center font-weight-bold text-dark">
                            {{$profile->profileSetting ? $profile->profileSetting->vmmfg_job_batch_no_title : 'Batch No'}}
                        </th>
                        <th class="text-center font-weight-bold text-dark">
                            {{$profile->profileSetting ? $profile->profileSetting->vmmfg_unit_vend_id_title : 'Vend ID'}}
                        </th>
                        <th class="text-center font-weight-bold text-dark">
                            Unit No
                        </th>
                        <th class="text-center font-weight-bold text-dark">
                            Model
                        </th>
                        <th class="text-center font-weight-bold text-dark">
                            Scope
                        </th>
                        <th class="text-center font-weight-bold text-dark">
                            Start Date
                        </th>
                        <th class="text-center font-weight-bold text-dark">
                            End Date
                        </th>
                    </tr>
                    <tr class="row_edit">
                        <td class="text-center">
                            {{ $vmmfgUnit->first()->vmmfgJob->batch_no }}
                        </td>
                        <td class="text-center">
                            {{ $vmmfgUnit->first()->vend_id }}
                        </td>
                        <td class="text-center">
                            {{ $vmmfgUnit->first()->unit_no }}
                        </td>
                        <td class="text-center">
                            {{ $vmmfgUnit->first()->model }}
                        </td>
                        <td class="text-center">
                            {{ $vmmfgUnit->first()->vmmfgScope->name }}
                        </td>
                        <td class="text-center">
                            {{ $vmmfgUnit->first()->order_date }}
                        </td>
                        <td class="text-center">
                            {{ $vmmfgUnit->first()->completion_date }}
                        </td>
                    </tr>
                </table>
            </div>
            {{-- @dd($vmmfgUnit->toArray()) --}}
                <ul class="list-group">
                    @forelse($vmmfgUnit->first()->vmmfgScope->vmmfgTitles as $index => $title)

                    <li class="list-group-item mt-2" style="background-color: #9bc2cf;" wire:key="title-{{$title->id}}">
                        @php
                            $sumItem = 0;
                            $sumDoneTask = 0;
                            $sumItem = count($title->vmmfgItems);
                            if($title->vmmfgItems) {
                                // @dd($title->vmmfgItems->toArray());
                                foreach($title->vmmfgItems as $item) {
                                    // @dd($item->toArray());
                                    if($item->vmmfgTasks) {
                                        // $sumDoneTask = $item->vmmfgTasks()->whereIn('status', [$vmmfgTask::STATUS_DONE, $vmmfgTask::STATUS_CHECKED, $vmmfgTask::STATUS_CANCELLED])->count();

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
                        <li class="list-group-item ml-2 clearfix" style="background-color: #e6f3f7;" wire:key="item-{{$item->id}}">
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

                            <div class="row">
                                <span class="mr-auto">
                                    {{$item->sequence}}.  {{$item->name}}
                                    <br>
                                    @if($item->remarks)
                                        <div class="p-2 mb-1 bg-light">
                                            <p>{{$item->remarks}}</p>
                                        </div>
                                    @endif
                                    @if($item->flag_id == array_search('New', \App\Models\VmmfgItem::FLAGS))
                                    <span class="badge badge-danger">
                                    New
                                    </span>
                                    @elseif($item->flag_id == array_search('Updated', \App\Models\VmmfgItem::FLAGS))
                                    <span class="badge badge-success">
                                    Updated
                                    </span>
                                    @endif
                                </span>
                                <span class="ml-auto">
                                    {{-- @if($item->attachments()->exists()) --}}
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

                                    {{-- @endif --}}
                                </span>
                            </div>
{{--
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

                                @endif --}}

                            {{-- </div> --}}
                            {{-- @if($this->editArea === $item->id) --}}
                            @if(array_search($item->id, $editArea, true))
                                <div class="form-group" id="item-dropdown-{{$item->id}}">
                                    @if($item->attachments()->exists())
                                    {{-- @dd(count($item->attachments, $item->attachments) --}}
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
                                                <img class="img-fluid border border-dark" style="max-height: 600px;" src="{{$attachment->full_url}}" alt="" wire:click.prevent="onZoomPictureClicked({{$attachment}})"  data-toggle="modal" data-target="#zoom-picture-modal">
                                            @endif
                                            </div>
                                        @endforeach
{{--
                                        <div class="row">
                                            <hr>
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
                                        </div> --}}
                                        <hr>
                                        <x-input-file class="form-control-file"  wire:model="file" wire:key="upload-item-{{$item->id}}" id="{{$item->id}}" multiple></x-input-file>
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
                                                                <img class="card-img-top" src="{{$attachment->full_url}}" style="max-height: 600px;" alt="" wire:click.prevent="onZoomPictureClicked({{$attachment}})"  data-toggle="modal" data-target="#zoom-picture-modal">
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

                                    <hr>

                                    {{-- @dd($errors->has('form.remarks.'.$item->id), $errors->toArray()); --}}
                                    {{-- @dd($this->form) --}}
                                    <div class="form-group pt-3">
                                        <label for="remarks">Remarks 备注</label>
                                        @if($item->is_required)
                                            <label for="art" style="color: red;">*</label>
                                        @endif
                                        {{-- <textarea name="remarks" class="form-control" rows="3" wire:model="form.remarks.{{$item->id}}" style="min-width: 100%;" placeholder="{{$item->is_required ? 'Compulsory to fill 必须填写' : '(Optional)'}}"></textarea> --}}
                                        <textarea name="remarks" wire:model.defer="form.remarks.{{$item->id}}" class="form-control  @error('form.remarks.'.$item->id) is-invalid @enderror" rows="3" style="min-width: 100%;" placeholder="{{$item->is_required ? 'Compulsory to fill 必须填写' : '(Optional)'}}" {{isset($task) && $task->is_done ? 'disabled' : ''}} ></textarea>
                                        @error('form.remarks.'.$item->id)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        {{-- <button class="btn btn-success" wire:click.prevent="saveRemarks()">
                                            <i class="far fa-save"></i>
                                        </button> --}}
                                    </div>

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
                                            @php
                                                $disabled = false;
                                                if($item->attachments()->exists() and (!$task or !$task->attachments()->exists())) {
                                                    $disabled = true;
                                                }
                                            @endphp
                                            @if($showDone)
                                                <button class="btn btn-outline-dark btn-xs-block" wire:key="item-done-normal-{{$item->id}}" wire:click.prevent="onDoneClicked({{$item}})" {{$disabled ?  'disabled' : ''}}>
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
                {{-- {{$vmmfgUnit}} --}}
                {{-- @dd($vmmfgUnit->first()->referCompletionUnit->toArray()); --}}
                @if($vmmfgUnit->first()->referCompletionUnit)
                <div class="form-group pt-3">
                    <label for="completion_date">
                        This Partial Completion Date
                    </label>
                    <input type="date" wire:model.defer="form.completion_date" class="form-control" value="{{$vmmfgUnit->first()->completion_date}}">
                </div>
                <button type="button" class="btn btn-success btn-xs-block" wire:click.prevent="saveCompletionDate({{$vmmfgUnit}})">
                    Save Partial Completion Date
                </button>
                <div style="padding: 10px 0px 3px 6px;">
                    Please refer to this unit for final completion
                    <br>
                    {{-- <a class="btn btn-success btn-block" href="/vmmfg-ops?unit_id={{$vmmfgUnit->first()->referCompletionUnit->id}}"> --}}
                    <a class="btn btn-success btn-block" href="/vmmfg-ops?unit_id={{$vmmfgUnit->first()->referCompletionUnit->id}}&is_completed=''">
                        {{$vmmfgUnit->first()->referCompletionUnit->vmmfgJob->batch_no}} ({{$vmmfgUnit->first()->referCompletionUnit->unit_no}})
                        <br> {{$vmmfgUnit->first()->referCompletionUnit->vend_id}}
                        <br> {{$vmmfgUnit->first()->referCompletionUnit->model}}
                    </a>
                </div>
                @else
                    <div class="form-group pt-3">
                        <label for="completion_date">
                            Completion Date
                        </label>
                        <input type="date" wire:model.defer="form.completion_date" class="form-control" value="{{$vmmfgUnit->first()->completion_date}}">
                    </div>
                    <button type="button" class="btn btn-success btn-xs-block" wire:click.prevent="saveCompletionDate({{$vmmfgUnit}})">
                        Save Completion Date
                    </button>
                @endif
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
                    <img class="img-fluid border border-dark" style="max-height: 600px;" src="{{$zoomPictureUrl}}" alt="">
                </x-slot>
            </x-zoom-modal>
        </div>
    </div>

</div>
