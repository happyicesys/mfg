<div>
    <div>
        <div>
            <x-flash></x-flash>
            <h2>Receiving</h2>
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
                                            Incomplete
                                        </option>
                                        <option value="{{array_search('Completed', \App\Models\InventoryMovement::STATUSES)}}">
                                            Completed
                                        </option>
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

                                <div class="form-group col-md-3 col-xs-12">
                                    <label>
                                        Rec Date
                                    </label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" wire:model="filters.date">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateFiltersClicked(-1, 'date')">
                                                <i class="fas fa-caret-left"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateFiltersClicked(1, 'date')">
                                                <i class="fas fa-caret-right"></i>
                                            </button>
                                        </div>
                                    </div>
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
                    {{-- <div class="mr-auto pl-1">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                            Search
                        </button>
                    </div> --}}
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
                        {{-- <th class="text-center">
                            <input type="checkbox" name="" id="">
                        </th> --}}
                        <th class="text-center">
                            #
                        </th>
                        <x-th-data model="batch" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Batch
                        </x-th-data>
                        <x-th-data model="supplier_id" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Supplier
                        </x-th-data>
                        <th class="text-center text-dark">
                            Code
                        </th>
                        <th class="text-center text-dark">
                            Part Name
                        </th>
                        <th class="text-center text-dark">
                            Remarks
                        </th>
                        <th class="text-center text-dark">
                            Qty
                        </th>
                        <th class="text-center text-dark">
                            ETA
                        </th>
                        <th class="text-center text-dark">
                            Rec Qty
                        </th>
                        <th class="text-center text-dark">
                            Rec By & Dt
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
                                    <td class="text-left">
                                        @if($loop->first)
                                            <a href="#" wire:click="editInventoryMovement({{$inventoryMovement}})" data-toggle="modal" data-target="#inventory-movement-modal">
                                                {{ $inventoryMovement->batch }}
                                            </a>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($loop->first)
                                            {{ $inventoryMovement->supplier->company_name }}
                                        @endif
                                    </td>
{{--
                                    <td class="text-center" colspan="2">
                                        ({{ $inventoryMovementItemIndex + 1 }})
                                        @if($inventoryMovementItem->bomItem->attachments()->exists())
                                            <button type="button" class="btn btn-outline-dark btn-sm" wire:click="viewBomItemAttachments({{$inventoryMovementItem->bomItem}})" wire:key="inventory-movement-item-bom-item-attachment-{{$inventoryMovementItem->id}}" data-toggle="modal" data-target="#attachment-modal">
                                                <i class="far fa-images"></i>
                                            </button>
                                        @endif
                                    </td> --}}
                                    <td class="text-left">
                                        @if($inventoryMovementItem->bomItem->attachments()->exists())
                                            <a href="#" wire:click.prevent="viewBomItemAttachments({{$inventoryMovementItem->bomItem}})" data-toggle="modal" data-target="#attachment-modal-nonedit">
                                                {{ $inventoryMovementItem->bomItem->code }}
                                            </a>
                                        @else
                                            {{ $inventoryMovementItem->bomItem->code }}
                                        @endif
                                    </td>
                                    <td class="text-left">
                                        {{ $inventoryMovementItem->bomItem->name }}
                                    </td>
                                    <td class="text-left">
                                        {{ $inventoryMovementItem->remarks }}
                                    </td>
                                    <td class="text-center">
                                        {{ $inventoryMovementItem->qty }}
                                    </td>
                                    <td class="text-center @if(\Carbon\Carbon::createFromFormat('Y-m-d', $inventoryMovementItem->date) < \Carbon\Carbon::today()) text-danger @endif">
                                    {{-- <td class="text-center"> --}}
                                        {{ \Carbon\Carbon::parse($inventoryMovementItem->date)->format('ymd') }}
                                    </td>
                                    <td class="text-center" style="{{($inventoryMovementItem->qty == $inventoryMovementItem->inventoryMovementItemQuantities()->sum('qty')) ? 'background-color: #90eeb0;' : ''}} {{$inventoryMovementItem->is_incomplete_qty ? 'background-color: #83B795;' : ''}}">
                                        {{$inventoryMovementItem->inventoryMovementItemQuantities()->sum('qty') + 0}}
                                    </td>
                                    @if($inventoryMovementItem->inventoryMovementItemQuantities()->exists())
                                        @php
                                            $latestReceived = $inventoryMovementItem->inventoryMovementItemQuantities()->latest()->first();
                                            $highlightDate = false;
                                            if($filters['date']) {
                                                foreach($inventoryMovementItem->inventoryMovementItemQuantities as $inventoryMovementItemQuantity) {
                                                    if(\Carbon\Carbon::parse($inventoryMovementItemQuantity->date)->eq(\Carbon\Carbon::parse($filters['date']))) {
                                                        $highlightDate = true;
                                                    }
                                                }
                                            }

                                        @endphp
                                        <td class="text-center {{$highlightDate ? 'bg-warning' : '' }}">
                                            <b>{{ $latestReceived->createdBy ? $latestReceived->createdBy->name : null }}</b> <br>
                                            {{ $latestReceived->created_at ? \Carbon\Carbon::parse($latestReceived->created_at)->format('ymd h:ia') : null }}
                                        </td>
                                    @else
                                        <td></td>
                                    @endif

                                    <td class="text-center">
                                        <div class="btn-group">
                                            @if($inventoryMovement->status > array_search('Pending', \App\Models\InventoryMovement::STATUSES))
                                                @if($inventoryMovement->status != array_search('Completed', \App\Models\InventoryMovement::STATUSES))
                                                    <button class="btn btn-sm btn-success" wire:click.prevent="editReceiveInventoryMovementItem({{$inventoryMovementItem}})" data-toggle="modal" data-target="#inventory-movement-item-quantity-modal" title="Create Receiving" {{$inventoryMovementItem->status == array_search('Received', \App\Models\InventoryMovementItem::RECEIVING_STATUSES) ? 'disabled' : ''}}>
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>
                                                    @role('admin')
                                                        <button class="btn btn-sm btn-danger" wire:click.prevent="deleteSingleInventoryMovementItem({{$inventoryMovementItem->id}})" {{$inventoryMovementItem['inventoryMovement']['status'] == array_search('Completed', \App\Models\InventoryMovement::STATUSES) ? 'disabled' : '' }}>
                                                            <i class="fas fa-times-circle"></i>
                                                        </button>
                                                    @endrole
                                                @endif
                                                @if($inventoryMovementItem->attachments()->exists())
                                                    <button type="button" class="btn btn-outline-dark btn-sm" wire:click="viewInventoryItemAttachments({{$inventoryMovementItem}})" wire:key="inventory-movement-item-quantity-attachment-{{$inventoryMovementItem->id}}" data-toggle="modal" data-target="#attachment-modal">
                                                        <i class="far fa-images"></i>
                                                    </button>
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
                                {{-- <th class="text-center">
                                    <input type="checkbox" wire:model="selected" value="{{$admin->id}}">
                                </th> --}}
                                <td class="text-center">
                                    <b>
                                        {{ $index + $from}}
                                    </b>
                                </td>
                                <td class="text-left">
                                    <a href="#" wire:click="editInventoryMovement({{$inventoryMovement}})" data-toggle="modal" data-target="#inventory-movement-modal">
                                        {{ $inventoryMovement->batch }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    {{ $inventoryMovement->supplier->company_name }}
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-center">
                                    {{ $inventoryMovement->order_date ? \Carbon\Carbon::parse($inventoryMovement->order_date)->format('ymd') : null }}
                                </td>
                                <td class="text-center">
                                </td>
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
                            Create Receiving
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
                                    case array_search('Partially', \App\Models\InventoryMovement::STATUSES):
                                        $statusClass = 'badge badge-info';
                                        break;
                                    case array_search('Completed', \App\Models\InventoryMovement::STATUSES):
                                        $statusClass = 'badge badge-success';
                                        break;
                                    case array_search('Cancelled', \App\Models\InventoryMovement::STATUSES):
                                        $statusClass = 'badge badge-danger';
                                        break;
                                }
                            @endphp
                            <span class="{{$statusClass}}">{{ $statusStr }}</span>  Edit {{\App\Models\InventoryMovement::ACTIONS[$inventoryMovementForm->action]}} - {{ $inventoryMovementForm->batch }} @if($inventoryMovementForm->country) ({{$supplierForm->company_name}}) ({{ $inventoryMovementForm->country->currency_name }}) @endif
                        @endif
                    </x-slot>
                    <x-slot name="content">
                        @if(!$inventoryMovementForm->id)
                            <div class="form-group">
                                <label for="supplier_id">
                                    Supplier
                                </label>
                                <select name="supplier_id" wire:model="inventoryMovementForm.supplier_id" class="select form-control">
                                    <option value="">Select..</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">
                                            {{ $supplier->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has("inventoryMovementForm.supplier_id"))<span style="color:red;"><small>{{ $errors->first("inventoryMovementForm.supplier_id") }}</small></span>@endif
                            </div>
                            {{-- <div class="form-group">
                                <label for="country_id">
                                    Currency
                                </label>
                                <select name="country_id" wire:model="inventoryMovementForm.country_id" class="select form-control">
                                    <option value="">Select..</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">
                                            {{ $country->currency_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has("inventoryMovementForm.country_id"))<span style="color:red;"><small>{{ $errors->first("inventoryMovementForm.country_id") }}</small></span>@endif
                            </div> --}}
                        @endif
                        @if($inventoryMovementForm->action == array_search('Outgoing', \App\Models\InventoryMovement::ACTIONS))
                            <div class="form-group">
                                <label for="bom_id">
                                    BOM
                                </label>
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
                        @endif
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
                                    <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateinventoryMovementFormClicked(-1, 'order_date')">
                                        <i class="fas fa-caret-left"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateinventoryMovementFormClicked(1, 'order_date')">
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
                        @if($this->inventoryMovementForm->country_id)
                            <hr>
                            <div class="card">
                                <div class="card-header bg-info">
                                    Add Order
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="bom_item_id">
                                            Part
                                        </label>
                                        <label for="*" class="text-danger">*</label>
                                        <div class="bg-light p-3">
                                            <div class="form-row">
                                                <div class="form-group col-md-4 col-xs-12">
                                                    <label for="name">
                                                        Code Filter
                                                    </label>
                                                    <input type="text" wire:model.debounce.500ms="inventoryMovementItemFormFilters.code" class="form-control" placeholder="Code">
                                                </div>
                                                <div class="form-group col-md-4 col-xs-12">
                                                    <label for="name">
                                                        Name Filter
                                                    </label>
                                                    <input type="text" wire:model.debounce.500ms="inventoryMovementItemFormFilters.name" class="form-control" placeholder="Name">
                                                </div>
                                                <div class="form-group col-md-4 col-xs-12">
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
                                            </div>
                                        </div>
                                        <select wire:model="inventoryMovementItemForm.bom_item_id" class="form-control select">
                                            <option value="">Select..</option>
                                            @foreach($bomItems as $bomItem)
                                                <option value="{{ $bomItem->id }}">
                                                    {{ $bomItem->code }} - {{ $bomItem->name }} (Avail Qty: {{ $bomItem->available_qty }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if($inventoryMovementItemForm->bom_item_id and isset($supplierForm) and $supplierForm->id)
                                        <hr>
                                        <div class="form-row">
                                            <div class="form-group col-md-6 col-xs-12">
                                                <label for="supplier_unit_price">
                                                    Quoted Unit Price <br>({{$supplierForm->company_name}})
                                                </label>
                                                <input type="text" wire:model="inventoryMovementItemForm.supplier_unit_price" wire:change="calculateAmount()" class="form-control">
                                            </div>
                                            <div class="form-group col-md-6 col-xs-12">
                                                <label for="supplier_unit_price">
                                                    Latest Currency Rate <br> ({{$supplierForm->transactedCurrency->currency_name}})
                                                </label>
                                                <input type="text" wire:model="inventoryMovementItemForm.rate" class="form-control">
                                            </div>
                                        </div>
                                        <hr>
                                    @endif
                                        <div class="form-row">
                                            <div class="form-group @if($this->inventoryMovementItemForm->bom_item_id and isset($supplierForm) and $supplierForm->id) col-md-4 col-xs-12 @else col-md-12 col-xs-12 @endif ">
                                                <label>
                                                    Qty
                                                </label>
                                                <label for="*" class="text-danger">*</label>
                                                <input wire:model="inventoryMovementItemForm.qty" wire:change="calculateAmount()" type="text" class="form-control" placeholder="Qty">
                                            </div>
                                            @if($this->inventoryMovementItemForm->bom_item_id and isset($supplierForm) and $supplierForm->id)
                                            <div class="form-group col-md-4 col-xs-12">
                                                <label>
                                                    Unit Price
                                                </label>
                                                <input wire:model="inventoryMovementItemForm.unit_price" wire:change="calculateAmount()" type="text" class="form-control" placeholder="Unit Price">
                                            </div>
                                            <div class="form-group col-md-4 col-xs-12">
                                                <label>
                                                    Amount
                                                </label>
                                                <input wire:model="inventoryMovementItemForm.amount" type="text" class="form-control" placeholder="Unit Price" disabled>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                ETA
                                            </label>
                                            <div class="input-group">
                                                <input type="date" class="form-control" wire:model.defer="inventoryMovementItemForm.date">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateinventoryMovementItemFormClicked(-1, 'date')">
                                                        <i class="fas fa-caret-left"></i>
                                                    </button>
                                                    <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateinventoryMovementItemFormClicked(1, 'date')">
                                                        <i class="fas fa-caret-right"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                Remarks
                                            </label>
                                            <textarea wire:model.defer="inventoryMovementItemForm.remarks" class="form-control" name="remarks" rows="3"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="file">
                                                Upload File
                                            </label>
                                            <input type="file" class="form-control-file" wire:model.defer="file">
                                        </div>
                                    <button class="btn btn-success" wire:click="addInventoryMovementItem()" {{$inventoryMovementItemForm->bom_item_id && $inventoryMovementItemForm->qty ? '' : 'disabled' }}>
                                        <i class="fas fa-plus-circle"></i>
                                        Add
                                    </button>
                                </div>
                            </div>

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
                                            ETA
                                        </th>
                                        <th class="text-center text-dark">
                                            Remarks
                                        </th>
                                        <th class="text-center text-dark">
                                            Qty
                                        </th>
                                        <th class="text-center text-dark">
                                            Unit Price
                                        </th>
                                        <th class="text-center text-dark">
                                            Amount
                                        </th>
                                        @if($inventoryMovementForm->id)
                                        <th class="text-center text-dark">
                                            Status
                                        </th>
                                        @endif
                                        <th class="text-center text-dark">
                                            Attachment
                                        </th>
                                        <th class="text-center text-dark">
                                            Action
                                        </th>
                                    </tr>
                                    @forelse($inventoryMovementItems as $inventoryMovementItemIndex => $inventoryMovementItem)
                                        <tr wire:key="inventory-movement-item-{{ $inventoryMovementItemIndex }}">
                                            <td class="text-center">
                                                {{ $inventoryMovementItemIndex + 1 }}
                                            </td>
                                            <td class="text-left">
                                                {{ $inventoryMovementItem['bom_item_code'] }}
                                            </td>
                                            <td class="text-left">
                                                {{ $inventoryMovementItem['bom_item_name'] }}
                                            </td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($inventoryMovementItem['date'])->format('ymd') }}
                                            </td>
                                            <td class="text-left">
                                                {{ $inventoryMovementItem['remarks'] }}
                                            </td>
                                            <td class="text-right">
                                                {{ $inventoryMovementItem['qty'] }}
                                            </td>
                                            <td class="text-right">
                                                {{ number_format($inventoryMovementItem['unit_price'], 2, '.', ',') }}
                                            </td>
                                            <td class="text-right">
                                                {{ number_format($inventoryMovementItem['amount'], 2, '.', ',') }}
                                            </td>
                                            @if($inventoryMovementForm->id)
                                            <td class="text-center">
                                                {{ $inventoryMovementItem['status'] ? \App\Models\InventoryMovementItem::RECEIVING_STATUSES[$inventoryMovementItem['status']] : null }}
                                            </td>
                                            @endif
                                            <td class="text-center">
                                                @php
                                                    $ext = pathinfo($inventoryMovementItem['attachment_url'], PATHINFO_EXTENSION);
                                                @endphp
                                                @if($ext === 'pdf')
                                                    <embed src="{{$inventoryMovementItem['attachment_url']}}" type="application/pdf" class="card-img-top" style="min-height: 500px;">
                                                @elseif($ext === 'mov' or $ext === 'mp4')
                                                    <div class="embed-responsive embed-responsive-16by9">
                                                        <video class=" embed-responsive-item video-js" controls>
                                                            <source src="{{$inventoryMovementItem['attachment_url']}}">
                                                            Your browser does not support the video tag.
                                                        </video>
                                                    </div>
                                                @else
                                                    <img class="card-img-top" src="{{$inventoryMovementItem['attachment_url']}}" alt="">
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
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
                                                            <button class="btn btn-sm btn-danger" onclick="confirm('Are you sure you want to delete this part and its receiving?') || event.stopImmediatePropagation()" wire:click.prevent="deleteSingleInventoryMovementItem({{$inventoryMovementItem['id']}})" {{$editSingleInventoryMovementItemDisabled ? 'disabled' : '' }}>
                                                                <i class="fas fa-times-circle"></i>
                                                            </button>
                                                        @endrole
                                                    @else
                                                        <button class="btn btn-sm btn-danger" wire:click.prevent="deleteSingleInventoryMovementItemIndex({{$inventoryMovementItemIndex}})">
                                                            <i class="fas fa-times-circle"></i>
                                                        </button>
                                                    @endif

                                                </div>
                                            </td>
                                        </tr>
                                        @if(isset($inventoryMovementItem['inventoryMovementItemQuantities']))
                                            @foreach($inventoryMovementItem['inventoryMovementItemQuantities'] as $inventoryMovementItemQuantity)
                                                <tr class="ml-5" style="background-color: #90eeb0;">
                                                    <td class="text-center" colspan="3">
                                                        <b>
                                                            Received
                                                        </b>
                                                        @if(isset($inventoryMovementItemQuantity['createdBy']))
                                                            <b>({{$inventoryMovementItemQuantity['createdBy']['name']}}</b> {{\Carbon\Carbon::parse($inventoryMovementItemQuantity['created_at'])->format('ymd H:ia')}})
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $inventoryMovementItemQuantity['date'] }}
                                                    </td>
                                                    <td class="text-left">
                                                        {{ $inventoryMovementItemQuantity['remarks'] }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $inventoryMovementItemQuantity['qty'] }}
                                                    </td>
                                                    <td colspan="4"></td>
                                                    <td class="text-center" >
                                                        <div class="btn-group">
                                                            @if(isset($inventoryMovementItemQuantity['attachments']) and count($inventoryMovementItemQuantity['attachments']) > 0)
                                                                <button type="button" class="btn btn-outline-dark btn-sm" wire:click="viewQuantityAttachmentsByInventoryMovementItemQuantityId({{$inventoryMovementItemQuantity['id']}})" wire:key="inventory-movement-item-quantity-attachment-{{$inventoryMovementItemQuantity['id']}}" data-toggle="modal" data-target="#attachment-modal">
                                                                    <i class="far fa-images"></i>
                                                                </button>
                                                            @endif
                                                            @php
                                                                $disabled = false;
                                                                if(isset($inventoryMovementItemQuantity['inventoryMovementItem'])) {
                                                                    $status = $inventoryMovementItemQuantity['inventoryMovementItem']['inventoryMovement']['status'];
                                                                    if($status == array_search('Completed', \App\Models\InventoryMovement::STATUSES)) {
                                                                        $disabled = true;
                                                                    }
                                                                }
                                                            @endphp
                                                            <button type="button" class="btn btn-danger btn-sm" onclick="confirm('Are you sure you want to delete this received part?') || event.stopImmediatePropagation()" wire:click="removeQuantityByInventoryMovementItemQuantityId({{$inventoryMovementItemQuantity['id']}})" wire:key="inventory-movement-item-quantity-delete-{{$inventoryMovementItemQuantity['id']}}" {{$disabled ? 'disabled' : ''}}>
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @empty
                                    <tr>
                                        <td colspan="18" class="text-center"> No Results Found </td>
                                    </tr>
                                    @endforelse
                                    @if(count($inventoryMovementItems) > 0)
                                        <td colspan="5" class="text-center">
                                            Total
                                        </td>
                                        <td class="text-right">
                                            {{ $inventoryMovementForm->total_qty }}
                                        </td>
                                        <td></td>
                                        <td class="text-right">
                                            {{ number_format($inventoryMovementForm->total_amount, 2, '.', ',') }}
                                        </td>
                                    @endif
                                </table>
                            </div>
                        @endif
                    </x-slot>
                    <x-slot name="footer" >
                        <div class="btn-group">
                            @if(isset($inventoryMovementForm->id))
                                @role('admin')
                                    <a href="#" class="btn btn-xs-block btn-danger" onclick="return confirm('Are you sure you want to delete this receiving?') || event.stopImmediatePropagation()" wire:click.prevent="deleteInventoryMovement()" >
                                        Delete
                                    </a>
                                @endrole
                            @endif
                            @if(!isset($inventoryMovementForm->id))
                                <button
                                    type="submit"
                                    class="btn btn-success btn-xs-block"
                                    wire:click.prevent="saveInventoryMovementForm('Confirmed')"
                                >
                                    Confirm
                                </button>
                            @endif
{{--
                            @if($inventoryMovementForm->status == array_search('Confirmed', \App\Models\InventoryMovement::STATUSES))
                                <button
                                    type="submit"
                                    class="btn btn-success btn-xs-block"
                                    wire:click.prevent="saveInventoryMovementForm('Completed')"
                                >
                                    Completed
                                </button>
                            @endif --}}
                            @if(isset($inventoryMovementForm->id))
                                <button
                                    type="submit"
                                    class="btn btn-outline-secondary"
                                    wire:click.prevent="saveInventoryMovementForm()"
                                >
                                    Save
                                </button>
                            @endif
                        </div>
                    </x-slot>
                </x-modal>
                <x-modal id="inventory-movement-item-quantity-modal">
                    <x-slot name="title">
                        Create Receiving for
                        @if($inventoryMovementItemForm->bomItem)
                            ({{$inventoryMovementItemForm->bomItem->code}} - {{$inventoryMovementItemForm->bomItem->name}})
                        @endif
                    </x-slot>
                    <x-slot name="content">
                        <div>
                            <div class="form-group">
                                <label>
                                    Received Date
                                </label>
                                <label for="*" class="text-danger">*</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" wire:model="inventoryMovementItemQuantityForm.date">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" wire:click.prevent="onInventoryMovementItemQuantityFormPrevNextDateClicked(-1, 'date')">
                                            <i class="fas fa-caret-left"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" wire:click.prevent="onInventoryMovementItemQuantityFormPrevNextDateClicked(1, 'date')">
                                            <i class="fas fa-caret-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @php
                                $neededQty = 0;
                                $totalReceivedQty = 0;

                                $neededQty = $inventoryMovementItemForm->qty;
                                if($inventoryMovementItemForm->inventoryMovementItemQuantities()->exists()) {
                                    foreach($inventoryMovementItemForm->inventoryMovementItemQuantities as $inventoryMovementItemQuantity) {
                                        $totalReceivedQty += $inventoryMovementItemQuantity->qty;
                                    }
                                }
                            @endphp
                            <div class="form-group">
                                <label>
                                    Qty
                                    (Needed: {{$neededQty}} ; Received: {{$totalReceivedQty}})
                                </label>
                                <label for="*" class="text-danger">*</label>
                                <input wire:model.debounce.300ms="inventoryMovementItemQuantityForm.qty" type="text" class="form-control" placeholder="Qty">
                                @if($totalReceivedQty + $inventoryMovementItemQuantityForm->qty < $neededQty)
                                    <div class="form-check form-check-inline mt-2">
                                        <input class="form-check-input" type="checkbox" name="is_incomplete_qty" wire:model="inventoryMovementItemQuantityForm.is_incomplete_qty">
                                        <label class="form-check-label" for="is_incomplete_qty">Incomplete Receiving (Mark as complete)</label>
                                    </div>
                                @endif
                                @if($inventoryMovementItemQuantityForm->qty + $totalReceivedQty > $neededQty)
                                <span style="color: red;">
                                    Total receive qty shouldnt exceed order qty
                                </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>
                                    Remarks
                                </label>
                                <textarea wire:model="inventoryMovementItemQuantityForm.remarks" class="form-control" name="remarks" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="file">
                                    Upload File(s)
                                </label>
                                <input type="file" class="form-control-file" wire:model.defer="file">
                            </div>
                        </div>
                    </x-slot>
                    <x-slot name="footer">
                        <div class="btn-group">
                            <button wire:click.prevent="saveInventoryMovementItemQuantityForm()" class="btn btn-outline-success" {{$file && $inventoryMovementItemQuantityForm->qty && ($inventoryMovementItemQuantityForm->qty + $totalReceivedQty <= $neededQty) ? '' : 'disabled'}}>
                                <i class="far fa-save"></i>
                                Received
                            </button>
                            @role('admin|superadmin')
                                <button wire:click.prevent="saveInventoryMovementItemQuantityForm()" class="btn btn-outline-success" {{$inventoryMovementItemQuantityForm->qty && ($inventoryMovementItemQuantityForm->qty + $totalReceivedQty <= $neededQty) ? '' : 'disabled'}}>
                                    <i class="far fa-save"></i>
                                    Received wo attachment
                                </button>
                            @endrole
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Close
                            </button>
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
                        @if($inventoryMovementItemForm->bom_item_id and isset($supplier) and $supplier->id)
                            <hr>
                            <div class="form-row">
                                <div class="form-group col-md-6 col-xs-12">
                                    <label for="supplier_unit_price">
                                        Quoted Unit Price <br>({{$supplier->company_name}})
                                    </label>
                                    <input type="text" wire:model="inventoryMovementItemForm.supplier_unit_price" wire:change="calculateAmount()" class="form-control">
                                </div>
                                <div class="form-group col-md-6 col-xs-12">
                                    <label for="supplier_unit_price">
                                        Latest Currency Rate <br> ({{$supplier->transactedCurrency->currency_name}})
                                    </label>
                                    <input type="text" wire:model="inventoryMovementItemForm.rate" class="form-control">
                                </div>
                            </div>
                            <hr>
                        @endif
                            <div class="form-row">
                                <div class="form-group @if($this->inventoryMovementItemForm->bom_item_id and isset($supplier) and $supplier->id) col-md-4 col-xs-12 @else col-md-12 col-xs-12 @endif ">
                                    <label>
                                        Qty
                                    </label>
                                    <label for="*" class="text-danger">*</label>
                                    <input wire:model="inventoryMovementItemForm.qty" wire:change="calculateAmount()" type="text" class="form-control" placeholder="Qty">
                                </div>
                                @if($this->inventoryMovementItemForm->bom_item_id and isset($supplier) and $supplier->id)
                                <div class="form-group col-md-4 col-xs-12">
                                    <label>
                                        Unit Price
                                    </label>
                                    <input wire:model="inventoryMovementItemForm.unit_price" wire:change="calculateAmount()" type="text" class="form-control" placeholder="Unit Price">
                                </div>
                                <div class="form-group col-md-4 col-xs-12">
                                    <label>
                                        Amount
                                    </label>
                                    <input wire:model="inventoryMovementItemForm.amount" type="text" class="form-control" placeholder="Unit Price" disabled>
                                </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>
                                    ETA
                                </label>
                                <div class="input-group">
                                    <input type="date" class="form-control" wire:model="inventoryMovementItemForm.date">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateinventoryMovementItemFormClicked(-1, 'date')">
                                            <i class="fas fa-caret-left"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateinventoryMovementItemFormClicked(1, 'date')">
                                            <i class="fas fa-caret-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>
                                    Remarks
                                </label>
                                <textarea wire:model.defer="inventoryMovementItemForm.remarks" class="form-control" name="remarks" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="file">
                                    Upload File(s)
                                </label>
                                <input type="file" class="form-control-file" wire:model.defer="file">
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
                <x-modal id="attachment-modal-nonedit">
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
                                            </div>
                                            <div class="d-block d-sm-none">
                                                <button type="button" class="btn btn-block btn-warning" wire:click="downloadAttachment({{$attachment}})">
                                                    <i class="fas fa-cloud-download-alt"></i>
                                                    Download
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