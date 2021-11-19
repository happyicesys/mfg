<div>
    <div>
        <x-flash></x-flash>
        <h2>Company Profile</h2>
        <hr>
        @php
            $profilesArr = $profiles->toArray();
            $from = $profilesArr['from'];
            $total = $profilesArr['total'];
            // dd($profilesArr);
        @endphp
        <div class="d-none d-sm-block">
            <div class="form-group form-inline">
                <label for="name">
                    Quick Search
                </label>
                <input type="text" wire:model="filters.search" class="form-control mx-2" placeholder="Quick Search" @if($showFilters) disabled @endif>
                {{-- <x-date model="filters.date"></x-date> --}}
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
                                    Name
                                </label>
                                <input wire:model="filters.name" type="text" class="form-control" placeholder="Name">
                                {{-- <input data-date-format="yyyy-mm-dd" wire:model="filters.name" class="form-control" type="date"> --}}
                                {{-- <x-date model="filters.name">Date</x-date> --}}
                            </div>
                            <div class="form-group col-4">
                                <label>
                                    Symbol
                                </label>
                                <input wire:model="filters.symbol" type="text" class="form-control" placeholder="Symbol">
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
                            Showing {{ count($profiles) }} of {{$total}}
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
                                    Name
                                </label>
                                <input wire:model="filters.name" type="text" class="form-control" placeholder="Name">
                            </div>
                            <div class="form-group">
                                <label>
                                    Symbol
                                </label>
                                <input wire:model="filters.symbol" type="text" class="form-control" placeholder="Symbol">
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

                <div class="form-group form-inline">
                    <label class="mt-1" for="display_num">Display </label>
                    <select wire:model="itemPerPage" class="ml-1 mr-1" name="pageNum">
                        <option value="100">100</option>
                        <option value="200">200</option>
                        <option value="500">500</option>
                    </select>
                    <label class="mt-1" for="display_num2" style="padding-right: 20px"> per Page</label>
                    <label class="ml-auto">
                        Showing {{ count($profiles) }} of {{$total}}
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
                    <x-th-data model="name" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                        Name
                    </x-th-data>
                    <x-th-data model="symbol" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                        Symbol
                    </x-th-data>
                    <x-th-data model="reg_no" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                        Reg No (UEN)
                    </x-th-data>
                    {{-- <x-th-data model="email" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                        Address
                    </x-th-data> --}}
                    <th></th>
                </tr>

                @forelse($profiles as $index => $profile)
                <tr class="row_edit" wire:loading.class.delay="opacity-2" wire:key="row-{{$profile->id}}">
                    {{-- <th class="text-center">
                        <input type="checkbox" wire:model="selected" value="{{$admin->id}}">
                    </th> --}}
                    <td class="text-center">
                        {{ $index + $from}}
                    </td>
                    <td class="text-left">
                        {{ $profile->name }}
                    </td>
                    <td class="text-center">
                        {{ $profile->symbol }}
                    </td>
                    <td class="text-left">
                        {{ $profile->reg_no }}
                    </td>
                    {{-- <td class="text-left">
                        {{ $profile->address }}
                    </td> --}}
                    <td class="text-center">
                        <button type="button" wire:click="edit({{$profile->id}})" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#edit-profile">
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
            {{ $profiles->links() }}
        </div>

        {{-- <form wire:submit.prevent="save"> --}}
            <x-modal id="edit-profile">
                <x-slot name="title">
                    Edit Profile
                </x-slot>
                <x-slot name="content">
                    <x-input type="text" model="form.name">
                        Name
                    </x-input>
                    <x-input type="text" model="form.symbol">
                        Symbol
                    </x-input>
                    <x-input type="text" model="form.reg_no">
                        Reg No (UEN)
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
