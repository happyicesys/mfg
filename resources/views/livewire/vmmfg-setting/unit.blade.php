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
                                        Unit No
                                    </label>
                                    <input wire:model="filters.unit_no" type="text" class="form-control" placeholder="Unit No">
                                </div>
                                <div class="form-group col-md-4 col-xs-12">
                                    <label>
                                        {{$profile->profileSetting ? $profile->profileSetting->vmmfg_job_batch_no_title : 'Batch No'}}
                                    </label>
                                    <input wire:model="filters.batch_no" type="text" class="form-control" placeholder="Batch No">
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
                        <x-th-data model="batch_no" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            {{$profile->profileSetting ? $profile->profileSetting->vmmfg_job_batch_no_title : 'Batch No'}}
                        </x-th-data>
                        <x-th-data model="vend_id" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            {{$profile->profileSetting ? $profile->profileSetting->vmmfg_unit_vend_id_title : 'Vend ID'}}
                        </x-th-data>
                        <x-th-data model="unit_no" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Unit No
                        </x-th-data>
                        <x-th-data model="vmmfg_units.model" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Model
                        </x-th-data>
                        <x-th-data model="order_date" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Start Date
                        </x-th-data>
                        <x-th-data model="completion_date" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
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
                            {{ $unit->vmmfgJob->batch_no }}
                        </td>
                        <td class="text-center">
                            {{ $unit->vend_id }}
                        </td>
                        <td class="text-center">
                            {{ $unit->unit_no }}
                        </td>
                        <td class="text-center">
                            {{ $unit->model }}
                        </td>
                        <td class="text-center">
                            {{ $unit->vmmfgJob->order_date }}
                        </td>
                        <td class="text-center">
                            {{ $unit->completion_date }}
                        </td>
                        <td class="text-center">
                            <button type="button" wire:click="edit({{$unit->id}})" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#edit-unit">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
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
                        <x-input type="text" model="unitForm.vend_id">
                            {{$profile->profileSetting ? $profile->profileSetting->vmmfg_unit_vend_id_title : 'Vend ID'}}
                        </x-input>
                        <x-input type="text" model="unitForm.unit_no">
                            Unit No
                        </x-input>
                        <x-input type="text" model="unitForm.model">
                            Model
                        </x-input>
                        <div class="form-group">
                            <label for="completion_date">
                                Completion Date
                            </label>
                            <input type="date" class="form-control" wire:model.defer="unitForm.completion_date">
                        </div>
                    </x-slot>
                    <x-slot name="footer">
                        <button type="submit" class="btn btn-success d-none d-sm-block" wire:click.prevent="save">
                            Submit
                        </button>
                        <button type="submit" class="btn btn-success btn-block d-block d-sm-none" wire:click.prevent="save">
                            Submit
                        </button>
                    </x-slot>
                </x-modal>
            {{-- </form> --}}
        </div>
    </div>

</div>
