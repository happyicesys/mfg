<div>
    <div>
        <div>
            <x-flash></x-flash>
            <h2>Outgoing</h2>
            <hr>
            @php
                $inventoryMovementsArr = $inventoryMovements->toArray();
                $from = $inventoryMovementsArr['from'];
                $total = $inventoryMovementsArr['total'];
                $profile = \App\Models\Profile::where('is_primary', 1)->first();
            @endphp
            <div class="">
                <div>
                    {{-- @if($showFilters) --}}
                        <div class="bg-light pt-2 pb-2 pl-2 pr-2 mb-2">
                            <div class="form-row">
                                <div class="form-group col-md-3 col-xs-12">
                                    <label>
                                        ID
                                    </label>
                                    <input wire:model="filters.sequence" type="text" class="form-control" placeholder="ID">
                                </div>
                                <div class="form-group col-md-3 col-xs-12">
                                    <label>
                                        Batch
                                    </label>
                                    <input wire:model="filters.batch" type="text" class="form-control" placeholder="Batch">
                                </div>
                                <div class="form-group col-md-3 col-xs-12">
                                    <label>
                                        Status
                                    </label>
                                    <select name="action" wire:model="filters.status" class="form-control">
                                        <option value="">All</option>
                                        <option value="{{array_search('Confirmed', \App\Models\InventoryMovement::STATUSES)}}">
                                            Planned
                                        </option>
                                        <option value="{{array_search('Completed', \App\Models\InventoryMovement::STATUSES)}}">
                                            Completed
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3 col-xs-12">
                                    <label>
                                        Name
                                    </label>
                                    <input wire:model="filters.name" type="text" class="form-control" placeholder="Name">
                                </div>
                                <div class="form-group col-md-3 col-xs-12">
                                    <label>
                                        Type
                                    </label>
                                    <select name="bom_item_type_id" wire:model="filters.bom_item_type_id" class="select form-control">
                                        <option value="">All</option>
                                        @foreach($bomItemTypes as $bomItemType)
                                            <option value="{{$bomItemType->id}}">
                                                {{$bomItemType->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-3 col-xs-12">
                                    <label>
                                        Is Consumable(C) or Cable(CB)?
                                    </label>
                                    <select name="is_consumable" wire:model="filters.is_consumable" class="select form-control">
                                        <option value="">All</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3 col-xs-12">
                                    <label>
                                        Is Inventory?
                                    </label>
                                    <select name="is_inventory" wire:model="filters.is_inventory" class="select form-control">
                                        <option value="">All</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3 col-xs-12">
                                    <label>
                                        Supplier
                                    </label>
                                    <select name="supplier_id" wire:model="filters.supplier_id" class="select form-control">
                                        <option value="">All</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{$supplier->id}}">
                                                {{$supplier->company_name}}
                                            </option>
                                        @endforeach
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
                    <div class="mr-auto pl-1">
                        <button class="btn btn-success" wire:click="createInventoryMovement()" data-toggle="modal" data-target="#inventory-movement-modal">
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
                                Showing {{ count($inventoryMovements) }} of {{$total}}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive pt-3" style="font-size: 14px;">
                <table class="table table-bordered table-hover table-sm">
                    <tr class="table-secondary">
                        <th class="text-center">
                            #
                        </th>
                        <x-th-data model="sequence" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            ID
                        </x-th-data>
                        <x-th-data model="batch" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Batch
                        </x-th-data>
                        <x-th-data model="bom_id" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            BOM
                        </x-th-data>
                        <th class="text-center text-dark">
                            Delivery Dt
                        </th>
                        <th class="text-center text-dark">
                            Code
                        </th>
                        <th class="text-center text-dark">
                            Part Name
                        </th>
                        <th class="text-center text-dark">
                            Type
                        </th>
                        <th class="text-center text-dark">
                            Qty
                        </th>
                        <th></th>
                    </tr>
                    @forelse($inventoryMovements as $index => $inventoryMovement)
                        @if($inventoryMovement->inventoryMovementItems()->exists())
                            @foreach($inventoryMovement->inventoryMovementItems as $inventoryMovementItemIndex => $inventoryMovementItem)
                                <tr class="ml-3">
                                    <td class="text-center">
                                        @if($loop->first)
                                            <b>
                                                {{ $index + $from}}
                                            </b>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($loop->first)
                                            <a href="#" wire:click="editInventoryMovement({{$inventoryMovement}})" data-toggle="modal" data-target="#inventory-movement-modal">
                                                <b>
                                                    {{ $inventoryMovement->sequence}}
                                                </b>
                                            </a>
                                        @endif
                                    </td>
                                    @php
                                        $bgColor = '';
                                        if($inventoryMovement->status == array_search('Confirmed', \App\Models\InventoryMovement::STATUSES)) {
                                            $bgColor = 'bg-warning';
                                        }else if($inventoryMovement->status == array_search('Completed', \App\Models\InventoryMovement::STATUSES)) {
                                            $bgColor = 'bg-success';
                                        }
                                    @endphp
                                    <td class="text-left @if($loop->first) {{$bgColor}} @endif">
                                        @if($loop->first)
                                            {{ $inventoryMovement->batch }}
                                            <br>
                                            <span class="text-dark">
                                                [{{$inventoryMovement->createdBy ? $inventoryMovement->createdBy->name : ''}} {{\Carbon\Carbon::parse($inventoryMovement->created_at)->format('ymd H:ia')}}]
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-left">
                                        @if($loop->first)
                                            <b>
                                                {{ $inventoryMovement->bom->name }}
                                            </b>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($loop->first)
                                            <b>
                                                {{ $inventoryMovement->delivery_date }}
                                            </b>
                                        @endif
                                    </td>
                                    @php
                                        $bgColor = '';
                                        $bgColorBoolean = false;

                                        if($filters['name']) {
                                            if(str_contains(strtolower($inventoryMovementItem->bomItem->name), strtolower($filters['name']))) {
                                                $bgColorBoolean = true;
                                            }
                                        }

                                        if($filters['bom_item_type_id']) {
                                            if($filters['bom_item_type_id'] == $inventoryMovementItem->bomItem->bomItemType->id) {
                                                $bgColorBoolean = true;
                                            }
                                        }

                                        if($filters['is_consumable'] != '') {
                                            if($filters['is_consumable']) {
                                                if($inventoryMovementItem->bomItem->bomItemType->name == 'C' or $inventoryMovementItem->bomItem->bomItemType->name == 'CB') {
                                                    $bgColorBoolean = true;
                                                }
                                            }else {
                                                if($inventoryMovementItem->bomItem->bomItemType->name != 'C' or $inventoryMovementItem->bomItem->bomItemType->name != 'CB') {
                                                    $bgColorBoolean = true;
                                                }
                                            }
                                        }

                                        if($filters['is_inventory'] != '') {
                                            if($filters['is_inventory'] == $inventoryMovementItem->bomItem->is_inventory) {
                                                $bgColorBoolean = true;
                                            }
                                        }

                                        if($filters['supplier_id']) {
                                            if($inventoryMovementItem->bomItem->supplierQuotePrices()->where('supplier_id', $filters['supplier_id'])->exists()) {
                                                $bgColorBoolean = true;
                                            }
                                        }

                                        if($bgColorBoolean) {
                                            $bgColor = 'bg-info';
                                        }
                                    @endphp
                                    <td class="text-left {{$bgColor}}">
                                        @if($inventoryMovementItem->bomItem->attachments()->exists())
                                            <a href="#" wire:click.prevent="viewBomItemAttachments({{$inventoryMovementItem->bomItem}})" data-toggle="modal" data-target="#attachment-modal-nonedit">
                                                {{ $inventoryMovementItem->bomItem->code }}
                                            </a>
                                        @else
                                            {{ $inventoryMovementItem->bomItem->code }}
                                        @endif
                                    </td>
                                    <td class="text-left {{$bgColor}}">
                                        {{ $inventoryMovementItem->bomItem->name }}
                                    </td>
                                    <td class="text-left {{$bgColor}}">
                                        {{ $inventoryMovementItem->bomItem->bomItemType->name }}
                                    </td>
                                    <td class="text-right {{$bgColor}}">
                                        {{ $inventoryMovementItem->qty }}
                                    </td>

                                    <td class="text-center {{$bgColor}}">
                                        <div class="btn-group">
                                            @if($inventoryMovement->status > array_search('Pending', \App\Models\InventoryMovement::STATUSES))
                                                @if($inventoryMovement->status != array_search('Completed', \App\Models\InventoryMovement::STATUSES))
                                                    @role('admin')
                                                        <button class="btn btn-sm btn-danger" wire:click.prevent="deleteSingleInventoryMovementItem({{$inventoryMovementItem->id}})" {{$inventoryMovementItem['inventoryMovement']['status'] == array_search('Completed', \App\Models\InventoryMovement::STATUSES) ? 'disabled' : '' }}>
                                                            <i class="fas fa-times-circle"></i>
                                                        </button>
                                                    @endrole
                                                @endif
                                            @else
                                                <button class="btn btn-sm btn-danger" wire:click.prevent="deleteSingleInventoryMovementItem({{$inventoryMovementItemIndex}})">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="row_edit m-b-3" wire:loading.class.delay="opacity-2" wire:key="row-{{$inventoryMovement->id}}" style="background-color: #adcfe6;">
                                <td class="text-center">
                                    <b>
                                        {{ $index + $from}}
                                    </b>
                                </td>
                                <td class="text-left">
                                    <a href="#" class="text-dark" wire:click="editInventoryMovement({{$inventoryMovement}})" data-toggle="modal" data-target="#inventory-movement-modal">
                                        {{ $inventoryMovement->batch }}
                                    </a>
                                </td>
                                <td class="text-center">
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-center">
                                </td>
                                <td class="text-center">
                                    <button type="button" wire:click="editInventoryMovement({{$inventoryMovement}})" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#inventory-movement-modal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        @endif
                    @if(count($inventoryMovement->inventoryMovementItems) > 0)

                        <tr style="max-height: 15px;">
                            <td colspan="18"></td>
                        </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="18" class="text-center"> No Results Found </td>
                    </tr>
                    @endforelse
                </table>
            </div>
            <div>
                {{ $inventoryMovements->links() }}
            </div>

                <x-modal id="inventory-movement-modal">
                    <x-slot name="title">
                        @if(!$inventoryMovementForm->id)
                            Create Outgoing
                        @else
                            @php
                                $statusStr = '';
                                $statusClass = '';

                                $statusStr = \App\Models\InventoryMovement::STATUSES[$inventoryMovementForm->status];
                                switch($inventoryMovementForm->status) {
                                    case array_search('Pending', \App\Models\InventoryMovement::STATUSES):
                                        $statusClass = 'badge badge-secondary';
                                        break;
                                    case array_search('Confirmed', \App\Models\InventoryMovement::STATUSES):
                                        $statusClass = 'badge badge-primary';
                                        break;
                                    case array_search('Completed', \App\Models\InventoryMovement::STATUSES):
                                        $statusClass = 'badge badge-success';
                                        break;
                                    case array_search('Cancelled', \App\Models\InventoryMovement::STATUSES):
                                        $statusClass = 'badge badge-danger';
                                        break;
                                }
                            @endphp
                            <span class="{{$statusClass}}">{{ $statusStr }}</span>  Edit {{\App\Models\InventoryMovement::ACTIONS[$inventoryMovementForm->action]}} - {{$inventoryMovementForm->sequence}} {{ $inventoryMovementForm->batch }} @if($inventoryMovementForm->country) ({{ $inventoryMovementForm->country->currency_name }}) @endif
                        @endif
                    </x-slot>
                    <x-slot name="content">
                        <div class="form-group">
                            <label for="bom_id">
                                BOM
                            </label>
                            <label for="*" class="text-danger">*</label>
                            <select name="bom_id" wire:model="inventoryMovementForm.bom_id" class="select form-control">
                                <option value="">Select..</option>
                                @foreach($boms as $bom)
                                    <option value="{{ $bom->id }}">
                                        {{ $bom->name }} @if($bom->remarks) ({{ $bom->remarks }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @if($errors->has("inventoryMovementForm.bom_id"))<span style="color:red;"><small>{{ $errors->first("inventoryMovementForm.bom_id") }}</small></span>@endif
                        </div>
                        <x-input type="text" model="inventoryMovementForm.batch">
                            Batch
                        </x-input>
                        <div class="form-group">
                            <label>
                                Order Date
                            </label>
                            <div class="input-group">
                                <input type="date" class="form-control" wire:model="inventoryMovementForm.order_date">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateInventoryMovementFormClicked(-1, 'order_date')">
                                        <i class="fas fa-caret-left"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateInventoryMovementFormClicked(1, 'order_date')">
                                        <i class="fas fa-caret-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>
                                Delivery Date
                            </label>
                            <div class="input-group">
                                <input type="date" class="form-control" wire:model="inventoryMovementForm.delivery_date">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateInventoryMovementFormClicked(-1, 'delivery_date')">
                                        <i class="fas fa-caret-left"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateInventoryMovementFormClicked(1, 'delivery_date')">
                                        <i class="fas fa-caret-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="remarks">
                                Remarks
                            </label>
                            <textarea name="remarks" rows="4" wire:model.defer="inventoryMovementForm.remarks" class="form-control"></textarea>
                        </div>
                        @if($this->inventoryMovementForm->bom_id)
                            <hr>
                            <div class="card">
                                <div class="card-header bg-info">
                                    Outgoing by BOM
                                </div>
                                <div class="card-body">
                                @if(isset($bom))
                                    <div class="form-group">
                                        <button wire:click="$toggle('showGenerateByBomArea')" class="btn btn-outline-secondary btn-block">
                                            Generate by BOM
                                            @if($showGenerateByBomArea)
                                                <i class="fas fa-caret-right"></i>
                                            @else
                                                <i class="fas fa-caret-down"></i>
                                            @endif
                                        </button>
                                    </div>
                                        @if($showGenerateByBomArea)
                                        <div class="form-group">
                                            <label for="bom_qty">
                                                How many unit(s)?
                                            </label>
                                            <input type="text" wire:model="inventoryMovementItemForm.bom_qty" class="form-control">
                                        </div>
{{--
                                        <div class="form-group">
                                            <button class="btn btn-success" wire:click.prevent="onGenerateOutgoingClicked()" {{ $inventoryMovementItemForm->bom_qty && is_numeric($inventoryMovementItemForm->bom_qty) && $this->selectBomContent ? '' : 'disabled' }}>
                                                Generate Outgoing(s)
                                            </button>
                                        </div> --}}

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <tr class="d-flex">
                                                    <th class="col-md-1 bg-secondary text-white text-center">
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
                                                        Needed Qty
                                                    </th>
                                                    <th class="col-md-2 bg-secondary text-white text-center">
                                                        Action
                                                    </th>
                                                </tr>
                                            </table>
                                            @if(isset($bomForm->bomHeaders) and $bomForm->bomHeaders()->exists())
                                                @foreach($bomForm->bomHeaders as $bomHeaderIndex => $bomHeader)
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
                                                        <th class="col-md-4 bg-info text-dark">
                                                            {{$bomHeader->bomItem ? $bomHeader->bomItem->name : null}}
                                                        </th>
                                                        <th class="col-md-1 bg-info text-dark text-center">
                                                            {{-- {{$bomHeader->qty }} --}}
                                                        </th>
                                                        <td class="col-md-2 bg-info text-center">
                                                        </td>
                                                    </tr>
                                                    @if($bomHeader->bomContents()->exists())
                                                        @foreach($bomHeader->bomContents->sortBy('sequence', SORT_NATURAL) as $bomContentIndex => $bomContent)
                                                            @php
                                                                $sequenceStyle = '';
                                                                $dotCount = substr_count($bomContent->sequence, '.');
                                                                switch($dotCount) {
                                                                    case 1:
                                                                        $sequenceStyle = 'text-dark';
                                                                        break;
                                                                    case 2:
                                                                        $sequenceStyle = 'pl-3 text-dark';
                                                                        break;
                                                                    case 3:
                                                                        $sequenceStyle = 'pl-5 text-secondary';
                                                                        break;
                                                                    case 4:
                                                                        $sequenceStyle = 'pl-5 text-secondary';
                                                                        break;
                                                                    default:
                                                                        $sequenceStyle = 'text-dark';
                                                                }
                                                            @endphp
                                                            <tr class="d-flex border border-secondary ml-3" wire:key="content-table-{{$bomContentIndex}}">
                                                                {{-- <th>{{$bomContent->id}}</th> --}}
                                                                {{-- <th>@json($selectBomContent)</th> --}}
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
                                                                    @if($bomContent->bomItem->bomContents()->count() > 1)
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
                                                                <th class="col-md-1 {{$bomContent->is_group ? 'bg-info' : 'bg-light'}} text-dark text-center">
                                                                    @php
                                                                        $neededQty = 0;
                                                                        if(!$bomContent->is_group and is_numeric($inventoryMovementItemForm->bom_qty)) {
                                                                            $neededQty = $bomContent->qty * $inventoryMovementItemForm->bom_qty;
                                                                        }
                                                                    @endphp
                                                                        {{$neededQty}}
                                                                </th>
                                                                <td class="col-md-2 {{$bomContent->is_group ? 'bg-info' : 'bg-light'}} text-center">
                                                                    <div class="btn-group">
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

                                    <div class="form-group">
                                        <button wire:click="$toggle('showGenerateLooseArea')" class="btn btn-outline-secondary btn-block">
                                            Loose
                                            @if($showGenerateLooseArea)
                                                <i class="fas fa-caret-right"></i>
                                            @else
                                                <i class="fas fa-caret-down"></i>
                                            @endif
                                        </button>
                                    </div>

                                        @if($showGenerateLooseArea)
                                            <div class="form-group">
                                                <div class="bg-light p-3">
                                                    <div class="form-row">
                                                        <div class="form-group col-md-3 col-xs-12">
                                                            <label for="name">
                                                                Code Filter
                                                            </label>
                                                            <input type="text" wire:model.debounce.500ms="inventoryMovementItemFormFilters.code" class="form-control" placeholder="Code">
                                                        </div>
                                                        <div class="form-group col-md-3 col-xs-12">
                                                            <label for="name">
                                                                Name Filter
                                                            </label>
                                                            <input type="text" wire:model.debounce.500ms="inventoryMovementItemFormFilters.name" class="form-control" placeholder="Name">
                                                        </div>
                                                        <div class="form-group col-md-3 col-xs-12">
                                                            <label for="name">
                                                                Type Filter
                                                            </label>
                                                            <select class="select form-control" wire:model="inventoryMovementItemFormFilters.bom_item_type_id">
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
                                                            <select class="select form-control" wire:model="inventoryMovementItemFormFilters.supplier_id">
                                                                <option value="">All</option>
                                                                @foreach($suppliers as $supplierOption)
                                                                    <option value="{{$supplierOption->id}}">
                                                                        {{ $supplierOption->company_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-12 col-xs-12">
                                                            <label for="bom_item_id">
                                                                Part
                                                            </label>
                                                            <label for="*" class="text-danger">*</label>
                                                            <select wire:model="inventoryMovementItemForm.bom_item_id" class="form-control select">
                                                                <option value="">Select..</option>
                                                                @foreach($bomItems as $bomItem)
                                                                    <option value="{{ $bomItem->id }}">
                                                                        {{ $bomItem->code }} - {{ $bomItem->name }} (Avail Qty: {{ $bomItem->available_qty }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-12 col-xs-12">
                                                            <label for="qty">
                                                                Qty
                                                            </label>
                                                            <label for="*" class="text-danger">*</label>
                                                            <input type="text" wire:model.debounce.500ms="inventoryMovementItemForm.qty" class="form-control" placeholder="Qty">
                                                        </div>
                                                        <div class="form-group">
                                                            <button class="btn btn-success" wire:click.prevent="onGenerateLooseClicked()" {{ $inventoryMovementItemForm->qty && is_numeric($inventoryMovementItemForm->qty) && $inventoryMovementItemForm->bom_item_id ? '' : 'disabled' }}>
                                                                Add Loose
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <tr class="table-primary">
                                                    <th class="text-center text-dark">
                                                        #
                                                    </th>
                                                    <th class="text-center text-dark">
                                                        Part Code
                                                    </th>
                                                    <th class="text-center text-dark">
                                                        Part Name
                                                    </th>
                                                    <th class="text-center text-dark">
                                                        Qty
                                                    </th>
                                                    <th class="text-center text-dark">
                                                        Available Qty
                                                    </th>
                                                    <th class="text-center text-dark">
                                                        Balance Qty
                                                    </th>
                                                    <th class="text-center text-dark">
                                                        Action
                                                    </th>
                                                </tr>
                                                @php
                                                    $indexCount = 0;
                                                @endphp
                                                {{-- @dd($inventoryMovementItems); --}}
                                                @forelse($inventoryMovementItems as $inventoryMovementItemIndex => $inventoryMovementItem)
                                                @php
                                                    if(!isset($inventoryMovementItem['available_qty'])) {
                                                        $inventoryMovementItem['available_qty'] = \App\Models\BomItem::findOrFail($inventoryMovementItem['bom_item_id'])->available_qty;
                                                        $inventoryMovementItem['balance_qty'] = $inventoryMovementItem['available_qty'] - $inventoryMovementItem['qty'];
                                                    }
                                                    if($inventoryMovementItem['balance_qty'] < 0) {
                                                        $negativeAvailableQtyAlert = true;
                                                    }
                                                @endphp
                                                <tr wire:key="{{ $inventoryMovementItemIndex }}">
                                                    <td class="text-center">
                                                        {{ ++$indexCount }}
                                                    </td>
                                                    <td class="text-left">
                                                        {{ $inventoryMovementItem['bom_item_code'] }}
                                                    </td>
                                                    <td class="text-left">
                                                        {{ $inventoryMovementItem['bom_item_name'] }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ $inventoryMovementItem['qty'] }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ $inventoryMovementItem['available_qty'] }}
                                                    </td>
                                                    <td class="text-right {{ $inventoryMovementItem['balance_qty'] < 0 ? 'text-danger' : 'text-dark'}}">
                                                        {{ $inventoryMovementItem['balance_qty'] }}
                                                    </td>
                                                    <td class="text-center">
                                                        @if(isset($inventoryMovementItem['id']))
                                                            @if(isset($inventoryMovementItem['attachments']) and count($inventoryMovementItem['attachments']) > 0)
                                                                <button type="button" class="btn btn-outline-dark btn-sm" wire:click="viewInventoryItemAttachments({{$inventoryMovementItem['id']}})" wire:key="inventory-movement-item-attachment-{{$inventoryMovementItem['id']}}" data-toggle="modal" data-target="#attachment-modal">
                                                                    <i class="far fa-images"></i>
                                                                </button>
                                                            @endif
                                                            @php
                                                                $editSingleInventoryMovementItemDisabled = false;
                                                                if($inventoryMovementItem['inventoryMovement']['status'] == array_search('Completed', \App\Models\InventoryMovement::STATUSES)) {
                                                                    $editSingleInventoryMovementItemDisabled = true;
                                                                }
                                                                if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('superadmin')) {
                                                                    $editSingleInventoryMovementItemDisabled = false;
                                                                }
                                                            @endphp
                                                            <button class="btn btn-sm btn-outline-secondary" wire:click.prevent="editSingleInventoryMovementItem({{$inventoryMovementItem['id']}})" data-toggle="modal" data-target="#inventory-movement-edit-modal" {{$editSingleInventoryMovementItemDisabled ? 'disabled' : '' }}>
                                                                <i class="far fa-edit"></i>
                                                            </button>
                                                            @role('admin')
                                                                <button class="btn btn-sm btn-danger" onclick="confirm('Are you sure you want to delete this outgoing item?') || event.stopImmediatePropagation()" wire:click.prevent="deleteSingleInventoryMovementItem({{$inventoryMovementItem['id']}})" {{$editSingleInventoryMovementItemDisabled ? 'disabled' : '' }}>
                                                                    <i class="fas fa-times-circle"></i>
                                                                </button>
                                                            @endrole
                                                        @else
                                                            <button class="btn btn-sm btn-danger" wire:click.prevent="deleteSingleInventoryMovementItemIndex({{$inventoryMovementItemIndex}})">
                                                                <i class="fas fa-times-circle"></i>
                                                            </button>
                                                        @endif
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
                                </div>
                            </div>
                        @endif
                    </x-slot>
                    <x-slot name="footer" >
                        <div class="btn-group">
                            @if($showGenerateByBomArea)
                                <button class="btn btn-success" wire:click.prevent="onGenerateOutgoingClicked()" {{ $inventoryMovementItemForm->bom_qty && is_numeric($inventoryMovementItemForm->bom_qty) && $this->selectBomContent ? '' : 'disabled' }}>
                                    Generate Outgoing(s)
                                </button>
                            @endif
                            @if(isset($inventoryMovementForm->id))
                                <a href="#" class="btn btn-xs-block btn-danger" onclick="return confirm('Are you sure you want to delete this outgoing?') || event.stopImmediatePropagation()" wire:click.prevent="deleteInventoryMovement()" >
                                    Delete
                                </a>
                            @endif
                            @if(!isset($inventoryMovementForm->id))
                                <button
                                    type="submit"
                                    class="btn btn-success btn-xs-block"
                                    wire:click.prevent="saveInventoryMovementForm('Confirmed')"
                                    {{count($inventoryMovementItems) > 0 ? '' : 'disabled'}}
                                >
                                    Confirm
                                </button>
                            @endif
                            @if($inventoryMovementForm->status == array_search('Confirmed', \App\Models\InventoryMovement::STATUSES))
                                <button
                                    type="submit"
                                    class="btn btn-success btn-xs-block"
                                    wire:click.prevent="saveInventoryMovementForm('Completed')"
                                >
                                    Completed
                                </button>
                            @endif
                            @if(isset($inventoryMovementForm->id))
                                <button
                                    type="submit"
                                    class="btn btn-outline-secondary"
                                    wire:click.prevent="saveInventoryMovementForm()"
                                >
                                    Save
                                </button>
                                <a href="#" class="btn btn-outline-primary" onclick="return confirm('Are you sure you want to replicate this outgoing?') || event.stopImmediatePropagation()" wire:click.prevent="replicateInventoryMovementForm()" >
                                    Replicate
                                </a>
                            @endif
                        </div>
                    </x-slot>
                </x-modal>
                <x-modal id="inventory-movement-edit-modal">
                    <x-slot name="title">
                        Edit Order
                    </x-slot>
                    <x-slot name="content">
                        <div class="form-group">
                            <label for="bom_item_id">
                                Part
                            </label>
                            <label for="*" class="text-danger">*</label>
                            <select wire:model="inventoryMovementItemForm.bom_item_id" class="form-control select" disabled>
                                <option value="">Select..</option>
                                @foreach($bomItems as $bomItem)
                                    <option value="{{ $bomItem->id }}">
                                        {{ $bomItem->code }} - {{ $bomItem->name }} (Avail Qty: {{ $bomItem->available_qty }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group @if($this->inventoryMovementItemForm->bom_item_id and isset($supplier) and $supplier->id) col-md-4 col-xs-12 @else col-md-12 col-xs-12 @endif ">
                                <label>
                                    Qty
                                </label>
                                <label for="*" class="text-danger">*</label>
                                <input wire:model="inventoryMovementItemForm.qty" type="text" class="form-control" placeholder="Qty">
                            </div>
                        </div>
                    </x-slot>
                    <x-slot name="footer">
                        @role('admin')
                            <button class="btn btn-success" wire:click="updateSingleInventoryMovementItem()" {{$inventoryMovementItemForm->bom_item_id && $inventoryMovementItemForm->qty ? '' : 'disabled' }}>
                                <i class="fas fa-check"></i>
                                Edit
                            </button>
                        @endrole
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
                                {{-- @dd($attachments, $attachment); --}}
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
                    </x-slot>
                    <x-slot name="footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </x-slot>
                </x-modal>


        </div>
    </div>

</div>