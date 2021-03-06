<div>
    <div>
    <x-flash></x-flash>
    <h2>VM MFG BOM</h2>
    <hr>
    @php
        $bomsArr = $boms->toArray();
        $from = $bomsArr['from'];
        $total = $bomsArr['total'];
        $profile = \App\Models\Profile::where('is_primary', 1)->first();
        $amountTotal = 0;
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
                        Showing {{ count($boms) }} of {{$total}}
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
                    Showing {{ count($boms) }} of {{$total}}
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
            @forelse($boms as $index => $bomItem)
            <tr class="row_edit" wire:loading.class.delay="opacity-2" wire:key="row-{{$index}}">
                {{-- <th class="text-center">
                    <input type="checkbox" wire:model="selected" value="{{$admin->id}}">
                </th> --}}
                <td class="text-center">
                    {{ $index + $from}}
                </td>
                <td class="text-left">
                    {{ $bomItem->name }}
                </td>
                <td class="text-left">
                    {{ $bomItem->remarks }}
                </td>
                <td class="text-center">
                    <div class="btn-group">
                        <button type="button" wire:key="edit-bom-{{$bomItem->id}}" wire:click="edit({{$bomItem}})" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#edit-bom">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-primary" wire:key="replicate-scope-{{$bomItem->id}}"  onclick="return confirm('Are you sure you want to Replicate?') || event.stopImmediatePropagation()" wire:click.prevent="replicateBom({{$bomItem}})">
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
        {{ $boms->links() }}
    </div>

    <x-modal id="edit-bom">
        <x-slot name="title">
            Edit BOM
        </x-slot>
        <x-slot name="content">
            <x-input type="text" model="bom.name">
                Name
            </x-input>
            <div class="form-group">
                <label for="remarks">
                    Remarks
                </label>
                <textarea name="remarks" wire:model="bom.remarks" rows="5" class="form-control" placeholder="Remarks"></textarea>
            </div>
            <div class="btn-group">
                <button
                type="submit"
                class="btn btn-outline-secondary"
                wire:click.prevent="save()"
                >
                    Save
                </button>
                <button type="button" class="btn btn-danger btn-md" onclick="confirm('Are you sure you want to remove this bom?') || event.stopImmediatePropagation()" wire:click="deleteBom({{$bom}})">
                    <i class="fas fa-trash"></i>
                    Delete
                </button>
            </div>
            <hr>
            @if(isset($bom))
                @hasanyrole('admin|superadmin')
                <div class="btn-group">
                    <button type="button" class="btn btn-success btn-md" data-toggle="modal" data-target="#header-modal" wire:click.prevent="createHeader({{$bom}})">
                        <i class="fas fa-plus-circle"></i>
                        Group
                    </button>
                    <button type="button" class="btn btn-danger btn-md" onclick="confirm('Are you sure you want to remove these BOM part(s)?') || event.stopImmediatePropagation()" wire:click="batchDeleteBomHeaderBomContent({{$bom}})">
                        <i class="fas fa-trash"></i>
                        Batch Delete Part(s)
                    </button>
                </div>
                @endhasanyrole

                <div class="table-responsive pt-3">
                    <table class="table table-bordered table-sm">
                        <tr class="d-flex">
                            <th class="col bg-secondary text-white text-center">
                                <input type="checkbox" wire:model="selectAll">
                            </th>
                            <th class="col-md-2 bg-secondary text-white text-center">
                                Cat1/Cat2
                            </th>
                            <th class="col-md-1 bg-secondary text-white text-center">
                                Type
                            </th>
                            <th class="col-md-2 bg-secondary text-white text-center">
                                Code
                            </th>
                            <th class="col-md-4 bg-secondary text-white text-center">
                                Name
                            </th>
                            <th class="col-md-1 bg-secondary text-white text-center">
                                Qty
                            </th>
                            <th class="col-md-1 bg-secondary text-white text-center">
                                Amt ({{$profile->country->currency_name}})
                            </th>
                            <th class="col-md-1 bg-secondary text-white text-center">
                                Inv?
                            </th>
                            <th class="col-md-2 bg-secondary text-white text-center">
                                Action
                            </th>
                        </tr>
                    </table>
                    @if($bom->bomHeaders()->exists())
                        @foreach($bom->bomHeaders as $bomHeaderIndex => $bomHeader)
                        <table class="table table-borderless table-sm" wire:key="header-table-{{$bomHeaderIndex}}">
                            <tr class="d-flex border border-secondary">
                                <th class="col-md-1 bg-info text-dark text-center">
                                    <input type="checkbox" wire:model="selectBomHeader" wire:click="selectedHeader({{$bomHeader->id}})" value="{{$bomHeader->id}}">
                                </th>
                                <th class="col-md-2 bg-info text-dark">
                                    {{$bomHeader->sequence}}
                                    @if($bomHeader->bomCategory)
                                        <span class="badge badge-dark pl-1">
                                            {{$bomHeader->bomCategory->name}}
                                        </span>
                                    @endif
                                </th>
                                <th class="col-md-1 bg-info">
                                    @if(isset($bomHeader->bomItem) and isset($bomHeader->bomItem->bomItemType))
                                        <span class="badge badge-dark">
                                            {{ $bomHeader->bomItem->bomItemType->name }}
                                        </span>
                                    @endif
                                </th>
                                <th class="col-md-2 bg-info text-dark">
                                    {{$bomHeader->bomItem ? $bomHeader->bomItem->code : null}}
                                </th>
                                <th class="col-md-6 bg-info text-dark">
                                    {{$bomHeader->bomItem ? $bomHeader->bomItem->name : null}}
                                </th>
                                <th class="col-md-1 bg-info text-dark text-center">
                                    {{-- {{$bomHeader->qty }} --}}
                                </th>
                                <td class="col-md-2 bg-info text-center">
                                    <div class="btn-group">
                                        @hasanyrole('admin|superadmin')
                                            <button type="button" wire:click="editHeader({{$bomHeader}})" wire:key="header-edit-{{$bomHeaderIndex}}" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#header-modal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-warning btn-sm" wire:click="createSubGroup({{$bomHeader}})" wire:key="header-create-sub-group-{{$bomHeaderIndex}}" data-toggle="modal" data-target="#content-modal">
                                                <i class="fas fa-plus-circle"></i>
                                                SubG
                                            </button>
                                            <button type="button" class="btn btn-success btn-sm" wire:click="createContent({{$bomHeader}})" wire:key="header-create-content-{{$bomHeaderIndex}}" data-toggle="modal" data-target="#content-modal">
                                                <i class="fas fa-plus-circle"></i>
                                                P
                                            </button>
                                        @endhasanyrole
                                        @if($bomHeader->bomItem->attachments()->exists())
                                            <button type="button" class="btn btn-outline-dark btn-sm" wire:click="viewAttachmentsByBomItem({{$bomHeader->bomItem}})" wire:key="header-view-attachment-{{$bomHeaderIndex}}" data-toggle="modal" data-target="#attachment-modal">
                                                <i class="far fa-images"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @if($bomHeader->bomContents()->exists())
                                @foreach($bomHeader->bomContents->sortBy('sequence', SORT_NATURAL) as $bomContent)
                                    @php
                                        $sequenceStyle = '';
                                        $dotCount = substr_count($bomContent->sequence, '.');
                                        switch($dotCount) {
                                            case 1:
                                                $sequenceStyle = 'text-dark';
                                                break;
                                            case 2:
                                                $sequenceStyle = 'pl-2 text-dark';
                                                break;
                                            case 3:
                                                $sequenceStyle = 'pl-4 text-secondary';
                                                break;
                                            case 4:
                                                $sequenceStyle = 'pl-5 text-secondary';
                                                break;
                                            default:
                                                $sequenceStyle = 'text-dark';
                                        }
                                    @endphp
                                    <tr class="d-flex border border-secondary ml-3">
                                        <th class="col-md-1 {{$bomContent->is_group ? 'bg-info' : 'bg-light'}} text-dark text-center">
                                            <input type="checkbox" wire:model.defer="selectBomContent" wire:click="selectedContent({{$bomContent->id}})" value="{{$bomContent->id}}">
                                        </th>
                                        <th class="col-md-2 {{$bomContent->is_group ? 'bg-info' : 'bg-light'}} text-dark">
                                            {{$bomContent->sequence}}
                                            @if($bomContent->bomSubCategory)
                                                <span class="badge badge-dark pl-1">
                                                    {{$bomContent->bomSubCategory->name}}
                                                </span>
                                            @endif
                                        </th>
                                        <th class="col-md-1 {{$bomContent->is_group ? 'bg-info' : 'bg-light'}}">
                                            @if(isset($bomContent->bomItem) and isset($bomContent->bomItem->bomItemType))
                                                <span class="badge badge-dark">
                                                    {{ $bomContent->bomItem->bomItemType->name }}
                                                </span>
                                            @endif
                                        </th>
                                        <th class="col-md-2 {{$bomContent->is_group ? 'bg-info' : 'bg-light'}} text-dark">
                                            {{$bomContent->bomItem->code}}
                                            @php
                                                $bomItemCount = \App\Models\BomContent::where('bom_item_id', $bomContent->bomItem->id)->whereHas('bomHeader', function($query) use ($bomContent) {
                                                    $query->where('bom_id', $bomContent->bomHeader->bom_id);
                                                })->count();
                                            @endphp
                                            @if($bomItemCount > 1)
                                                <small>
                                                    <span class="badge badge-pill badge-danger">&nbsp;</span>
                                                </small>
                                            @endif
                                        </th>
                                        <th class="col-md-4 {{$bomContent->is_group ? 'bg-info' : 'bg-light'}} {{$sequenceStyle}}">
                                            <span class="">
                                                {{$bomContent->bomItem->name}}
                                            </span>
                                        </th>
                                        <th class="col-md-1 {{$bomContent->is_group ? 'bg-info' : 'bg-light'}} text-dark text-center">
                                            @if($bomContent->is_group)
                                                {{$bomContent->qty ? $bomContent->qty : ''}}
                                            @else
                                                {{$bomContent->qty}}
                                            @endif
                                        </th>
                                        @php
                                            $unitPrice = 0;
                                            $amount = 0;

                                            if($bomContent->bomItem->supplierQuotePrices()->exists()) {
                                                $unitPrice = $bomContent->bomItem->supplierQuotePrices()->latest()->first()->base_price;
                                            }
                                            if($unitPrice and $bomContent->qty) {
                                                $amount = $unitPrice * $bomContent->qty;
                                                $amountTotal += $amount;
                                            }
                                        @endphp
                                        <th class="col-md-1 {{$bomContent->is_group ? 'bg-info' : 'bg-light'}} text-dark text-right">
                                            {{$amount}}
                                        </th>
                                        <th class="col-md-1 {{$bomContent->is_group ? 'bg-info' : 'bg-light'}} text-dark text-center">
                                            @if($bomContent->bomItem->is_part)
                                                @if($bomContent->bomItem->is_inventory)
                                                    <i class="far fa-check-circle text-success"></i>
                                                @else
                                                    <i class="far fa-times-circle text-danger"></i>

                                                    @if($bomContent->bomItem->bom_item_parent_id)
                                                        <span class="badge badge-info">
                                                            {{ $bomContent->bomItem->parent->code }}
                                                        </span>
                                                    @endif
                                                @endif
                                            @endif
                                        </th>
                                        <td class="col-md-2 {{$bomContent->is_group ? 'bg-info' : 'bg-light'}} text-center">
                                            <div class="btn-group">
                                                @hasanyrole('admin|superadmin')
                                                    <button type="button" wire:click="editPart({{$bomContent}})" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#content-modal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                @endhasanyrole
                                                @if($bomContent->bomItem->attachments()->exists())
                                                    <button type="button" class="btn btn-outline-dark btn-sm" wire:click="viewAttachmentsByBomItem({{$bomContent->bomItem}})" wire:key="content-view-attachment-{{$bomContent->id}}" data-toggle="modal" data-target="#attachment-modal">
                                                        <i class="far fa-images"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </table>
                        @endforeach
                    @endif
                </div>
            @endif
        </x-slot>
        <x-slot name="footer"></x-slot>
        {{-- <x-slot name="footer">

            <div class="btn-group">
                @php
                    $vmmfgUnitCount = 0;
                    if(isset($this->scope)) {
                        $vmmfgUnitCount = $vmmfgUnits->where('vmmfg_scope_id', $this->scope->id)->count();
                    }
                @endphp
                <button type="submit" class="btn btn-danger d-none d-sm-block" onclick="confirm('Are you sure you want to remove this scope?') || event.stopImmediatePropagation()" wire:click.prevent="delete" {{$vmmfgUnitCount > 0 ? 'disabled' : ''}}>
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

            <button type="submit" class="btn btn-danger btn-block d-block d-sm-none" onclick="confirm('Are you sure you want to remove this scope?') || event.stopImmediatePropagation()" wire:click.prevent="delete" {{$vmmfgUnitCount > 0 ? 'disabled' : ''}}>
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
        </x-slot> --}}
    </x-modal>

    <x-modal id="create-modal">
        <x-slot name="title">
            Create BOM
        </x-slot>
        <x-slot name="content">
            <x-input type="text" model="bom.name">
                Name
            </x-input>
            <label for="remarks">
                Remarks
            </label>
            <textarea name="remarks" rows="5" wire:model="bom.remarks" class="form-control" placeholder="Remarks"></textarea>
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

    <x-modal id="header-modal">
        <x-slot name="title">
            @if(isset($bomHeaderForm->id)) Edit Group: {{$bomHeaderForm->bomItem->sequence}}. {{$bomHeaderForm->bomItem->name}} @else Create Group @endif
        </x-slot>
        <x-slot name="content">
            <div class="form-group">
                <div class="form-check form-check-inline">
                    <label class="form-check-label" for="is_required">Select from Existing Group?</label>
                    <input class="form-check-input ml-2" type="checkbox" name="is_existing" wire:model="bomHeaderForm.is_existing">
                </div>
            </div>
            <hr>
            <x-input type="text" model="bomHeaderForm.sequence">
                Sequence
            </x-input>
            @if(isset($bomHeaderForm->is_existing) and $bomHeaderForm->is_existing)
            <hr>
                <div class="form-group">
                    <label>
                        Existing Group
                    </label>
                    <select class="select form-control" wire:model.defer="bomHeaderForm.bom_item_id">
                        <option value="">Select..</option>
                        @foreach($bomItemGroups as $bomItem)
                            <option value="{{$bomItem->id}}" {{isset($bomHeaderForm->bom_item_id) && ($bomItem->id == $bomHeaderForm->bom_item_id) ? 'selected' : ''}}>
                                {{$bomItem->code}} - {{$bomItem->name}}
                            </option>
                        @endforeach
                    </select>
                    @error('bomHeaderForm.bom_category_id')
                        <span style="color: red;">
                            <small>
                                {{ $message }}
                            </small>
                        </span>
                    @enderror
                </div>
            <hr>
            @else
                <hr>
                @if(!isset($bomHeaderForm->id))
                    <div class="form-group">
                        <h3 class="badge badge-info">
                            Create New
                        </h3>
                    </div>
                @endif
                <x-input type="text" model="bomHeaderForm.code">
                    Group Code
                </x-input>
                <x-input type="text" model="bomHeaderForm.name">
                    Group Name
                </x-input>
                <div class="form-group">
                    <label>
                        Type
                    </label>
                    <select class="select form-control" wire:model.defer="bomHeaderForm.bom_item_type_id">
                        <option value="">Select..</option>
                        @foreach($bomItemTypes as $bomItemType)
                            <option value="{{$bomItemType->id}}" {{isset($bomHeaderForm->bomItem->bomItemType->id) && ($bomItemType->id == $bomHeaderForm->bomItem->bomItemType->id) ? 'selected' : ''}}>
                                {{ $bomItemType->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('bomHeaderForm.bom_item_type_id')
                        <span style="color: red;">
                            <small>
                                {{ $message }}
                            </small>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="file">
                        Upload File(s)
                    </label>
                    <input type="file" class="form-control-file" wire:model.defer="file">
                </div>
                <div class="form-group">
                    @if(isset($bomHeaderForm->bomItem->attachments))
                        @foreach($bomHeaderForm->bomItem->attachments as $attachmentIndex => $attachment)
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
                <hr>
                <div class="form-group">
                    <label>
                        QA/QC
                    </label>
                    <select class="select form-control" wire:model.defer="bomHeaderForm.vmmfg_item_id">
                        <option value="">Select..</option>
                        @foreach($vmmfgItems as $vmmfgItem)
                            <option value="{{$vmmfgItem->id}}" {{isset($bomHeaderForm->bomItem->vmmfg_item_id) && ($vmmfgItem->id == $bomHeaderForm->bomItem->vmmfg_item_id) ? 'selected' : ''}}>
                                ({{ $vmmfgItem->vmmfgTitle->sequence }}) - ({{ $vmmfgItem->sequence }}) {{ $vmmfgItem->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('bomHeaderForm.vmmfg_item_id')
                        <span style="color: red;">
                            <small>
                                {{ $message }}
                            </small>
                        </span>
                    @enderror
                </div>
            @endif
                {{-- @dd($bomGroupForm->toArray()) --}}
                <div class="form-group">
                    <label>
                        Category1
                    </label>
                    <select class="select form-control" wire:model.defer="bomHeaderForm.bom_category_id">
                        <option value="">Select..</option>
                        @foreach($bomCategories as $bomCategory)
                            <option value="{{$bomCategory->id}}" {{isset($bomHeaderForm->bom_category_id) && ($bomCategory->id == $bomHeaderForm->bom_category_id) ? 'selected' : ''}}>
                                {{$bomCategory->name}}
                            </option>
                        @endforeach
                    </select>
                    @error('bomHeaderForm.bom_category_id')
                        <span style="color: red;">
                            <small>
                                {{ $message }}
                            </small>
                        </span>
                    @enderror
                </div>
                <x-input type="text" model="bomHeaderForm.qty">
                    Qty
                </x-input>
        </x-slot>
        <x-slot name="footer">
            @if($bomHeaderForm->is_edit)
                <button type="submit" class="btn btn-danger btn-xs-block" onclick="return confirm('Are you sure you want to Delete the Group and Unbind the Parts?') || event.stopImmediatePropagation()" wire:click.prevent="deleteHeader">
                    <i class="fas fa-trash"></i>
                    Delete
                </button>
            @endif
            <button type="submit" class="btn btn-success btn-xs-block" wire:click.prevent="saveHeader">
                <i class="fas fa-save"></i>
                Save
            </button>
        </x-slot>
    </x-modal>

    <x-modal id="content-modal">
        <x-slot name="title">
            @if(isset($bomContentForm->id))
                Edit
                @if($bomContentForm->is_group)
                Sub Group:
                @else
                Part:
                @endif
                {{$bomContentForm->bomItem->sequence}}. {{$bomContentForm->bomItem->name}}
                @php
                    $bomItemCount = \App\Models\BomContent::where('bom_item_id', $bomContentForm->bomItem->id)->whereHas('bomHeader', function($query) use ($bomContentForm) {
                        $query->where('bom_id', $bomContentForm->bomHeader->bom_id);
                    })->count();
                @endphp
                @if($bomItemCount > 1)
                    <small>
                        <span class="badge badge-pill badge-danger">&nbsp;</span>
                    </small>
                @endif
            @else
                Create
                @if($bomContentForm->is_group)
                Sub Group
                @else
                Part
                @endif
            @endif
        </x-slot>
        <x-slot name="content">
            <div class="form-group">
                <div class="form-check form-check-inline">
                    <label class="form-check-label" for="is_required">
                        Select from Existing
                        @if($bomContentForm->is_group)
                        Sub Group
                        @else
                        Part
                        @endif
                        ?
                    </label>
                    <input class="form-check-input ml-2" type="checkbox" name="is_existing" wire:model="bomContentForm.is_existing">
                </div>
            </div>
            <hr>
            <x-input type="text" model="bomContentForm.sequence">
                Sequence
            </x-input>
            @if(isset($bomContentForm->is_existing) and $bomContentForm->is_existing)
                <div class="form-group">
                    <label>
                        Existing
                        @if($bomContentForm->is_group)
                        Sub Group
                        @else
                        Part
                        @endif
                    </label>
                    <select class="select form-control" wire:model.defer="bomContentForm.bom_item_id">
                        <option value="">Select..</option>
                        @foreach($bomItemSubGroups as $bomItem)
                            <option value="{{$bomItem->id}}" {{isset($bomContentForm->bom_item_id) && ($bomItem->id == $bomContentForm->bom_item_id) ? 'selected' : ''}}>
                                {{$bomItem->code}} - {{$bomItem->name}}
                            </option>
                        @endforeach
                    </select>
                    @error('bomContentForm.bom_category_id')
                        <span style="color: red;">
                            <small>
                                {{ $message }}
                            </small>
                        </span>
                    @enderror
                </div>
            @else
                <hr>
                @if(!isset($bomContentForm->id))
                    <div class="form-group">
                        <h3 class="badge badge-info">
                            Create New
                        </h3>
                    </div>
                @endif
                <x-input type="text" model="bomContentForm.code">
                    @if($bomContentForm->is_group) Sub Group @else Part @endif Code
                </x-input>
                <x-input type="text" model="bomContentForm.name">
                    @if($bomContentForm->is_group) Sub Group @else Part @endif Name
                </x-input>
                <div class="form-group">
                    <label>
                        Type
                    </label>
                    <select class="select form-control" wire:model.defer="bomContentForm.bom_item_type_id">
                        <option value="">Select..</option>
                        @foreach($bomItemTypes as $bomItemType)
                            <option value="{{$bomItemType->id}}" {{isset($bomContentForm->bomItem->bomItemType->id) && ($bomItemType->id == $bomContentForm->bomItem->bomItemType->id) ? 'selected' : ''}}>
                                {{ $bomItemType->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('bomContentForm.bom_item_type_id')
                        <span style="color: red;">
                            <small>
                                {{ $message }}
                            </small>
                        </span>
                    @enderror
                </div>
                <hr>
                @if(!$bomContentForm->is_group)
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <label class="form-check-label" for="is_required">Is Inventory?</label>
                            <input class="form-check-input ml-2" type="checkbox" name="is_inventory" wire:model="bomContentForm.is_inventory">
                        </div>
                    </div>
                @endif

                @if($bomContentForm->is_inventory == false)
                    <div class="bg-light pt-2 pb-2 pl-2 pr-2 mb-2">
                    <div class="form-row">
                        <div class="form-group col-md-12 col-xs-12">
                            <label class="form-check-label">Is the Children for</label>
                        </div>
                        <div class="form-group col-md-3 col-xs-12">
                            <label for="name">
                                Code Filter
                            </label>
                            <input type="text" wire:model.debounce.500ms="bomContentFormFilters.code" class="form-control" placeholder="Code">
                        </div>
                        <div class="form-group col-md-3 col-xs-12">
                            <label for="name">
                                Name Filter
                            </label>
                            <input type="text" wire:model.debounce.500ms="bomContentFormFilters.name" class="form-control" placeholder="Name">
                        </div>
                        <div class="form-group col-md-3 col-xs-12">
                            <label for="name">
                                Type Filter
                            </label>
                            <select class="select form-control" wire:model="bomContentFormFilters.bom_item_type_id">
                                <option value="">All</option>
                                @foreach($bomItemTypes as $bomItemType)
                                    <option value="{{$bomItemType->id}}" {{isset($bomItemForm->bom_item_type_id) && ($bomItemType->id == $bomItemForm->bom_item_type_id) ? 'selected' : ''}}>
                                        {{ $bomItemType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3 col-xs-12">
                            <label for="supplier_id">
                                Supplier Filter
                            </label>
                            <select class="select form-control" wire:model="bomContentFormFilters.supplier_id">
                                <option value="">All</option>
                                @foreach($suppliers as $supplierOption)
                                    <option value="{{$supplierOption->id}}">
                                        {{ $supplierOption->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-12 col-xs-12">
                            <label for="bom_item_parent_id">
                                Part
                            </label>
                            <label for="*" class="text-danger">*</label>
                            <select wire:model.defer="bomContentForm.bom_item_parent_id" class="form-control select">
                                <option value="">Select..</option>
                                @foreach($bomItemsFilters as $bomItemsFilter)
                                    <option value="{{ $bomItemsFilter->id }}" {{isset($bomContentForm->bomItem->bom_item_parent_id) && ($bomItemsFilter->id == $bomContentForm->bomItem->bom_item_parent_id) ? 'selected' : ''}}>
                                        {{ $bomItemsFilter->code }} - {{ $bomItemsFilter->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    </div>
                @endif
                <hr>
                <div class="form-group">
                    <label for="file">
                        Upload File(s)
                    </label>
                    <input type="file" class="form-control-file" wire:model.defer="file">
                </div>
                <div class="form-group">
                    @if(isset($bomContentForm->bomItem->attachments))
                        @foreach($bomContentForm->bomItem->attachments as $attachmentIndex => $attachment)
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
                <hr>
            @endif
                <div class="form-group">
                    <label>
                        QA/QC
                    </label>
                    <select class="select form-control" wire:model.defer="bomContentForm.vmmfg_item_id">
                        <option value="">Select..</option>
                        @foreach($vmmfgItems as $vmmfgItem)
                            <option value="{{$vmmfgItem->id}}" {{isset($bomContentForm->bomItem->vmmfg_item_id) && ($vmmfgItem->id == $bomContentForm->bomItem->vmmfg_item_id) ? 'selected' : ''}}>
                                ({{ $vmmfgItem->vmmfgTitle->sequence }}) - ({{ $vmmfgItem->sequence }}) {{ $vmmfgItem->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('bomContentForm.vmmfg_item_id')
                        <span style="color: red;">
                            <small>
                                {{ $message }}
                            </small>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>
                        Category2
                    </label>
                    <select class="select form-control" wire:model.defer="bomContentForm.bom_sub_category_id">
                        <option value="">Select..</option>
                        @foreach($bomSubCategories as $bomSubCategory)
                            <option value="{{$bomSubCategory->id}}" {{isset($bomContentForm->bom_sub_category_id) && ($bomSubCategory->id == $bomContentForm->bom_sub_category_id) ? 'selected' : ''}}>
                                {{$bomSubCategory->name}}
                            </option>
                        @endforeach
                    </select>
                    @error('bomContentForm.bom_sub_category_id')
                        <span style="color: red;">
                            <small>
                                {{ $message }}
                            </small>
                        </span>
                    @enderror
                </div>
                <x-input type="text" model="bomContentForm.qty">
                    Qty
                </x-input>
        </x-slot>
        <x-slot name="footer">
            @if($bomContentForm->is_edit)
                <button type="submit" class="btn btn-danger btn-xs-block" onclick="return confirm('Are you sure you want to Unbind this?') || event.stopImmediatePropagation()" wire:click.prevent="deletePart">
                    <i class="fas fa-trash"></i>
                    Unbind
                </button>
            @endif
            <button type="submit" class="btn btn-success btn-xs-block" wire:click.prevent="savePart">
                <i class="fas fa-save"></i>
                Save
            </button>
        </x-slot>
    </x-modal>

    <x-modal id="attachment-modal">
        <x-slot name="title">
            Attachments
        </x-slot>
        <x-slot name="content">
            <div class="form-group">
                @if(isset($attachments))
                    @foreach($attachments as $attachmentIndex => $attachment)
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
                                <label for="">
                                    {{$attachment->url}}
                                </label>
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
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </x-slot>
    </x-modal>

    </div>
</div>
