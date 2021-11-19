<div>
    <div>
        <div>
            <x-flash></x-flash>
            <h2>VM MFG Scopes Settings</h2>
            <hr>
            @php
                $scopesArr = $scopes->toArray();
                $from = $scopesArr['from'];
                $total = $scopesArr['total'];
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
                                        Remarks
                                    </label>
                                    <input wire:model="filters.remarks" type="text" class="form-control" placeholder="Remarks">
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
                                Showing {{ count($scopes) }} of {{$total}}
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
                                        Remarks
                                    </label>
                                    <input wire:model="filters.remarks" type="text" class="form-control" placeholder="Remarks">
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
                            Showing {{ count($scopes) }} of {{$total}}
                        </label>
                    </div>
            </div>

            <div class="table-responsive pt-3" style="font-size: 14px;">
                <table class="table table-bordered table-hover">
                    <tr class="table-secondary">
                        <th class="text-center">
                            #
                        </th>
                        <x-th-data model="name" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Name
                        </x-th-data>
                        <x-th-data model="remarks" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Remarks
                        </x-th-data>
                        <th></th>
                    </tr>
                    @forelse($scopes as $index => $scope)
                    <tr class="row_edit" wire:loading.class.delay="opacity-2" wire:key="row-{{$scope->id}}">
                        {{-- <th class="text-center">
                            <input type="checkbox" wire:model="selected" value="{{$admin->id}}">
                        </th> --}}
                        <td class="text-center">
                            {{ $index + $from}}
                        </td>
                        <td class="text-left">
                            {{ $scope->name }}
                        </td>
                        <td class="text-left">
                            {{ $scope->remarks }}
                        </td>
                        <td class="text-center">
                            <button type="button" wire:click="edit({{$scope->id}})" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#edit-scope">
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
                {{ $scopes->links() }}
            </div>

            {{-- <form wire:submit.prevent="save"> --}}
                <x-modal id="edit-scope">
                    <x-slot name="title">
                        Edit Scope
                    </x-slot>
                    <x-slot name="content">
                        <x-input type="text" model="form.name">
                            Name
                        </x-input>
                        <div class="form-group">
                            <label for="remarks">
                                Remarks
                            </label>
                            <textarea name="remarks" wire:model="form.remarks" rows="5" class="form-control"></textarea>
                        </div>
                        <hr>
                        <div class="form-group">
                            <button wire:click="$toggle('showCreateTitleArea')" class="btn btn-outline-secondary btn-block">
                                Create Title
                                @if($showCreateTitleArea)
                                    <i class="fas fa-caret-right"></i>
                                @else
                                    <i class="fas fa-caret-down"></i>
                                @endif
                            </button>
                        </div>
                        <div>
                            @if($showCreateTitleArea)
                                <div class="bg-light">
                                    <div class="form-group">
                                        <x-input type="text" model="titleForm.sequence">
                                            Sequence (Number only)
                                        </x-input>
                                    </div>
                                    <div class="form-group">
                                        <x-input type="text" model="titleForm.name">
                                            Name
                                        </x-input>
                                    </div>
                                    <button type="submit" class="btn btn-success d-none d-sm-block" wire:click.prevent="generateTitle">
                                        Create Title
                                    </button>
                                    <button type="submit" class="btn btn-success btn-block d-block d-sm-none" wire:click.prevent="generateTitle">
                                        Create Title
                                    </button>
                                </div>
                            @endif
                        </div>
                        <hr>
                        @if($form)
                            <ul class="list-group">
                                @foreach($form->vmmfgTitles as $title)
                                <li class="list-group-item mt-2" style="background-color: #9bc2cf;">
                                    {{$title->sequence}}.  {{$title->name}}
                                </li>
                                    @foreach($title->vmmfgItems as $item)
                                    <li class="list-group-item ml-5" style="background-color: #e6f3f7;">
                                        {{$item->sequence}}.  {{$item->name}}
                                    </li>
                                    @endforeach
                                @endforeach
                            </ul>
                        @endif
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
        </div>
    </div>

</div>
