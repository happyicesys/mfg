<div>
    <div>
        <div>
            <x-flash></x-flash>
            <h2>VM MFG Scopes Settings</h2>
            <hr>
            @inject('vmmfgUnits', 'App\Models\VmmfgUnit')
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
                        <th class="text-center">
                            Action
                        </th>
                    </tr>
                    @forelse($scopes as $index => $scope)
                    <tr class="row_edit" wire:loading.class.delay="opacity-2" wire:key="row-{{$index}}">
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
                            <div class="btn-group">
                                <button type="button" wire:key="edit-scope-{{$scope->id}}" wire:click="edit({{$scope}})" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#edit-scope">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-primary" wire:key="replicate-scope-{{$scope->id}}"  onclick="return confirm('Are you sure you want to Replicate?') || event.stopImmediatePropagation()" wire:click.prevent="replicateScope({{$scope}})">
                                    <i class="fas fa-clone"></i>
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
                {{ $scopes->links() }}
            </div>

            {{-- <form wire:submit.prevent="save"> --}}
                <x-modal id="edit-scope">
                    <x-slot name="title">
                        Edit Scope
                    </x-slot>
                    <x-slot name="content">
                        <x-input type="text" model="scope.name">
                            Name
                        </x-input>
                        <div class="form-group">
                            <label for="remarks">
                                Remarks
                            </label>
                            <textarea name="remarks" wire:model="scope.remarks" rows="5" class="form-control" placeholder="Remarks"></textarea>
                        </div>
                        <hr>
                        {{-- <div class="form-group">
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
                                <div class="bg-light p-2">
                                    <div class="form-group">
                                        <x-input type="number" model="titleForm.sequence">
                                            Sequence (Number only)
                                        </x-input>
                                    </div>
                                    <div class="form-group">
                                        <x-input type="text" model="titleForm.name">
                                            Name
                                        </x-input>
                                    </div>
                                    <button type="submit" class="btn btn-success d-none d-sm-block" wire:click.prevent="generateTitle">
                                        <i class="fas fa-plus-circle"></i>
                                        Create Title
                                    </button>
                                    <button type="submit" class="btn btn-success btn-block d-block d-sm-none" wire:click.prevent="generateTitle">
                                        <i class="fas fa-plus-circle"></i>
                                        Create Title
                                    </button>
                                </div>
                            @endif
                        </div>
                        <hr> --}}
                        @if(isset($this->scope))
                            <div class="form-group">
                                <button type="button" class="btn btn-success btn-md" data-toggle="modal" data-target="#title-modal" wire:click="createTitle({{$this->scope}})">
                                    <i class="fas fa-plus-circle"></i>
                                    Title
                                </button>
                            </div>
                            <ul class="list-group"  wire:key="scope-{{$this->scope->id}}">
                                @if($this->scope->vmmfgTitles()->exists())
                                @foreach($this->scope->vmmfgTitles as $titleIndex => $title)
                                <li class="list-group-item mt-2" style="background-color: #9bc2cf;" wire:key="title-{{$titleIndex}}">
                                    <div class="form-group">
                                        <span class="float-left">
                                            {{$title->sequence}}.  {{$title->name}}
                                            @if($title->vmmfgTitleCategory)
                                                <span class="badge badge-warning">
                                                    {{$title->vmmfgTitleCategory->name}}
                                                </span>
                                            @endif
                                        </span>
                                        <span class="float-right">
                                            {{-- <button type="button" class="btn btn-success btn-md" wire:click="showCreateTask({{$title->id}})"> --}}
                                            <div class="btn-group">
                                                <button type="button" wire:click="editTitle({{$title}})" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#title-modal">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-success btn-md" data-toggle="modal" data-target="#task-modal" wire:click="createTask({{$title}})">
                                                    <i class="fas fa-plus-circle"></i>
                                                    Task
                                                </button>
                                            </div>
                                        </span>
                                    </div>
{{--
                                    @if($showCreateTaskArea === $title->id)
                                        <div class="form-group">
                                            <input type="text" wire:model=taskForm.name class="form-control" placeholder="New Task Name">
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-success d-none d-sm-block" wire:click.prevent="saveNewTask">
                                                <i class="fas fa-save"></i>
                                                Save
                                            </button>
                                            <button type="submit" class="btn btn-success btn-block d-block d-sm-none" wire:click.prevent="saveNewTask">
                                                <i class="fas fa-save"></i>
                                                Save
                                            </button>
                                        </div>
                                    @endif --}}
                                </li>
                                    @if($title->vmmfgItems()->exists())
                                        @foreach($title->vmmfgItems as $itemIndex => $item)
                                        <li class="list-group-item ml-2" style="background-color: #e6f3f7;" wire:key="item-{{$itemIndex}}">
                                            <div class="form-group">
                                                <span class="float-left">
                                                    {{$item->sequence}}.  {{$item->name}}
                                                </span>
                                                <span class="float-right">
                                                    <button type="button" wire:click="editTask({{$item}})" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#task-modal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </li>
                                        @endforeach
                                    @endif
                                @endforeach
                                @endif
                            </ul>
                        @endif
                    </x-slot>
                    <x-slot name="footer">
                        {{-- normal view --}}
                        <div class="btn-group">
                            @php
                                $vmmfgUnitCount = 0;
                                if(isset($this->scope)) {
                                    $vmmfgUnitCount = $vmmfgUnits->where('vmmfg_scope_id', $this->scope->id)->count();
                                }
                            @endphp
                            <button type="submit" class="btn btn-danger d-none d-sm-block" wire:click.prevent="delete" {{$vmmfgUnitCount > 0 ? 'disabled' : ''}}>
                                <i class="fas fa-trash"></i>
                                Delete
                                @if($vmmfgUnitCount > 0)
                                    - {{$vmmfgUnitCount}} unit(s) still using this scope
                                @endif
                            </button>
                            <button type="submit" class="btn btn-success d-none d-sm-block" wire:click.prevent="save">
                                <i class="fas fa-save"></i>
                                Save
                            </button>
                        </div>
                        {{-- phone view --}}
                        <button type="submit" class="btn btn-danger btn-block d-block d-sm-none" wire:click.prevent="delete" {{$vmmfgUnitCount > 0 ? 'disabled' : ''}}>
                            <i class="fas fa-trash"></i>
                            Delete
                            @if($vmmfgUnitCount > 0)
                                - {{$vmmfgUnitCount}} unit(s) still using this scope
                            @endif
                        </button>
                        <button type="submit" class="btn btn-success btn-block d-block d-sm-none" wire:click.prevent="save">
                            <i class="fas fa-save"></i>
                            Save
                        </button>
                    </x-slot>
                </x-modal>
            {{-- </form> --}}
            <x-modal id="create-modal">
                <x-slot name="title">
                    Create Scope
                </x-slot>
                <x-slot name="content">
                    <x-input type="text" model="scope.name">
                        Name
                    </x-input>
                    <label for="remarks">
                        Remarks
                    </label>
                    <textarea name="remarks" rows="5" wire:model="scope.remarks" class="form-control" placeholder="Remarks"></textarea>
                </x-slot>
                <x-slot name="footer">
                    <button type="submit" class="btn btn-success d-none d-sm-block" wire:click.prevent="save">
                        <i class="fas fa-save"></i>
                        Save
                    </button>
                    <button type="submit" class="btn btn-success btn-block d-block d-sm-none" wire:click.prevent="save">
                        <i class="fas fa-save"></i>
                        Save
                    </button>
                </x-slot>
            </x-modal>

            <x-modal id="title-modal">
                <x-slot name="title">
                    @if(isset($this->title) and $this->title->id) Edit Title: {{$this->title->sequence}}. {{$this->title->name}} @else Create Title @endif
                </x-slot>
                <x-slot name="content">
                    <x-input type="text" model="title.sequence">
                        Sequence
                    </x-input>
                    <x-input type="text" model="title.name">
                        Name
                    </x-input>
                    <div class="form-group">
                        <label>
                            Category
                        </label>
                        <select name="vmmfg_title_category_id" wire:model.defer="title.vmmfg_title_category_id" class="select form-control">
                            <option value="">None</option>
                            @foreach($vmmfgTitleCategories as $vmmfgTitleCategory)
                                <option value="{{$vmmfgTitleCategory->id}}">
                                    {{$vmmfgTitleCategory->name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </x-slot>
                <x-slot name="footer">
                    {{-- normal view --}}
                    <button type="submit" class="btn btn-danger d-none d-sm-block" wire:click.prevent="deleteTitle">
                        <i class="fas fa-trash"></i>
                        Delete
                    </button>
                    <button type="submit" class="btn btn-success d-none d-sm-block" wire:click.prevent="saveTitle">
                        <i class="fas fa-save"></i>
                        Save
                    </button>
                    {{-- phone view --}}
                    <button type="submit" class="btn btn-danger btn-block d-block d-sm-none" wire:click.prevent="deleteTitle">
                        <i class="fas fa-trash"></i>
                        Delete
                    </button>
                    <button type="submit" class="btn btn-success btn-block d-block d-sm-none" wire:click.prevent="saveTitle">
                        <i class="fas fa-save"></i>
                        Save
                    </button>
                </x-slot>
            </x-modal>

            <x-modal id="task-modal">
                <x-slot name="title">
                    @if(isset($this->item) and $this->item->id) Edit Task: {{$this->item->sequence}}. {{$this->item->name}} @else Create Task @endif
                </x-slot>
                <x-slot name="content">
                    <x-input type="text" model="item.sequence">
                        Sequence
                    </x-input>
                    <x-input type="text" model="item.name">
                        Name
                    </x-input>
                    <div class="form-group">
                        <label for="remarks">
                            Desc
                        </label>
                        <textarea name="remarks" rows="5" wire:model.defer="item.remarks" class="form-control" placeholder="Desc"></textarea>
                    </div>
                    @if(isset($this->item) and $this->item->id)
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <label class="form-check-label" for="is_required">Required to Reply Text?</label>
                                <input class="form-check-input ml-2" type="checkbox" name="is_required" wire:model.defer="item.is_required">
                            </div>
{{--
                            <div class="form-check form-check-inline">
                                <label class="form-check-label" for="is_required_upload">Required to Upload Image(s)?</label>
                                <input class="form-check-input ml-2" type="checkbox" name="is_required_upload" wire:model.defer="item.is_required_upload">
                            </div> --}}
                        </div>
                        <div class="form-group">
                            <label for="file">
                                Upload File(s)
                            </label>
                            <input type="file" class="form-control-file" wire:model="file">
                            {{-- <x-input-file wire:model="file" multiple></x-input-file> --}}
                        </div>
                        <div class="form-group">
                            @if($this->item->attachments)
                                @foreach($this->item->attachments as $attachmentIndex => $attachment)
                                <div class="card" style="max-width:600px;width:100%;" wire:key="attachment-{{$attachmentIndex}}">
                                        @php
                                            $ext = pathinfo($attachment->full_url, PATHINFO_EXTENSION);
                                        @endphp
                                        @if($ext === 'pdf')
                                            <embed src="{{$attachment->full_url}}" type="application/pdf" class="card-img-top" style="min-height: 500px;">
                                        @elseif($ext === 'mov' or $ext === 'mp4')
                                            <div class="embed-responsive embed-responsive-16by9">
                                                <video class=" embed-responsive-item video-js" controls>
                                                    <source src="{{$attachment->full_url}}">
                                                    Your browser does not support the video tag.
                                                </video>
                                            </div>
                                        @else
                                            <img class="card-img-top" src="{{$attachment->full_url}}" alt="">
                                        @endif
                                        <div class="card-body">
                                            <div class="btn-group d-none d-sm-block">
                                                <button type="button" class="btn btn-warning" wire:click="downloadAttachment({{$attachment}})">
                                                    <i class="fas fa-cloud-download-alt"></i>
                                                    Download
                                                </button>
                                                <button type="button" class="btn btn-danger" wire:click="deleteAttachment({{$attachment}})">
                                                    <i class="fas fa-trash"></i>
                                                    Delete
                                                </button>
                                            </div>
                                            <div class="d-block d-sm-none">
                                                <button type="button" class="btn btn-block btn-warning" wire:click="downloadAttachment({{$attachment}})">
                                                    <i class="fas fa-cloud-download-alt"></i>
                                                    Download
                                                </button>
                                                <button type="button" class="btn btn-block btn-danger" wire:click="deleteAttachment({{$attachment}})">
                                                    <i class="fas fa-trash"></i>
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </x-slot>
                <x-slot name="footer">
                    {{-- normal view --}}
                    <div class="btn-group">
                        <button type="submit" class="btn btn-danger d-none d-sm-block" wire:click.prevent="deleteTask">
                            <i class="fas fa-trash"></i>
                            Delete
                        </button>
                        <button type="submit" class="btn btn-success d-none d-sm-block" wire:click.prevent="saveTask">
                            <i class="fas fa-save"></i>
                            Save
                        </button>
                    </div>
                    {{-- phone view --}}
                    <button type="submit" class="btn btn-danger btn-block d-block d-sm-none" wire:click.prevent="deleteTask">
                        <i class="fas fa-trash"></i>
                        Delete
                    </button>
                    <button type="submit" class="btn btn-success btn-block d-block d-sm-none" wire:click.prevent="saveTask">
                        <i class="fas fa-save"></i>
                        Save
                    </button>
                </x-slot>
            </x-modal>
        </div>
    </div>

</div>
