@inject('vmmfgTask', 'App\Models\VmmfgTask')
@extends('pdf.base')

@section('content')
    <table width="100%">
        <tr>
            <td style="padding-bottom: 0.5em; text-align: right">
                <span style="font-size: 20px">
                    <strong>QA/QC Report</strong>
                </span>
            </td>
        </tr>
        <tr>
            <td class="text" valign="top" style="text-align: right">
                <table align="right">
                    <tr>
                        <td>Batch No</td>
                        <td>:</td>
                        <td style="text-align: right">{{ $filtersData['jobBatchNo'] }}</td>
                    </tr>
                    <tr>
                        <td>Unit No</td>
                        <td>:</td>
                        <td style="text-align: right">{{ $filtersData['unitNo'] }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="items">
        @foreach($vmmfgUnit->first()->vmmfgScope->vmmfgTitles as $index => $title)
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
            <table style="width:100%; padding-top: 15px;">
                <tr>
                    <th style="font-family: 'founder-type'; font-weight: bold; word-wrap: break-word;">
                        <span class="mr-auto" style="font-size: 13px;">
                            {{$title->sequence}}.  {{$title->name}}
                            @if($title->vmmfgTitleCategory)
                                <span class="badge badge-warning">
                                    {{$title->vmmfgTitleCategory->name}}
                                </span>
                            @endif
                        </span>
                        <span class="ml-auto" style="font-size: 15px;">
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
                    </th>
                </tr>
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

                <tr>
                    <td style="font-family: 'founder-type'; font-weight: bold; word-wrap: break-word;">
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
                                    <span class="" style="font-size: 12px;">
                                        @if($showDoneTimeDoneBy)
                                            @if(!$showUndoDoneBy)
                                                @if($adminClickable)
                                                    <a href="" class="badge badge-success" style="font-size: 12px;" onclick="return confirm('Are you sure you want to Undo the Task?') || event.stopImmediatePropagation()" wire:click.prevent="onUndoClicked({{$task}})">
                                                        <i class="fas fa-check-circle"></i>
                                                        Done
                                                    </a>
                                                @else
                                                    <span class="badge badge-success" style="font-size: 12px;">
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
                                                <a href="" class="badge badge-primary" style="font-size: 12px;" wire:click.prevent="onUndoCheckedClicked({{$task}})">
                                                    <i class="fas fa-check-circle"></i>
                                                    Checked
                                                </a>
                                            @else
                                                <span class="badge badge-primary" style="font-size: 12px;">
                                                    <i class="fas fa-check-circle"></i>
                                                    Checked
                                                </span>
                                            @endif
                                            By: <span class="font-weight-bold">{{$checkedBy}}</span> <br>
                                            On: <span class="font-weight-bold">{{$checkedTime}}</span> <br>
                                        @endif
                                        @if($showUndoDoneBy)
                                            <span class="badge badge-warning" style="font-size: 12px;">
                                                Undo
                                            </span>
                                            By: <span class="font-weight-bold">{{$undoDoneBy}}</span> <br>
                                            On: <span class="font-weight-bold">{{$undoDoneTime}}</span> <br>
                                        @endif
                                        @if($showCancelledBy)
                                            @if($adminClickable)
                                                <a href="" class="badge badge-danger" style="font-size: 12px;" wire:click.prevent="onUndoCancelledClicked({{$task}})">
                                                    <i class="far fa-times-circle"></i>
                                                    Cancelled
                                                </a>
                                            @else
                                                <span class="badge badge-danger" style="font-size: 12px;">
                                                    <i class="far fa-times-circle"></i>
                                                    Cancelled
                                                </span>
                                            @endif
                                            By: <span class="font-weight-bold">{{$cancelledBy}}</span> <br>
                                            On: <span class="font-weight-bold">{{$cancelledTime}}</span> <br>
                                        @endif
                                    </span>

                            @endif
                        </span>
                    </div>
                    @if(!$item->attachments()->exists())
                    <div class="row pt-1" >
                        <span class="ml-auto" style="font-size: 12px;">
                            @if($showDoneTimeDoneBy)
                                @if(!$showUndoDoneBy)
                                    @if($adminClickable)
                                        <a href="" class="badge badge-success" style="font-size: 12px;" onclick="return confirm('Are you sure you want to Undo the Task?') || event.stopImmediatePropagation()" wire:click.prevent="onUndoClicked({{$task}})">
                                            <i class="fas fa-check-circle"></i>
                                            Done
                                        </a>
                                    @else
                                        <span class="badge badge-success" style="font-size: 12px;">
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
                                    <a href="" class="badge badge-primary" style="font-size: 12px;" wire:click.prevent="onUndoCheckedClicked({{$task}})">
                                        <i class="fas fa-check-circle"></i>
                                        Checked
                                    </a>
                                @else
                                    <span class="badge badge-primary" style="font-size: 12px;">
                                        <i class="fas fa-check-circle"></i>
                                        Checked
                                    </span>
                                @endif
                                By: <span class="font-weight-bold">{{$checkedBy}}</span> <br>
                                On: <span class="font-weight-bold">{{$checkedTime}}</span> <br>
                            @endif
                            @if($showUndoDoneBy)
                                <span class="badge badge-warning" style="font-size: 12px;">
                                    Undo
                                </span>
                                By: <span class="font-weight-bold">{{$undoDoneBy}}</span> <br>
                                On: <span class="font-weight-bold">{{$undoDoneTime}}</span> <br>
                            @endif
                            @if($showCancelledBy)
                                @if($adminClickable)
                                    <a href="" class="badge badge-danger" style="font-size: 12px;" wire:click.prevent="onUndoCancelledClicked({{$task}})">
                                        <i class="far fa-times-circle"></i>
                                        Cancelled
                                    </a>
                                @else
                                    <span class="badge badge-danger" style="font-size: 12px;">
                                        <i class="far fa-times-circle"></i>
                                        Cancelled
                                    </span>
                                @endif
                                By: <span class="font-weight-bold">{{$cancelledBy}}</span> <br>
                                On: <span class="font-weight-bold">{{$cancelledTime}}</span> <br>
                            @endif
                        </span>
                    </div>

                    @endif

                {{-- </div> --}}
                    <div class="form-group" id="item-dropdown-{{$item->id}}">
                        @if($item->attachments)
                            <div class="form-group">
                                @if($task and $task->attachments)
                                    @foreach($task->attachments as $attachment)
                                        <div class="row">
                                            <div class="card" style="max-width:600px;width:100%;" wire:key="attachment-{{$attachment->id}}">
                                                @php
                                                    $ext = pathinfo($attachment->full_url, PATHINFO_EXTENSION);
                                                @endphp
                                                {{-- @if($ext === 'pdf')
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
                                                @endif --}}

                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endif

                    </div>
                    </td>
                </tr>
            {{-- @endif --}}
            @endforeach
            </table>
        @endforeach
    </div>
@endsection



