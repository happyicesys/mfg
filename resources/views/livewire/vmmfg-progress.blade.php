<div>
    <div>
        <div>
            <x-flash></x-flash>
            <h2>Progress</h2>
            <hr>
            @php
                $unitsArr = $units->toArray();
                $from = $unitsArr['from'];
                $total = $unitsArr['total'];

                $profile = \App\Models\Profile::with('profileSetting')->where('is_primary', 1)->first();
            @endphp
            <div class="">
                <div>
                    <div class="bg-light pt-2 pb-2 pl-2 pr-2 mb-2">
                        <div class="form-row">
{{--
                            <div class="form-group col-md-4 col-xs-12">
                                <label>
                                    Unit No
                                </label>
                                <input wire:model="filters.unit_no" type="text" class="form-control" placeholder="Unit No">
                            </div>
                            <div class="form-group col-md-4 col-xs-12">
                                <label>
                                    Batch No
                                </label>
                                <input wire:model="filters.batch_no" type="text" class="form-control" placeholder="Batch No">
                            </div>
                            <div class="form-group col-md-4 col-xs-12">
                                <label>
                                    Model
                                </label>
                                <input wire:model="filters.model" type="text" class="form-control" placeholder="Model">
                            </div> --}}
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
                                    Model
                                </label>
                                <input wire:model="filters.model" type="text" class="form-control" placeholder="Model">
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
                </div>
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
                        <th class="text-center text-dark">
                            #
                        </th>
                        <x-th-data model="batch_no" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            {{$profile->profileSetting ? $profile->profileSetting->vmmfg_job_batch_no_title : 'Batch No'}}
                        </x-th-data>
                        <x-th-data model="code" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Serial No
                        </x-th-data>
                        <x-th-data model="vend_id" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            {{$profile->profileSetting ? $profile->profileSetting->vmmfg_unit_vend_id_title : 'Vend ID'}}
                        </x-th-data>
                        <x-th-data model="unit_no" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Unit No
                        </x-th-data>
                        <x-th-data model="model" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Model
                        </x-th-data>
                        <x-th-data model="vmmfg_scopes.name" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Scope
                        </x-th-data>
                        <x-th-data model="order_date" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Start Date
                        </x-th-data>
                        <x-th-data model="completion_date" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Completion Date
                        </x-th-data>
                        @foreach($vmmfgTitleCategories as $vmmfgTitleCategory)
                            <th class="text-center text-dark">
                                {{$vmmfgTitleCategory->name}}
                            </th>
                        @endforeach
                        <th class="text-center text-dark">
                            Completion (%)
                        </th>
                        <th class="text-center text-dark">
                            Checked
                        </th>
                    </tr>
                    @forelse($units as $index => $unit)
                        @php
                            $totalItemCount = 0;
                            $totalTaskCount = 0;
                            $totalCheckedTaskCount = 0;
                            $color = '';
                        @endphp
                        <tr class="row_edit" wire:loading.class.delay="opacity-2" wire:key="row-{{$unit->id}}">
                            {{-- <th class="text-center">
                                <input type="checkbox" wire:model="selected" value="{{$admin->id}}">
                            </th> --}}
                            <td class="text-center">
                                {{ $index + $from}}
                            </td>
                            <td class="text-center">
                                {{ $unit->vmmfgJob ? $unit->vmmfgJob->batch_no : null }}
                            </td>
                            <td class="text-center">
                                {{ $unit->code }}
                            </td>
                            <td class="text-center">
                                {{ $unit->vend_id }}
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
                                {{ $unit->model }}
                            </td>
                            <td class="text-center">
                                {{ $unit->vmmfgScope ? $unit->vmmfgScope->name : null }}
                            </td>
                            <td class="text-center">
                                {{ $unit->order_date }}
                            </td>
                            <td class="text-center">
                                {{ $unit->completion_date }}
                            </td>
                            @foreach($vmmfgTitleCategories as $vmmfgTitleCategory)
                            @php
                                $createDiv = true;
                            @endphp
                                @if($unit->progress_json)
                                    @foreach($unit->progress_json['data'] as $progress)
                                        @if($progress['vmmfg_title_category_id'] == $vmmfgTitleCategory->id)
                                            @php
                                                $createDiv = false;
                                            @endphp
                                            <td class="text-center text-dark {{$progress['done']/ $progress['total'] * 100 == 100 ? 'bg-success' : (($progress['done']/ $progress['total'] * 100) >= 80 && ($progress['done']/ $progress['total'] * 100) < 100 ? 'bg-warning' : '')}}">
                                                {{ $progress['done'] }} /
                                                {{ $progress['total'] }}
                                                <br>
                                                @if($progress['is_stocked'])
                                                    <i class="fas fa-check-circle" style="color: green;"></i>
                                                @endif
                                            </td>
                                        @endif
                                    @endforeach
                                @endif

                                @if($createDiv)
                                    <td class="text-center text-dark">
                                        0 / 0
                                    </td>
                                @endif

                            @endforeach

                            <td class="text-center text-dark {{$unit->progress_json['done']/ ($unit->progress_json['total'] ? $unit->progress_json['total'] : 1) * 100 == 100 ? 'bg-success' : ($unit->progress_json['done']/ ($unit->progress_json['total'] ? $unit->progress_json['total'] : 1) * 100 >= 80 && $unit->progress_json['done']/ ($unit->progress_json['total'] ? $unit->progress_json['total'] : 1) * 100 < 100 ? 'bg-warning' : '')}}">
                                {{ round($unit->progress_json['done']/ ($unit->progress_json['total'] ? $unit->progress_json['total'] : 1) * 100) }}
                            </td>
                            <td class="text-center text-dark {{$unit->progress_json['checked']/ ($unit->progress_json['total'] ? $unit->progress_json['total'] : 1) * 100 == 100 ? 'bg-success' : ($unit->progress_json['checked']/ ($unit->progress_json['total'] ? $unit->progress_json['total'] : 1) * 100 >= 80 && $unit->progress_json['done']/ ($unit->progress_json['total'] ? $unit->progress_json['total'] : 1) * 100 < 100 ? 'bg-warning' : '')}}">
                                {{ $unit->progress_json['checked'] }} /
                                {{ ($unit->progress_json['total'] ? $unit->progress_json['total'] : 0) }}
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
                </x-modal>
            {{-- </form> --}}
        </div>
    </div>
</div>
