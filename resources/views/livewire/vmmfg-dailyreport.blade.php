<div>
    <div>
        <div>
            <x-flash></x-flash>
            <h2>Daily Report</h2>
            <hr>
            {{-- @dd($tasks->toArray()) --}}
            @php
                $tasksArr = $tasks->toArray();
                $from = isset($tasksArr['from']) ? $tasksArr['from'] : 0;
                $total = isset($tasksArr['total']) ? $tasksArr['total'] : count($tasks);
            @endphp

            <div class="bg-light pt-2 pb-2 pl-2 pr-2 mb-2">
                <div class="form-row">
                    <div class="form-group col-md-4 col-xs-12">
                        <label for="job_id">
                            Batch No
                        </label>
                        <select name="job_id" wire:model="filters.job_id" class="select form-control">
                            <option value="">All</option>
                            @foreach($jobs as $job)
                                <option value="{{$job->id}}">
                                    #{{$job->batch_no}} - {{$job->model}}
                                    @if($job->vmmfgUnits) ({{count($job->vmmfgUnits)}} units) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4 col-xs-12">
                        <label>
                            Unit No
                        </label>
                        <select name="unit_no" wire:model="filters.unit_id" class="select form-control">
                            <option value="">All</option>
                            @foreach($units as $unit)
                                <option value="{{$unit->id}}">
                                    #{{$unit->unit_no}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4 col-xs-12">
                        <label>
                            Is Done?
                        </label>
                        <select name="is_done" wire:model="filters.is_done" class="select form-control">
                            <option value="">All</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    @hasanyrole('superadmin|admin')
                    <div class="form-group col-md-4 col-xs-12">
                        <label>
                            Is Checked?
                        </label>
                        <select name="is_checked" wire:model="filters.is_checked" class="select form-control">
                            <option value="">All</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    @endhasanyrole
                    <div class="form-group col-md-4 col-xs-12">
                        <label>
                            User
                        </label>
                        <select name="user_id" wire:model="filters.user_id" class="select form-control">
                            <option value="">All</option>
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
                        <div class="input-group">
                            <input type="date" class="form-control" wire:model.defer="filters.date_from">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateClicked(-1, 'date_from')">
                                    <i class="fas fa-caret-left"></i>
                                </button>
                                <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateClicked(1, 'date_from')">
                                    <i class="fas fa-caret-right"></i>
                                </button>
                            </div>
                        </div>

                        {{-- <x-input-date model="filters.date_from" placeholder="Date From"></x-input-date> --}}
                        {{-- <input wire:model="filters.date_from" type="date" class="form-control" placeholder="Date From"> --}}
                    </div>
                    <div class="form-group col-md-4 col-xs-12">
                        <label>
                            Date To
                        </label>
                        <div class="input-group">
                            <input type="date" class="form-control" wire:model.defer="filters.date_to">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateClicked(-1, 'date_to')">
                                    <i class="fas fa-caret-left"></i>
                                </button>
                                <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateClicked(1, 'date_to')">
                                    <i class="fas fa-caret-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row d-flex btn-xs-block ml-auto">
                    <div class="btn-group">
                        <button class="btn btn-success" wire:click="exportPdf">
                            <i class="far fa-file-pdf"></i>
                            Export PDF
                        </button>
                        <button wire:click="resetFilters()" class="btn btn-outline-dark">Reset</button>
                    </div>
                </div>
            </div>
            <div class="d-none d-sm-block">
                <div class="form-row">
                    <div class="ml-auto">
                        <div class="form-inline">
                            <label for="display_num">Display </label>
                            <select wire:model="itemPerPage" class="form-control form-control-sm ml-1 mr-1" name="pageNum">
                                <option value="100">100</option>
                                <option value="200">200</option>
                                <option value="500">500</option>
                            </select>
                            <label for="display_num2" style="padding-right: 20px"> per Page</label>
                        </div>
                        <div>
                            <label style="padding-right:18px; font-weight: bold;">
                                Showing {{ count($tasks) }} of {{$total}}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-block d-sm-none">
                    <div class="form-group form-inline">
                        <label class="mt-1" for="display_num">Display </label>
                        <select wire:model="itemPerPage" class="ml-1 mr-1" name="pageNum">
                            <option value="100">100</option>
                            <option value="200">200</option>
                            <option value="500">500</option>
                        </select>
                        <label class="mt-1" for="display_num2" style="padding-right: 20px"> per Page</label>
                        <label class="ml-auto">
                            Showing {{ count($tasks) }} of {{$total}}
                        </label>
                    </div>
            </div>

            <div class="table-responsive pt-3" style="font-size: 14px;">
                <table class="table table-bordered table-hover">
                    <tr class="table-secondary">
                        {{-- <th class="text-center">
                            <input type="checkbox" name="" id="">
                        </th> --}}
                        <th class="text-center">
                            #
                        </th>
                        <x-th-data model="batch_no" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Batch No
                        </x-th-data>
                        <x-th-data model="unit_no" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Unit No
                        </x-th-data>
                        <x-th-data model="model" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Model
                        </x-th-data>
                        <x-th-data model="vmmfg_titles.sequence" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Title No
                        </x-th-data>
                        <x-th-data model="vmmfg_titles.name" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Title Name
                        </x-th-data>
                        <x-th-data model="vmmfg_items.sequence" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Task No
                        </x-th-data>
                        <x-th-data model="vmmfg_items.name" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Task Name
                        </x-th-data>
                        <x-th-data model="done_users.name" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Done By
                        </x-th-data>
                        <x-th-data model="done_time" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Done Time
                        </x-th-data>
                        <x-th-data model="checked_users.name" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Checked By
                        </x-th-data>
                        <x-th-data model="checked_time" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Checked Time
                        </x-th-data>
                        <x-th-data model="undo_done_users.name" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Undo By
                        </x-th-data>
                        <x-th-data model="undo_done_time" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Undo Time
                        </x-th-data>
                        <th class="text-center text-dark">
                            Remarks
                        </th>
                        {{-- <th></th> --}}
                    </tr>
                    @forelse($tasks as $index => $task)
                    <tr class="row_edit" wire:loading.class.delay="opacity-2" wire:key="row-{{$index}}">
                        {{-- <th class="text-center">
                            <input type="checkbox" wire:model="selected" value="{{$admin->id}}">
                        </th> --}}
                        {{-- @dd($tasks->toArray()) --}}
                        <td class="text-center">
                            {{ $index + $from}}
                        </td>
                        <td class="text-center">
                            {{ $task->vmmfgUnit->vmmfgJob->batch_no }}
                        </td>
                        <td class="text-center">
                            <a href="/vmmfg-ops?unit_id={{$task->vmmfgUnit->id}}&is_completed=''">
                            {{-- <a href="/vmmfg-ops?unit_id={{$task->vmmfgUnit->id}}&status=''"> --}}
                                {{ $task->vmmfgUnit->unit_no }}
                            </a>
                        </td>
                        <td class="text-center">
                            {{ $task->vmmfgUnit->model }}
                        </td>
                        <td class="text-left" style="min-width: 100px;">
                            {{ $task->vmmfgItem && $task->vmmfgItem->vmmfgTitle ? $task->vmmfgItem->vmmfgTitle->sequence : '' }}
                        </td>
                        <td class="text-left" style="min-width: 200px;">
                            {{ $task->vmmfgItem && $task->vmmfgItem->vmmfgTitle ? $task->vmmfgItem->vmmfgTitle->name : ''}}
                        </td>
                        <td class="text-left" style="min-width: 120px;">
                            {{ $task->vmmfgItem ? $task->vmmfgItem->sequence : '' }}
                        </td>
                        <td class="text-left" style="min-width: 200px;">
                            {{ $task->vmmfgItem ? $task->vmmfgItem->name : '' }}
                        </td>
                        <td class="text-center">
                            {{ $task->doneBy ? $task->doneBy->name : null }}
                            @if($task->undo_done_by)<span class="badge badge-pill badge-warning">Previous</span>@endif
                        </td>
                        <td class="text-center">
                            {{ $task->done_time ? \Carbon\Carbon::parse($task->done_time)->format('Y-m-d h:ia') : null }}
                            @if($task->undo_done_by)<span class="badge badge-pill badge-warning">Previous</span>@endif
                        </td>
                        <td class="text-center">
                            {{ $task->checkedBy ? $task->checkedBy->name : null }}
                        </td>
                        <td class="text-center">
                            {{  $task->checked_time ? \Carbon\Carbon::parse($task->checked_time)->format('Y-m-d h:ia') : null }}
                        </td>
                        <td class="text-center">
                            {{ $task->undoDoneBy ? $task->undoDoneBy->name : null }}
                        </td>
                        <td class="text-center">
                            {{  $task->undo_done_time ? \Carbon\Carbon::parse($task->undo_done_time)->format('Y-m-d h:ia') : null }}
                        </td>
                        <td class="text-left">
                            {{ $task->remarks }}
                        </td>
{{--
                        <td class="text-center">
                            <button type="button" wire:click="edit({{$unit->id}})" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#edit-unit">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td> --}}
                    </tr>
                    @empty
                    <tr>
                        <td colspan="18" class="text-center"> No Results Found </td>
                    </tr>
                    @endforelse
                </table>
            </div>
            <div>
                {{ isset($tasksArr['from']) ? $tasks->links() : '' }}
            </div>

            {{-- <form wire:submit.prevent="save"> --}}
                {{-- <x-modal id="edit-unit">
                    <x-slot name="title">
                        Edit Unit
                    </x-slot>
                    <x-slot name="content">
                        <x-input type="text" model="form.unit_no">
                            Unit No
                        </x-input>
                    </x-slot>
                    <x-slot name="footer">
                        <button type="submit" class="btn btn-success d-none d-sm-block" wire:click.prevent="save">
                            Submit
                        </button>
                        <button type="submit" class="btn btn-success btn-block d-block d-sm-none" wire:click.prevent="save">
                            Submit
                        </button>
                    </x-slot>
                </x-modal> --}}
            {{-- </form> --}}
        </div>
    </div>

</div>
