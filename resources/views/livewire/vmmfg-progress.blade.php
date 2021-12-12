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
            @endphp
            <div class="">
                <div>
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
                                    Batch No
                                </label>
                                <input wire:model="filters.batch_no" type="text" class="form-control" placeholder="Batch No">
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
                                    <option value="">Select...</option>
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
                            Batch No
                        </x-th-data>
                        <x-th-data model="unit_no" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Unit No
                        </x-th-data>
                        <x-th-data model="unit_no" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Model
                        </x-th-data>
                        <x-th-data model="order_date" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Start Date
                        </x-th-data>
                        <th class="text-center text-dark">
                            Progress
                        </th>
                        <th class="text-center text-dark">
                            Completion (%)
                        </th>
                    </tr>
                    @forelse($units as $index => $unit)
                        @php
                            $itemCount = 0;
                            $taskCount = 0;
                            $color = '';
                            foreach($unit->vmmfgScope->vmmfgTitles as $title) {
                                $itemCount += $title->vmmfg_items_count;
                            }

                            $taskCount = $unit->vmmfg_tasks_count;

                            $progressPercent = round($taskCount/$itemCount * 100);

                            if($progressPercent == 100) {
                                $color = 'bg-success';
                            }else if($progressPercent >= 80 and $progressPercent < 100) {
                                $color = 'bg-warning';
                            }
                        @endphp
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
                                {{ $unit->unit_no }}
                            </td>
                            <td class="text-center">
                                {{ $unit->vmmfgJob->model }}
                            </td>
                            <td class="text-center">
                                {{ $unit->vmmfgJob->order_date }}
                            </td>
                            {{-- @dd($unit->toArray()) --}}
                            <td class="text-center text-dark">
                                {{ $taskCount }} / {{ $itemCount }}
                            </td>
                            <td class="text-center text-dark {{$color}}">
                                {{ $progressPercent }}
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
