<div>
    <div>
        <div>
            <x-flash></x-flash>
            <h2>VM MFG Units Settings</h2>
            <hr>
            @php
                $unitsArr = $units->toArray();
                $from = $unitsArr['from'];
                $total = $unitsArr['total'];
                $profile = \App\Models\Profile::where('is_primary', 1)->first();
            @endphp
            <div class="">
                <div>
                    {{-- @if($showFilters) --}}
                        <div class="bg-light pt-2 pb-2 pl-2 pr-2 mb-2">
                            <div class="form-row">
                                <div class="form-group col-md-4 col-xs-12">
                                    <label>
                                        Serial No
                                    </label>
                                    <input wire:model="filters.code" type="text" class="form-control" placeholder="Serial No">
                                </div>
                                <div class="form-group col-md-4 col-xs-12">
                                    <label>
                                        Unit No
                                    </label>
                                    <input wire:model="filters.unit_no" type="text" class="form-control" placeholder="Unit No">
                                </div>
                                <div class="form-group col-md-4 col-xs-12">
                                    <label>
                                        {{$profile->profileSetting ? $profile->profileSetting->vmmfg_job_batch_no_title : 'Batch No'}}
                                    </label>
                                    <input wire:model="filters.batch_no" type="text" class="form-control" placeholder="{{$profile->profileSetting ? $profile->profileSetting->vmmfg_job_batch_no_title : 'Batch No'}}">
                                </div>
                                <div class="form-group col-md-4 col-xs-12">
                                    <label>
                                        {{$profile->profileSetting ? $profile->profileSetting->vmmfg_unit_vend_id_title : 'Vend ID'}}
                                    </label>
                                    <input wire:model="filters.vend_id" type="text" class="form-control" placeholder="{{$profile->profileSetting ? $profile->profileSetting->vmmfg_unit_vend_id_title : 'Vend ID'}}">
                                </div>
                                <div class="form-group col-md-4 col-xs-12">
                                    <label>
                                        Date From
                                    </label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" wire:model="filters.date_from">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateClicked(-1, 'date_from')">
                                                <i class="fas fa-caret-left"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateClicked(1, 'date_from')">
                                                <i class="fas fa-caret-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-xs-12">
                                    <label>
                                        Date To
                                    </label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" wire:model="filters.date_to">
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
                                <div class="form-group col-md-4 col-xs-12">
                                    <label>
                                        Is Completed?
                                    </label>
                                    <select name="is_completed" wire:model="filters.is_completed" class="select form-control">
                                        <option value="">All</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row d-flex justify-content-end">
                                <div class="btn-group">
                                    <button wire:click="resetFilters()" class="btn btn-outline-dark">Reset</button>
                                </div>
                            </div>
                        </div>
                    {{-- @endif --}}
                </div>
                <div class="form-row">
                    {{-- <div class="mr-auto pl-1">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                            Search
                        </button>
                    </div> --}}

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
                                Showing {{ count($units) }} of {{$total}}
                            </label>
                        </div>
                    </div>
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
                        <x-th-data model="code" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Serial No
                        </x-th-data>
                        <x-th-data model="batch_no" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            {{$profile->profileSetting ? $profile->profileSetting->vmmfg_job_batch_no_title : 'Batch No'}}
                        </x-th-data>
                        <x-th-data model="unit_no" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Unit No
                        </x-th-data>
                        <x-th-data model="vend_id" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            {{$profile->profileSetting ? $profile->profileSetting->vmmfg_unit_vend_id_title : 'Vend ID'}}
                        </x-th-data>
                        <x-th-data model="vmmfg_units.model" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Model
                        </x-th-data>
                        <x-th-data model="vmmfg_units.vmmfg_scope_id" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Scope
                        </x-th-data>
                        <x-th-data model="vmmfg_units.order_date" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Start Date
                        </x-th-data>
                        <x-th-data model="vmmfg_units.completion_date" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Completion Date
                        </x-th-data>
                        <th></th>
                    </tr>
                    @forelse($units as $index => $unit)
                    <tr class="row_edit" wire:loading.class.delay="opacity-2" wire:key="row-{{$unit->id}}">
                        {{-- <th class="text-center">
                            <input type="checkbox" wire:model="selected" value="{{$admin->id}}">
                        </th> --}}

                        <td class="text-center">
                            {{ $index + $from}}
                        </td>
                        <td class="text-center">
                            <a href="/vmmfg-ops?unit_id={{$unit->id}}&is_completed=''">
                                {{ $unit->code }}
                            </a>
                        </td>
                        <td class="text-center">
                            {{ $unit->vmmfgJob->batch_no }}
                        </td>
                        <td class="text-center">
                            <a href="/vmmfg-ops?unit_id={{$unit->id}}&is_completed=''">
                                {{ $unit->unit_no }}
                            </a>
                        </td>
                        <td class="text-center">
                            {{ $unit->vend_id }}
                        </td>
                        <td class="text-center">
                            {{ $unit->model }}
                        </td>
                        <td class="text-center">
                            {{ $unit->vmmfgScope->name }}
                        </td>
                        <td class="text-center">
                            {{ $unit->order_date }}
                        </td>
                        <td class="text-center">
                            @if($unit->referCompletionUnit)
                                <span class="badge badge-success">
                                    Refer: {{$unit->referCompletionUnit->vmmfgJob->batch_no}} ({{$unit->referCompletionUnit->unit_no}})
                                    <br> {{$unit->referCompletionUnit->vend_id}}
                                    <br> {{$unit->referCompletionUnit->model}}
                                </span>
                                <br>
                            @endif
                            {{ $unit->completion_date }}
                        </td>
                        <td class="text-center">
                            <button type="button" wire:click="edit({{$unit->id}})" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#edit-unit">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    {{-- <tr class="row_edit" wire:loading.class.delay="opacity-2" wire:key="row-{{$unit->id}}">
                        <th class="text-center">
                            <input type="checkbox" wire:model="selected" value="{{$admin->id}}">
                        </th>
                        <td class="text-center">
                            {{ $index + $from}}
                        </td>
                        <td class="text-center">
                            {{ $unit->batch_no }}
                        </td>
                        <td class="text-center">
                            <a href="/vmmfg-ops?unit_id={{$unit->id}}">
                                {{ $unit->unit_no }}
                            </a>
                        </td>
                        <td class="text-center">
                            {{ $unit->vend_id }}
                        </td>
                        <td class="text-center">
                            {{ $unit->model }}
                        </td>
                        <td class="text-center">
                            {{ $unit->scope_name }}
                        </td>
                        <td class="text-center">
                            {{ $unit->order_date }}
                        </td>
                        <td class="text-center">
                            {{$unit}}
                            @if($unit->referCompletionUnit)
                                <span class="badge badge-success">
                                    Refer to {{$unit->referCompletionUnit->unit_no}} @if($unit->referCompletionUnit->vend_id) - {{$unit->referCompletionUnit->vend_id}} @endif
                                    @if($unit->referCompletionUnit->vmmfgJob)
                                        <br>{{$unit->referCompletionUnit->vmmfgJob->model}}
                                    @endif

                                </span>
                            @endif
                            {{ $unit->completion_date }}
                        </td>
                        <td class="text-center">
                            <button type="button" wire:click="edit({{$unit->id}})" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#edit-unit">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr> --}}
                    @empty
                    <tr>
                        <td colspan="18" class="text-center"> No Results Found </td>
                    </tr>
                    @endforelse
                </table>
            </div>
            <div>
                {{ $units->links() }}
            </div>

            {{-- <form wire:submit.prevent="save"> --}}
                <x-modal id="edit-unit">
                    <x-slot name="title">
                        Edit Unit
                    </x-slot>
                    <x-slot name="content">
                        <x-input type="text" model="unitForm.code">
                            Serial No
                        </x-input>
                        <x-input type="text" model="unitForm.unit_no">
                            Unit No
                        </x-input>
                        <x-input type="text" model="unitForm.vend_id">
                            {{$profile->profileSetting ? $profile->profileSetting->vmmfg_unit_vend_id_title : 'Vend ID'}}
                        </x-input>
                        <x-input type="text" model="unitForm.model">
                            Model
                        </x-input>
                        <div class="form-group">
                            <label for="vmmfg_scope_id">
                                Scope
                            </label>
                            <select name="vmmfg_scope_id" wire:model="unitForm.vmmfg_scope_id" class="select form-control">
                                <option value="">Select..</option>
                                @foreach($this->scopes as $scope)
                                    <option value="{{$scope->id}}">
                                        {{$scope->name}} @if($scope->remarks)({{$scope->remarks}})@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="start_date">
                                Start Date
                            </label>
                            <input type="date" class="form-control" wire:model.defer="unitForm.order_date">
                        </div>
                        <div class="form-group">
                            <label for="completion_date">
                                Completion Date
                            </label>
                            <input type="date" class="form-control" wire:model.defer="unitForm.completion_date" {{isset($unitForm['refer_completion_unit_id']) ? 'disabled' : ''}}>
                        </div>

                        <hr>
                        <div class="form-group">
                            <label for="refer_completion_unit_id">
                                Refer to Completion Unit
                            </label>
                            <select name="refer_completion_unit_id" wire:model.defer="unitForm.refer_completion_unit_id" class="select form-control">
                                <option value="">
                                    @if(isset($unitForm['refer_completion_unit_id']))
                                    ----- Clear -----
                                    @else
                                        Select..
                                    @endif
                                </option>
                                @foreach($unitSelections as $unitSelection)
                                    <option value="{{$unitSelection->id}}">
                                        Unit No: {{$unitSelection->unit_no}} @if($unitSelection->vend_id)({{$profile->profileSetting ? $profile->profileSetting->vmmfg_unit_vend_id_title : 'Vend ID'}}: {{$unitSelection->vend_id}})@endif @if($unitSelection->model) Model: {{$unitSelection->model}}@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </x-slot>
                    <x-slot name="footer">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-danger d-none d-sm-block" onclick="return confirm('Are you sure you want to delete this unit?') || event.stopImmediatePropagation()" wire:click.prevent="delete">
                                Delete
                            </button>
                            <button type="submit" class="btn btn-danger btn-block d-block d-sm-none" onclick="return confirm('Are you sure you want to delete this unit?') || event.stopImmediatePropagation()" wire:click.prevent="delete">
                                Delete
                            </button>
                            <button type="submit" class="btn btn-success d-none d-sm-block" wire:click.prevent="save">
                                Submit
                            </button>
                            <button type="submit" class="btn btn-success btn-block d-block d-sm-none" wire:click.prevent="save">
                                Submit
                            </button>
                        </div>
                    </x-slot>
                </x-modal>
            {{-- </form> --}}
        </div>
    </div>

</div>
