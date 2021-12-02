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
                                $showDone = true;
                                $showUndo = false;
                                $showChecked = false;
                                $showDoneTimeDoneBy = false;
                                $showCheckedBy = false;
                                $showUndoChecked = false;

                                $task = $item->vmmfgTasks()->whereVmmfgUnitId($this->unit->id)->first();
                                if($task) {
                                    $status = $task->status;

                                    switch($status) {
                                        case 0:
                                            $showDone = true;
                                            $showUndo = false;
                                            $showChecked = false;
                                            $showDoneTimeDoneBy = false;
                                            $showCheckedBy = false;
                                            $showUndoChecked = false;
                                            break;
                                        case 1:
                                            $showDone = false;
                                            $showUndo = true;
                                            $showChecked = true;
                                            $showDoneTimeDoneBy = true;
                                            $showCheckedBy = false;
                                            $showUndoChecked = false;
                                            break;
                                        case 2:
                                            $showDone = false;
                                            $showUndo = false;
                                            $showChecked = false;
                                            $showDoneTimeDoneBy = true;
                                            $showCheckedBy = true;
                                            $showUndoChecked = true;
                                            break;
                                    }
                                }else {
                                    $showDone = true;
                                    $showUndo = false;
                                    $showChecked = false;
                                    $showDoneTimeDoneBy = false;
                                    $showCheckedBy = false;
                                    $showUndoChecked = false;
                                }
                                if(!auth()->user()->hasPermissionTo('vmmfg-ops-checker')) {
                                    $showChecked = false;
                                }

                            @endphp
                        <li class="list-group-item ml-2 clearfix" style="background-color: #e6f3f7;">
                            {{-- <div class="form-group"> --}}
                                <div class="row">
                                    <span class="mr-auto">
                                        {{$item->sequence}}.  {{$item->name}}
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
                                <div class="row pt-2">
                                    <span class="ml-auto">
                                        @if($showDone)
                                            <button class="btn btn-success btn-xs-block float-right" wire:key="item-done-{{$item->id}}" wire:click="onDoneClicked({{$item}})">
                                                <i class="fas fa-check-circle"></i>
                                                Done
                                            </button>
                                        @endif
                                        @if($showUndo)
                                            <button class="btn btn-warning btn-xs-block float-right" wire:key="item-undo-{{$item->id}}" wire:click="onUndoClicked({{$task}})">
                                                <i class="fas fa-undo-alt"></i>
                                                Not yet Done
                                            </button>
                                        @endif
                                        @if($showChecked)
                                            <button class="btn btn-secondary btn-xs-block float-right" wire:key="item-check-{{$item->id}}" wire:click="onCheckedClicked({{$task}})">
                                                <i class="fas fa-check-circle"></i>
                                                Checked?
                                            </button>
                                        @endif
                                        @if($showUndoChecked)
                                            <button class="btn btn-info btn-xs-block float-right" wire:key="item-undo-check-{{$item->id}}" wire:click="onUndoCheckedClicked({{$task}})">
                                                <i class="fas fa-undo-alt"></i>
                                                Un-Checked
                                            </button>
                                        @endif
                                    </span>
                                </div>
                                <div class="row">
                                    <span class="ml-auto" style="font-size: 12px;">
                                        @if($showDoneTimeDoneBy)
                                            By: <span class="font-weight-bold">{{$task->doneBy->name}}</span> <br>
                                            On: <span class="font-weight-bold">{{$task->doneTime}}</span> <br>
                                        @endif
                                        @if($showCheckedBy)
                                            Checked: <span class="font-weight-bold">{{$task->checkedBy->name}}</span>
                                        @endif
                                    </span>
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
                                                <img class="img-fluid border border-dark" src="{{$attachment->full_url}}" alt="">
                                            @endif
                                            </div>
                                        @endforeach

                                        <div class="row">
                                            <div class="form-group pt-2">
                                                <form wire:submit.prevent="uploadAttachment({{$item->id}})">
                                                    <label for="file">
                                                        Upload File(s)
                                                    </label>
                                                    <input type="file" class="form-control-file" wire:model="file" {{$task && $task->is_done ? 'disabled' : ''}}>
                                                    <button type="submit" class="btn btn-success" {{$task && $task->is_done ? 'disabled' : ''}}>
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
                                                                <img class="card-img-top" src="{{$attachment->full_url}}" alt="">
                                                            @endif
                                                            <div class="card-body">
                                                                <div class="btn-group d-none d-sm-block">
                                                                    <button type="button" class="btn btn-warning" wire:click="downloadAttachment({{$attachment}})">
                                                                        <i class="fas fa-cloud-download-alt"></i>
                                                                        Download
                                                                    </button>
                                                                    <button type="button" class="btn btn-danger" wire:click="deleteAttachment({{$attachment}})" {{$task->is_done ? 'disabled' : ''}}>
                                                                        <i class="fas fa-trash"></i>
                                                                        Delete
                                                                    </button>
                                                                </div>
                                                                <div class="d-block d-sm-none">
                                                                    <button type="button" class="btn btn-block btn-warning" wire:click="downloadAttachment({{$attachment}})">
                                                                        <i class="fas fa-cloud-download-alt"></i>
                                                                        Download
                                                                    </button>
                                                                    <button type="button" class="btn btn-block btn-danger" wire:click="deleteAttachment({{$attachment}})">
                                                                        <i class="fas fa-trash"></i>
                                                                        Delete
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    @endif

                                    <div class="row pt-2">
                                        <span class="ml-auto">
                                            @if($showDone)
                                                <button class="btn btn-success btn-xs-block float-right" wire:key="item-done-normal-{{$item->id}}" wire:click="onDoneClicked({{$item}})" {{$task && $task->attachments()->exists() ? '' : 'disabled'}}>
                                                    <i class="fas fa-check-circle"></i>
                                                    Done
                                                </button>
                                            @endif
                                            @if($showUndo)
                                                <button class="btn btn-warning btn-xs-block float-right" wire:key="item-undo-normal-{{$item->id}}" wire:click="onUndoClicked({{$task}})">
                                                    <i class="fas fa-undo-alt"></i>
                                                    Not yet Done
                                                </button>
                                            @endif
                                            @if($showChecked)
                                                <button class="btn btn-secondary btn-xs-block float-right" wire:key="item-check-normal-{{$item->id}}" wire:click="onCheckedClicked({{$task}})">
                                                    <i class="fas fa-check-circle"></i>
                                                    Checked?
                                                </button>
                                            @endif
                                            @if($showUndoChecked)
                                            <button class="btn btn-info btn-xs-block float-right" wire:key="item-undo-check-{{$item->id}}" wire:click="onUndoCheckedClicked({{$task}})">
                                                <i class="fas fa-undo-alt"></i>
                                                Un-Checked
                                            </button>
                                        @endif
                                        </span>
                                    </div>
                                    <div class="row">
                                        <span class="ml-auto" style="font-size: 12px;">
                                            @if($showDoneTimeDoneBy)
                                                By: <span class="font-weight-bold">{{$task->doneBy->name}}</span> <br>
                                                On: <span class="font-weight-bold">{{$task->doneTime}}</span> <br>
                                            @endif
                                            @if($showCheckedBy)
                                                Checked: <span class="font-weight-bold">{{$task->checkedBy->name}}</span>
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
{{--
                <x-modal id="edit-scope">
                    <x-slot name="title">
                        Edit Scope
                    </x-slot>
                    <x-slot name="content">
                        <x-input type="text" model="form.name">
                            Name
                        </x-input>
                        <div class="form-group">
                            <label for="remarks">
                                Remarks
                            </label>
                            <textarea name="remarks" wire:model="form.remarks" rows="5" class="form-control"></textarea>
                        </div>
                        <hr>
                        <div class="form-group">
                            <button wire:click="$toggle('showCreateTitleArea')" class="btn btn-outline-secondary btn-block">
                                Create Title
                                @if($showCreateTitleArea)
                                    <i class="fas fa-caret-right"></i>
                                @else
                                    <i class="fas fa-caret-down"></i>
                                @endif
                            </button>
                        </div>
                        <div>
                            @if($showCreateTitleArea)
                                <div class="bg-light">
                                    <div class="form-group">
                                        <x-input type="text" model="titleForm.sequence">
                                            Sequence (Number only)
                                        </x-input>
                                    </div>
                                    <div class="form-group">
                                        <x-input type="text" model="titleForm.name">
                                            Name
                                        </x-input>
                                    </div>
                                    <button type="submit" class="btn btn-success d-none d-sm-block" wire:click.prevent="generateTitle">
                                        Create Title
                                    </button>
                                    <button type="submit" class="btn btn-success btn-block d-block d-sm-none" wire:click.prevent="generateTitle">
                                        Create Title
                                    </button>
                                </div>
                            @endif
                        </div>
                        <hr>
                        @if($form)
                            <ul class="list-group">
                                @foreach($form->vmmfgTitles as $title)
                                <li class="list-group-item" style="background-color: #d3d3d3;">
                                    {{$title->sequence}}.  {{$title->name}}
                                </li>
                                    @foreach($title->vmmfgItems as $item)
                                    <li class="list-group-item ml-5" style="background-color: #ededed;">
                                        {{$item->sequence}}.  {{$item->name}}
                                    </li>
                                    @endforeach
                                @endforeach
                            </ul>
                        @endif
                    </x-slot>
                    <x-slot name="footer">
                        <button type="submit" class="btn btn-success d-none d-sm-block" wire:click.prevent="save">
                            Save
                        </button>
                        <button type="submit" class="btn btn-success btn-block d-block d-sm-none" wire:click.prevent="save">
                            Save
                        </button>
                    </x-slot>
                </x-modal> --}}
        </div>
    </div>

</div>
