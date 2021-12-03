<div>
    <div>
        <div>
            <x-flash></x-flash>
            <h2>VM MFG Jobs Settings</h2>
            <hr>
            @php
                $jobsArr = $jobs->toArray();
                $from = $jobsArr['from'];
                $total = $jobsArr['total'];
            @endphp
            <div class="d-none d-sm-block">
                <div class="form-group form-inline">
                    <label for="name">
                        Quick Search
                    </label>
                    <input type="text" wire:model="filters.search" class="form-control mx-2" placeholder="Quick Search" @if($showFilters) disabled @endif>
                    <button wire:click="$toggle('showFilters')" class="btn btn-outline-secondary">
                        Advance Search
                        @if($showFilters)
                            <i class="fas fa-caret-right"></i>
                        @else
                            <i class="fas fa-caret-down"></i>
                        @endif
                    </button>
        {{--
                    <div class="dropdown show">
                        <button class="btn btn-outline-info dropdown-toggle" type="button" id="bulkActionsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            Bulk Actions
                        </button>
                        <div class="dropdown-menu" aria-labelledby="bulkActionsDropdown">
                            <a class="dropdown-item" href="#">
                                <i class="far fa-file-excel"></i>
                                Export
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="far fa-trash-alt"></i>
                                Delete
                            </a>
                        </div>
                    </div> --}}

                </div>
                <div>
                    @if($showFilters)
                        <div class="bg-light pt-2 pb-2 pl-2 pr-2 mb-2">
                            <div class="form-row">
                                <div class="form-group col-4">
                                    <label>
                                        Batch No
                                    </label>
                                    <input wire:model="filters.batch_no" type="text" class="form-control" placeholder="Batch No">
                                </div>
                                <div class="form-group col-4">
                                    <label>
                                        Model
                                    </label>
                                    <input wire:model="filters.model" type="text" class="form-control" placeholder="Model">
                                </div>
                                <div class="form-group col-4">
                                    <label>
                                        Order Date From
                                    </label>

                                    <input wire:model="filters.order_date_from" type="date" class="form-control" placeholder="Order Date From">
                                </div>
                                <div class="form-group col-4">
                                    <label>
                                        Order Date To
                                    </label>
                                    <input wire:model="filters.order_date_to" type="date" class="form-control" placeholder="Order Date To">
                                </div>
                            </div>
                            <div class="form-row d-flex justify-content-end">
                                <div class="btn-group">
                                    <button wire:click="resetFilters()" class="btn btn-outline-dark">Reset</button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="form-row">
                    {{-- <div class="mr-auto pl-1">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                            Search
                        </button>
                    </div> --}}
                    <div class="mr-auto pl-1">
                        <button class="btn btn-success" wire:click="create()" data-toggle="modal" data-target="#create-modal">
                            <i class="fas fa-plus-circle"></i>
                            Create
                        </button>
                    </div>

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
                                Showing {{ count($jobs) }} of {{$total}}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-block d-sm-none">
                <div class="form-group">
                    <label for="name">
                        Quick Search
                    </label>
                    <input type="text" wire:model="filters.search" class="form-control" placeholder="Quick Search" @if($showFilters) disabled @endif>
        {{--
                    <div class="dropdown show">
                        <button class="btn btn-outline-info dropdown-toggle" type="button" id="bulkActionsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            Bulk Actions
                        </button>
                        <div class="dropdown-menu" aria-labelledby="bulkActionsDropdown">
                            <a class="dropdown-item" href="#">
                                <i class="far fa-file-excel"></i>
                                Export
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="far fa-trash-alt"></i>
                                Delete
                            </a>
                        </div>
                    </div> --}}
                </div>
                <div class="form-group">
                    <button wire:click="$toggle('showFilters')" class="btn btn-outline-secondary btn-block">
                        Advance Search
                        @if($showFilters)
                            <i class="fas fa-caret-right"></i>
                        @else
                            <i class="fas fa-caret-down"></i>
                        @endif
                    </button>
                </div>
                <div>
                    @if($showFilters)
                        <div class="bg-light">
                            {{-- <div class="form-row"> --}}
                                <div class="form-group">
                                    <label>
                                        Batch No
                                    </label>
                                    <input wire:model="filters.batch_no" type="text" class="form-control" placeholder="Batch No">
                                </div>
                                <div class="form-group">
                                    <label>
                                        Model
                                    </label>
                                    <input wire:model="filters.model" type="text" class="form-control" placeholder="Model">
                                </div>
                                <div class="form-group">
                                    <label>
                                        Order Date From
                                    </label>
                                    <input wire:model="filters.order_date_from" type="text" class="form-control" placeholder="Order Date From">
                                </div>
                                <div class="form-group">
                                    <label>
                                        Order Date To
                                    </label>
                                    <input wire:model="filters.order_date_to" type="text" class="form-control" placeholder="Order Date To">
                                </div>
                            {{-- </div> --}}
                            <div class="form-group">
                                {{-- <div class="btn-group"> --}}
                                    <button wire:click="resetFilters()" class="btn btn-outline-dark btn-block">Reset</button>
                                {{-- </div> --}}
                            </div>
                        </div>
                    @endif
                </div>
                    <div class="form-group">
                        <button class="btn btn-success btn-block" wire:click="create()" data-toggle="modal" data-target="#create-modal">
                            <i class="fas fa-plus-circle"></i>
                            Create
                        </button>
                    </div>
                    <div class="form-group form-inline">
                        <label class="mt-1" for="display_num">Display </label>
                        <select wire:model="itemPerPage" class="ml-1 mr-1" name="pageNum">
                            <option value="100">100</option>
                            <option value="200">200</option>
                            <option value="500">500</option>
                        </select>
                        <label class="mt-1" for="display_num2" style="padding-right: 20px"> per Page</label>
                        <label class="ml-auto">
                            Showing {{ count($jobs) }} of {{$total}}
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
                            Batch
                        </x-th-data>
                        <x-th-data model="model" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Model
                        </x-th-data>
                        <x-th-data model="order_date" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Order Date
                        </x-th-data>
                        <th class="text-center text-dark">
                            Total Units
                        </th>
                        <th></th>
                    </tr>
                    @forelse($jobs as $index => $job)
                    <tr class="row_edit" wire:loading.class.delay="opacity-2" wire:key="row-{{$job->id}}">
                        {{-- <th class="text-center">
                            <input type="checkbox" wire:model="selected" value="{{$admin->id}}">
                        </th> --}}
                        <td class="text-center">
                            {{ $index + $from}}
                        </td>
                        <td class="text-center">
                            {{ $job->batch_no }}
                        </td>
                        <td class="text-center">
                            {{ $job->model }}
                        </td>
                        <td class="text-center">
                            {{ $job->order_date }}
                        </td>
                        <td class="text-center">
                            {{ $job->vmmfgUnits->count() }}
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button type="button" wire:click="edit({{$job->id}})" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#edit-job">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" onclick="confirm('Are you sure you want to remove this entry?') || event.stopImmediatePropagation()" wire:click="delete({{$job->id}})" class="btn btn-danger btn-sm" >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
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
                {{ $jobs->links() }}
            </div>

            {{-- <form wire:submit.prevent="save"> --}}
                <x-modal id="edit-job">
                    <x-slot name="title">
                        Edit Job
                    </x-slot>
                    <x-slot name="content">
                        <x-input type="text" model="form.batch_no">
                            Batch No
                        </x-input>
                        <x-input type="text" model="form.model">
                            Model
                        </x-input>
                        <x-input type="date" model="form.order_date">
                            Order Date
                        </x-input>
                        {{-- <x-input type="text" model="form.id">
                            ID
                        </x-input> --}}
                        <hr>
                            @if($units)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <tr class="table-primary">
                                            <th class="text-center text-dark">
                                                #
                                            </th>
                                            <th class="text-center text-dark">
                                                Unit No
                                            </th>
                                            <th class="text-center text-dark">
                                                Scope
                                            </th>
                                        </tr>
                                        @forelse($units as $index => $unit)
                                        <tr>
                                            <td class="text-center">
                                                {{ $index + $from}}
                                            </td>
                                            <td class="text-center">
                                                {{ $unit->unit_no }}
                                            </td>
                                            <td class="text-left">
                                                @php
                                                    // dd($unit->toArray());
                                                @endphp
                                                {{ $unit->vmmfgScope ? $unit->vmmfgScope->name : '' }}
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="18" class="text-center"> No Results Found </td>
                                        </tr>
                                        @endforelse
                                    </table>
                                </div>
                            @endif
                        <hr>
                        <div class="form-group">
                            <button wire:click="$toggle('showBatchGenerateUnits')" class="btn btn-outline-secondary btn-block">
                                Batch Generate VM Units
                                @if($showBatchGenerateUnits)
                                    <i class="fas fa-caret-right"></i>
                                @else
                                    <i class="fas fa-caret-down"></i>
                                @endif
                            </button>
                        </div>
                        <div>
                            @if($showBatchGenerateUnits)
                                <div class="bg-light">
                                    <div class="form-group">
                                        <x-input type="text" model="unitForm.unit_quantity">
                                            How Many Units?
                                        </x-input>
                                    </div>
                                    <div class="form-group">
                                        <x-input type="text" model="unitForm.unit_number">
                                            Unit Number Start From?
                                        </x-input>
                                    </div>
                                    <div class="form-group">
                                        <label for="vmmfg_scope_id">
                                            Which Scope?
                                        </label>
                                        <select name="vmmfg_scope_id" wire:model="unitForm.vmmfg_scope_id" class="select form-control">
                                            <option value="">Select...</option>
                                            @foreach($this->scopes as $scope)
                                                <option value="{{$scope->id}}">
                                                    {{$scope->name}} @if($scope->remarks)({{$scope->remarks}})@endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-warning d-none d-sm-block" wire:click.prevent="generateUnits">
                                        Generate Unit(s)
                                    </button>
                                    <button type="submit" class="btn btn-warning btn-block d-block d-sm-none" wire:click.prevent="generateUnits">
                                        Generate Unit(s)
                                    </button>
                                </div>
                            @endif
                        </div>
                    </x-slot>
                    <x-slot name="footer">
                        <button type="submit" class="btn btn-success d-none d-sm-block" wire:click.prevent="save">
                            Save
                        </button>
                        <button type="submit" class="btn btn-success btn-block d-block d-sm-none" wire:click.prevent="save">
                            Save
                        </button>

                    </x-slot>
                </x-modal>
            {{-- </form> --}}

            <x-modal id="create-modal">
                <x-slot name="title">
                    Create Job
                </x-slot>
                <x-slot name="content">
                    <x-input type="text" model="form.batch_no">
                        Batch No
                    </x-input>
                    <x-input type="text" model="form.model">
                        Model
                    </x-input>
                    <x-input type="date" model="form.order_date">
                        Order Date
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
        </div>
    </div>

</div>
