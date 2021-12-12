{{-- @extends('layouts.app') --}}
<div>
    <div>
        <x-flash></x-flash>
        <h2>Users</h2>
        <hr>
        @php
            $adminsArr = $admins->toArray();
            $from = $adminsArr['from'];
            $total = $adminsArr['total'];
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
                                    Name
                                </label>
                                <input wire:model="filters.name" type="text" class="form-control" placeholder="Name">
                            </div>
                            <div class="form-group col-4">
                                <label>
                                    Phone Number
                                </label>
                                <input wire:model="filters.phone_number" type="text" class="form-control" placeholder="Phone Number">
                            </div>
                            <div class="form-group col-4">
                                <label>
                                    Email
                                </label>
                                <input wire:model="filters.email" type="text" class="form-control" placeholder="Email">
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
                            {{-- <x-select2 wire:model="itemPerPage" id="itemPerPage" name="itemPerPage" select-type="label"> --}}
                            <option value="100">100</option>
                            <option value="200">200</option>
                            <option value="500">500</option>
                            {{-- </x-select2> --}}
                        </select>
                        <label for="display_num2" style="padding-right: 20px"> per Page</label>
                    </div>
                    <div>
                        <label style="padding-right:18px; font-weight: bold;">
                            Showing {{ count($admins) }} of {{$total}}
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
                                    Phone Number
                                </label>
                                <input wire:model="filters.phone_number" type="text" class="form-control" placeholder="Phone Number">
                            </div>
                            <div class="form-group">
                                <label>
                                    Email
                                </label>
                                <input wire:model="filters.email" type="text" class="form-control" placeholder="Email">
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
                        Showing {{ count($admins) }} of {{$total}}
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
                    <x-th-data model="phone_number" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                        Phone Number
                    </x-th-data>
                    <x-th-data model="email" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                        Email
                    </x-th-data>
                    <th class="text-center">
                        Status
                    </th>
                    <x-th-data model="created_at" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                        Created At
                    </x-th-data>
                    <th></th>
                </tr>

                @forelse($admins as $index => $admin)
                <tr class="row_edit" wire:loading.class.delay="opacity-2" wire:key="row-{{$admin->id}}">
                    {{-- <th class="text-center">
                        <input type="checkbox" wire:model="selected" value="{{$admin->id}}">
                    </th> --}}
                    {{-- @dd($loop) --}}
                    <td class="text-center">
                        {{ $index + $from}}
                    </td>
                    <td class="text-left">
                        {{ $admin->name }}
                    </td>
                    <td class="text-left">
                        {{ $admin->phone_number }}
                    </td>
                    <td class="text-left">
                        {{ $admin->email }}
                    </td>
                    <td class="text-center">
                        Active
                    </td>
                    <td class="text-center">
                        {{ $admin->created_at }}
                    </td>
                    <td class="text-center">
                        <button type="button" wire:click="edit({{$admin->id}})" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#edit-admin">
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
            {{ $admins->links() }}
        </div>

        {{-- <form wire:submit.prevent="save"> --}}
            <x-modal id="edit-admin">
                <x-slot name="title">
                    Edit User
                </x-slot>
                <x-slot name="content">
                    <x-input type="text" model="form.name">
                        Name
                    </x-input>
                    <x-input type="text" model="form.username">
                        Username
                    </x-input>
                    <x-input type="text" model="form.phone_number">
                        Phone Number
                    </x-input>
                    <x-input type="text" model="form.email">
                        Email
                    </x-input>
                    <x-input type="password" model="form.password">
                        Password (Overwrite, Leave Blank to use the same)
                    </x-input>
                    <div class="form-group">
                        <label>
                            Role
                        </label>
                        <select name="role_id" wire:model.defer="role_id" class="select form-control">
                            <option value="">Select..</option>
                            @foreach($roles as $role)
                                <option value="{{$role->id}}">
                                    {{$role->name}}
                                </option>
                            @endforeach
                        </select>
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
