<div>
    <div>
        <div>
            <x-flash></x-flash>
            <h2>Master Unit</h2>
            <hr>
            @php
                $masterUnitsArr = $masterUnits->toArray();
                $from = $masterUnitsArr['from'];
                $total = $masterUnitsArr['total'];
                $profile = \App\Models\Profile::where('is_primary', 1)->first();
            @endphp
            <div class="">
                <div>
                    <div class="bg-light pt-2 pb-2 pl-2 pr-2 mb-2">
                        <div class="form-row">
                            <div class="form-group col-md-4 col-xs-12">
                                <label>
                                    Container
                                </label>
                                <input wire:model="filters.container" type="text" class="form-control" placeholder="Container">
                            </div>
                            <div class="form-group col-md-4 col-xs-12">
                                <label>
                                    Unit Code
                                </label>
                                <input wire:model="filters.code" type="text" class="form-control" placeholder="Unit Code">
                            </div>
                            <div class="form-group col-md-4 col-xs-12">
                                <label>
                                    Batch Number
                                </label>
                                <input wire:model="filters.batch" type="text" class="form-control" placeholder="Batch Number">
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
                                Showing {{ count($masterUnits) }} of {{$total}}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive pt-3" style="font-size: 14px;">
                <table class="table table-bordered table-hover">
                    <tr class="table-secondary">
                        <th class="text-center">
                            #
                        </th>
                        <x-th-data model="container" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Container
                        </x-th-data>
                        <x-th-data model="code" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Unit Code
                        </x-th-data>
                        <x-th-data model="batch" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Batch
                        </x-th-data>
                        <x-th-data model="vmmfg_units.order_date" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Created Date
                        </x-th-data>
                        <th class="text-center" style="color: black;">
                            Status
                        </th>
                        <th></th>
                    </tr>
                    @forelse($masterUnits as $index => $masterUnit)
                    <tr class="row_edit" wire:loading.class.delay="opacity-2" wire:key="row-{{$masterUnit->id}}">
                        <td class="text-center">
                            {{ $index + $from}}
                        </td>
                        <td class="text-center">
                            {{ $masterUnit->container }}
                        </td>
                        <td class="text-center">
                            {{ $masterUnit->code }}
                        </td>
                        <td class="text-center">
                            {{ $masterUnit->batch }}
                        </td>
                        <td class="text-center">
                            {{ $masterUnit->created_at->format('ymd') }}
                        </td>
                        <td class="text-center">
                            @if($masterUnit->is_retired)
                                <span class="badge badge-danger">
                                    Retired
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button type="button" wire:click="edit({{$masterUnit->id}})" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#edit-master-unit">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                        {{-- @if($masterUnit->vmmfgUnits)
                            @foreach($masterUnit->vmmfgUnits as $vmmfgUnit)
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
                        @endif --}}
                    @empty
                    <tr>
                        <td colspan="18" class="text-center"> No Results Found </td>
                    </tr>
                    @endforelse
                </table>
            </div>
            <div>
                {{ $masterUnits->links() }}
            </div>

            {{-- <form wire:submit.prevent="save"> --}}
                <x-modal id="edit-master-unit">
                    <x-slot name="title">
                        Edit Master Unit
                    </x-slot>
                    <x-slot name="content">
                        <x-input type="text" model="masterUnitForm.container">
                            Container
                        </x-input>
                        <x-input type="text" model="masterUnitForm.code">
                            Unit Code
                        </x-input>
                        <x-input type="text" model="masterUnitForm.batch">
                            Batch
                        </x-input>
                    </x-slot>
                    <x-slot name="footer">
                        <div class="btn-group float-left">
                            @if(isset($masterUnitForm['is_retired']) and !$masterUnitForm['is_retired'])
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
