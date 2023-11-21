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
                        <th class="text-center" style="color: black;">
                            Status
                        </th>
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
                            {{-- <a href="/vmmfg-ops?unit_id={{$unit->id}}&is_completed=''"> --}}
                                {{ $unit->code }}
                            {{-- </a> --}}
                        </td>
                        <td class="text-center">
                            {{ $unit->vmmfgJob ? $unit->vmmfgJob->batch_no : (isset($unit->origin_vmmfg_job_json['batch_no']) ? $unit->origin_vmmfg_job_json['batch_no'] : null) }}
                        </td>
                        <td class="text-center">
                            @if($unit->vmmfgScope)
                            <a href="/vmmfg-ops?unit_id={{$unit->id}}&is_completed=''">
                                {{ $unit->unit_no }}
                            </a>
                            @else
                                {{ $unit->unit_no }}
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $unit->vend_id }}
                        </td>
                        <td class="text-center">
                            {{ $unit->model }}
                        </td>
                        <td class="text-center">
                            {{ $unit->vmmfgScope ? $unit->vmmfgScope->name : null }}
                        </td>
                        <td class="text-center">
                            {{ $unit->order_date }}
                        </td>
                        <td class="text-center">
                            @if($unit->referCompletionUnit)
                                <span class="badge badge-success">
                                    Refer: {{$unit->referCompletionUnit->vmmfgJob ? $unit->referCompletionUnit->vmmfgJob->batch_no : null}} ({{$unit->referCompletionUnit->unit_no}})
                                    <br> {{$unit->referCompletionUnit->vend_id}}
                                    <br> {{$unit->referCompletionUnit->model}}
                                </span>
                                <br>
                            @endif
                            {{ $unit->completion_date }}
                        </td>
                        <td class="text-center">
                            @if($unit->referCompletionUnit)
                                <a href="vmmfg-setting-unit?filters[code]={{$unit->referCompletionUnit->code}}">
                                    <span class="badge badge-success">
                                        Before of: {{$unit->referCompletionUnit->vmmfgJob ? $unit->referCompletionUnit->vmmfgJob->batch_no : null}} ({{$unit->referCompletionUnit->unit_no}})
                                    </span>
                                </a>
                            @endif
                            @if($unit->bindedCompletionUnit)
                                <a href="vmmfg-setting-unit?filters[code]={{$unit->bindedCompletionUnit->code}}">
                                    <span class="badge badge-success">
                                        After of: {{$unit->bindedCompletionUnit->vmmfgJob ? $unit->bindedCompletionUnit->vmmfgJob->batch_no : null}} ({{$unit->bindedCompletionUnit->unit_no}})
                                    </span>
                                </a>
                            @endif
                            @if($unit->origin)
                                <span class="badge badge-success">
                                    From: {{$unit->origin}}
                                </span>
                            @endif
                            @if($unit->destination)
                                <span class="badge badge-danger">
                                    To: {{$unit->destination}}
                                </span>
                            @endif
                            @if($unit->is_retired)
                                <span class="badge badge-danger">
                                    Retired
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button type="button" wire:click="edit({{$unit->id}})" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#edit-unit">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                        @if($unit->children)
                            @foreach($unit->children as $child)
                            <tr>
                                <td></td>
                                <td colspan="2"></td>
                                <td class="text-center">
                                    @if($child->vmmfgScope)
                                    <a href="/vmmfg-ops?unit_id={{$child->id}}&is_completed=''">
                                        {{ $child->unit_no }}
                                    </a>
                                    @else
                                        {{ $child->unit_no }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ $child->vend_id }}
                                </td>
                                <td class="text-center">
                                    {{ $child->model }}
                                </td>
                                <td class="text-center">
                                    {{ $child->vmmfgScope ? $child->vmmfgScope->name : null }}
                                </td>
                                <td class="text-center">
                                    {{ $child->order_date }}
                                </td>
                                <td class="text-center">
                                    {{ $child->completion_date }}
                                </td>
                                <td>
                                    @if($child->is_rework)
                                        <span class="badge badge-warning">
                                            Rework
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" wire:click="edit({{$child->id}})" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#edit-unit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        @endif
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
                        @if(isset($unitForm['origin']))
                            <x-input type="text" model="unitForm.code" disabled>
                                Serial No
                            </x-input>
                            <x-input type="text" model="unitForm.unit_no" disabled>
                                Unit No
                            </x-input>
                        @else
                            <x-input type="text" model="unitForm.code">
                                Serial No
                            </x-input>
                            <x-input type="text" model="unitForm.unit_no">
                                Unit No
                            </x-input>
                        @endif
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
                        <hr>
                        <div class="form-group">
                            <label for="completion_date">
                                Completion Date
                            </label>
                            <input type="date" class="form-control" wire:model.defer="unitForm.completion_date" {{isset($unitForm['refer_completion_unit_id']) ? 'disabled' : ''}}>
                        </div>
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
                                @foreach($editUnitSelections as $editUnitSelection)
                                    <option value="{{$editUnitSelection->id}}" @if($editUnitSelection->id == $unitForm->refer_completion_unit_id) 'selected' @endif>
                                        Unit No: {{$editUnitSelection->unit_no}} @if($editUnitSelection->vend_id)({{$profile->profileSetting ? $profile->profileSetting->vmmfg_unit_vend_id_title : 'Vend ID'}}: {{$editUnitSelection->vend_id}})@endif @if($editUnitSelection->model) Model: {{$editUnitSelection->model}}@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="destination">
                                Transfer Unit to
                            </label>
                            <select name="destination" wire:model.defer="unitForm.destination" class="select form-control">
                                <option value="">
                                    Select...
                                </option>
                                @foreach($unitTransferDestinationOptions as $unitTransferDestinationOptionIndex => $unitTransferDestinationOption)
                                    <option value="{{$unitTransferDestinationOptionIndex}}">
                                        {{$unitTransferDestinationOptionIndex . ' - ' . $unitTransferDestinationOption}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if(isset($unitForm['origin']) or isset($unitForm['destination']))
                            <h4>
                            @if(isset($unitForm['origin']))
                                <span class="badge badge-success ">
                                    From: {{$unitForm['origin']}}
                                </span>
                            @endif
                            @if(isset($unitForm['destination']))
                                <span class="badge badge-danger">
                                    To: {{$unitForm['destination']}}
                                </span>
                            @endif
                            </h4>
                            @if(isset($unitForm['origin']))
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="previous_scope">
                                            <u>
                                                Previous Scope
                                            </u>
                                        </label>
                                        <div>
                                            @if(isset($unitForm['origin_vmmfg_scope_json']))
                                                <a href="{{\App\Models\UnitTransferDestination::OPTIONS[$unitForm['origin']].'/api/vmmfg-ops-public?unit_id='.$unitForm['origin_ref_id']}}" target="_blank">
                                                    {{$unitForm['origin_vmmfg_scope_json']['name']}}
                                                </a>
                                                @if($unitForm['origin_vmmfg_scope_json']['remarks'])
                                                    <br>({{$unitForm['origin_vmmfg_scope_json']['remarks']}})
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="previous_scope">
                                            <u>
                                                Previous Job
                                            </u>
                                        </label>
                                        <div>
                                            @if(isset($unitForm['origin_vmmfg_job_json']))
                                                {{$unitForm['origin_vmmfg_job_json']['model']}}
                                                @if($unitForm['origin_vmmfg_job_json']['remarks'])
                                                    <br>({{$unitForm['origin_vmmfg_job_json']['remarks']}})
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endif

                    </x-slot>
                    <x-slot name="footer">
                        <div class="btn-group float-left">
                            @if(isset($unitForm['is_rework']) and !$unitForm['is_rework'])
                                <button type="submit" class="btn btn-warning " wire:click.prevent="rework">
                                    Rework
                                </button>
                            @endif

                            @if(isset($unitForm['is_retired']) and !$unitForm['is_retired'])
                                <button type="submit" class="btn btn-danger" wire:click.prevent="retire">
                                    Retire
                                </button>
                            @else
                                <button type="submit" class="btn btn-default" wire:click.prevent="undoRetire">
                                    Undo Retire
                                </button>
                            @endif
                        </div>
                        <div class="btn-group float-right">
                            <button type="submit" class="btn btn-danger d-none d-sm-block" onclick="return confirm('Are you sure you want to delete this unit?') || event.stopImmediatePropagation()" wire:click.prevent="delete">
                                Delete
                            </button>
                            <button type="submit" class="btn btn-danger btn-block d-block d-sm-none" onclick="return confirm('Are you sure you want to delete this unit?') || event.stopImmediatePropagation()" wire:click.prevent="delete">
                                Delete
                            </button>
                            <button type="submit" class="btn btn-success d-none d-sm-block" wire:click.prevent="save">
                                Save
                            </button>
                            <button type="submit" class="btn btn-success btn-block d-block d-sm-none" wire:click.prevent="save">
                                Save
                            </button>
                        </div>
                    </x-slot>
                </x-modal>
            {{-- </form> --}}
        </div>
    </div>

</div>
