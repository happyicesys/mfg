<div>
    <div>
        <div>
            <x-flash></x-flash>
            <h2>Movement</h2>
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
                                <div class="form-group col-md-4 col-xs-12">
                                    <label>
                                        Batch
                                    </label>
                                    <input wire:model="filters.batch" type="text" class="form-control" placeholder="Batch">
                                </div>
                                <div class="form-group col-md-4 col-xs-12">
                                    <label>
                                        Action
                                    </label>
                                    <select name="action" wire:model="filters.action" class="form-control">
                                        <option value="">All</option>
                                        @foreach(\App\Models\InventoryMovement::ACTIONS as $actionIndex => $action)
                                            <option value="{{ $actionIndex }}">
                                                {{ $action }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4 col-xs-12">
                                    <label>
                                        Status
                                    </label>
                                    <select name="action" wire:model="filters.status" class="form-control">
                                        <option value="">All</option>
                                        @foreach(\App\Models\InventoryMovement::STATUSES as $statusIndex => $status)
                                            <option value="{{ $statusIndex }}">
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4 col-xs-12">
                                    <label>
                                        Created At
                                    </label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" wire:model="filters.created_at">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateClicked(-1, 'created_at')">
                                                <i class="fas fa-caret-left"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateClicked(1, 'created_at')">
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
                <table class="table table-bordered table-hover">
                    <tr class="table-secondary">
                        {{-- <th class="text-center">
                            <input type="checkbox" name="" id="">
                        </th> --}}
                        <th class="text-center">
                            #
                        </th>
                        <x-th-data model="action" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Action
                        </x-th-data>
                        <x-th-data model="batch" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Batch
                        </x-th-data>
                        <x-th-data model="order_date" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Order Date
                        </x-th-data>
                        <x-th-data model="country_id" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Currency
                        </x-th-data>
                        <x-th-data model="remarks" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Remarks
                        </x-th-data>
                        <x-th-data model="status" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Status
                        </x-th-data>
                        <x-th-data model="total_amount" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Total Amount
                        </x-th-data>
                        <x-th-data model="created_by" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Created By
                        </x-th-data>
                        <x-th-data model="created_at" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Created At
                        </x-th-data>
                        <x-th-data model="updated_by" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Updated By
                        </x-th-data>
                        <x-th-data model="updated_at" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Updated At
                        </x-th-data>
                        <th></th>
                    </tr>
                    @forelse($inventoryMovements as $index => $inventoryMovement)
                    <tr class="row_edit" wire:loading.class.delay="opacity-2" wire:key="row-{{$inventoryMovement->id}}">
                        {{-- <th class="text-center">
                            <input type="checkbox" wire:model="selected" value="{{$admin->id}}">
                        </th> --}}
                        <td class="text-center">
                            {{ $index + $from}}
                        </td>
                        <td class="text-center">
                            {{ $inventoryMovement->action ?
                                \App\Models\InventoryMovement::ACTIONS[$inventoryMovement->action] :
                                null
                            }}
                        </td>
                        <td class="text-center">
                            {{ $inventoryMovement->batch }}
                        </td>
                        <td class="text-center">
                            {{ $inventoryMovement->order_date ? \Carbon\Carbon::parse($inventoryMovement->order_date)->format('Y-m-d') : null }}
                            {{-- {{ \Carbon\Carbon::parse($inventoryMovement->order_date)->toDateString() }} --}}
                        </td>
                        <td class="text-center">
                            {{ $inventoryMovement->country ? $inventoryMovement->country->currency_name : null }}
                        </td>
                        <td class="text-left">
                            {{ $inventoryMovement->remarks }}
                        </td>
                        <td class="text-center">
                            {{ $inventoryMovement->status ?
                                \App\Models\InventoryMovement::STATUSES[$inventoryMovement->status] :
                                null
                            }}
                        </td>
                        <td class="text-right">
                            {{ $inventoryMovement->total_amount }}
                        </td>
                        <td class="text-center">
                            {{ $inventoryMovement->createdBy ? $inventoryMovement->createdBy->name : null }}
                        </td>
                        <td class="text-center">
                            {{ $inventoryMovement->created_at ? \Carbon\Carbon::parse($inventoryMovement->created_at)->format('Y-m-d h:ia') : null }}
                        </td>
                        <td class="text-center">
                            {{ $inventoryMovement->updatedBy ? $inventoryMovement->updatedBy->name : null }}
                        </td>
                        <td class="text-center">
                            {{ $inventoryMovement->updated_at ? \Carbon\Carbon::parse($inventoryMovement->updated_at)->format('Y-m-d h:ia') : null }}
                        </td>
                        <td class="text-center">
                            <button type="button" wire:click="editInventoryMovement({{$inventoryMovement}})" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#inventory-movement-modal">
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
                {{ $inventoryMovements->links() }}
            </div>

                <x-modal id="inventory-movement-modal">
                    <x-slot name="title">
                        @if(!$inventoryMovementForm->id)
                            Create Movement
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
                            <span class="{{$statusClass}}">{{ $statusStr }}</span>  Edit {{\App\Models\InventoryMovement::ACTIONS[$inventoryMovementForm->action]}} - {{ $inventoryMovementForm->batch }} @if($inventoryMovementForm->country) ({{ $inventoryMovementForm->country->currency_name }}) @endif
                        @endif
                    </x-slot>
                    <x-slot name="content">
                        @if(!$inventoryMovementForm->id)
                            <div class="form-group">
                                <label for="action">
                                    Action
                                </label>
                                <select name="action" wire:model="inventoryMovementForm.action" class="select form-control">
                                    <option value="">Select..</option>
                                    @foreach(\App\Models\InventoryMovement::ACTIONS as $actionIndex => $action)
                                        <option value="{{$actionIndex}}">
                                            {{ $action }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has("inventoryMovementForm.action"))<span style="color:red;"><small>{{ $errors->first("inventoryMovementForm.action") }}</small></span>@endif
                            </div>
                            @if($inventoryMovementForm->action == array_search('Receiving', \App\Models\InventoryMovement::ACTIONS))
                                <div class="form-group">
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
                                </div>
                            @endif
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
                        @if($this->inventoryMovementForm->action and $this->inventoryMovementForm->action == 1 and $this->inventoryMovementForm->country_id)
                            <hr>
                            @if(!$inventoryMovementForm->id or $inventoryMovementForm->status == 1)
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
                                        <select wire:model="inventoryMovementItemForm.bom_item_id" class="form-control select">
                                            <option value="">Select..</option>
                                            @foreach($bomItems as $bomItem)
                                                <option value="{{ $bomItem->id }}">
                                                    {{ $bomItem->code }} - {{ $bomItem->name }} (Avail Qty: {{ $bomItem->available_qty }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if($inventoryMovementItemForm->bom_item_id and $supplier->id)
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
                                            <div class="form-group @if($this->inventoryMovementItemForm->bom_item_id and $supplier->id) col-md-4 col-xs-12 @else col-md-12 col-xs-12 @endif ">
                                                <label>
                                                    Qty
                                                </label>
                                                <label for="*" class="text-danger">*</label>
                                                <input wire:model="inventoryMovementItemForm.qty" wire:change="calculateAmount()" type="text" class="form-control" placeholder="Qty">
                                            </div>
                                            @if($this->inventoryMovementItemForm->bom_item_id and $supplier->id)
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
                                            <textarea wire:model="inventoryMovementItemForm.remarks" class="form-control" name="remarks" rows="3"></textarea>
                                        </div>
                                    <button class="btn btn-success" wire:click="addInventoryMovementItem()" {{$inventoryMovementItemForm->bom_item_id && $inventoryMovementItemForm->qty ? '' : 'disabled' }}>
                                        <i class="fas fa-plus-circle"></i>
                                        Add
                                    </button>
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
{{--
                                        @if($inventoryMovementForm->status > 1)
                                        <th class="text-center text-dark">
                                            Remarks
                                        </th>
                                        @endif --}}
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
                                            {{ \Carbon\Carbon::parse($inventoryMovementItem['date'])->format('Y-m-d') }}
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
                                            {{-- {{ $inventoryMovementItem['status'] }} --}}
                                            {{ $inventoryMovementItem['status'] ? \App\Models\InventoryMovementItem::RECEIVING_STATUSES[$inventoryMovementItem['status']] : null }}
                                        </td>
                                        @endif
{{--
                                        @if($inventoryMovementForm->status > 1)
                                        <td class="text-left">
                                            <input type="text" wire:model.debounce.200ms="inventoryMovementItem.remarks" wire:change="changeInventoryMovementItemRemarks({{$inventoryMovementItemIndex}})" class="form-control">
                                        </td>
                                        @endif --}}
                                        <td class="text-center">
                                            <div class="btn-group">
                                                @if(isset($inventoryMovementItem['id']))
                                                    <button class="btn btn-sm btn-outline-secondary" wire:click.prevent="editSingleInventoryMovementItem({{$inventoryMovementItem['id']}})" data-toggle="modal" data-target="#inventory-movement-item-modal">
                                                        <i class="far fa-edit"></i>
                                                    </button>
                                                @endif
                                                @if($inventoryMovementForm->status > 1)
                                                    @role('admin|superadmin')
                                                    @if($inventoryMovementItem['status'] != array_search('Received', \App\Models\InventoryMovementItem::RECEIVING_STATUSES))
                                                        <button class="btn btn-sm btn-success" wire:click.prevent="receivedSingleInventoryMovementItem({{$inventoryMovementItem['id']}})">
                                                            <i class="fas fa-check-square"></i>
                                                        </button>
                                                    @endif
                                                    @endrole
                                                    @if($inventoryMovementItem['status'] != array_search('Void', \App\Models\InventoryMovementItem::RECEIVING_STATUSES))
                                                        <button class="btn btn-sm btn-danger" wire:click.prevent="voidSingleInventoryMovementItem({{$inventoryMovementItem['id']}})">
                                                            <i class="fas fa-minus-circle"></i>
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
                                    @empty
                                    <tr>
                                        <td colspan="18" class="text-center"> No Results Found </td>
                                    </tr>
                                    @endforelse
                                    @if(count($inventoryMovementItems) > 0)
                                        <td colspan="7" class="text-center">
                                            Total
                                        </td>
                                        <td class="text-right">
                                            {{ number_format($inventoryMovementForm->total_amount, 2, '.', ',') }}
                                        </td>
                                    @endif
                                </table>
                            </div>
                        @elseif($this->inventoryMovementForm->action and $this->inventoryMovementForm->action == 2 and $this->inventoryMovementForm->bom_id)
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
                                        <div class="form-group">
                                            <button class="btn btn-success" wire:click.prevent="onGenerateOutgoingClicked()" {{ $inventoryMovementItemForm->bom_qty && is_numeric($inventoryMovementItemForm->bom_qty) ? '' : 'disabled' }}>
                                                Generate Outgoing(s)
                                            </button>
                                        </div>

                                        <div class="table-responsive">
                                            @if($bom->bomHeaders()->exists())
                                                @foreach($bom->bomHeaders as $bomHeaderIndex => $bomHeader)
                                                <table class="table table-borderless table-sm" wire:key="header-table-{{$bomHeaderIndex}}">
                                                    <tr class="d-flex">
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
                                                    <tr class="d-flex border border-secondary">
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
                                                        @foreach($bomHeader->bomContents->sortBy('sequence', SORT_NATURAL) as $bomContent)
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
                                                                    default:
                                                                        $sequenceStyle = 'text-dark';
                                                                }
                                                            @endphp
                                                            <tr class="d-flex border border-secondary ml-3">
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
                                                                    @if(!$bomContent->is_group)
                                                                        {{
                                                                            $neededQty = $bomContent->qty * (is_numeric($inventoryMovementItemForm->bom_qty) ? $inventoryMovementItemForm->bom_qty : 0)
                                                                        }}
    {{--
                                                                        @if($bomContent->bomItem->is_inventory)
                                                                            @if(($netQty = $bomContent->bomItem->available_qty - $neededQty) < 0)
                                                                            <span style="color:red;">
                                                                                ({{ $netQty }})
                                                                            </span>
                                                                            @else
                                                                                ({{ $netQty }})
                                                                            @endif
                                                                        @endif --}}
                                                                    @endif
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
                                                        @if($inventoryMovementForm->status > 1)
                                                            {{-- <div class="btn-group">
                                                                @if($inventoryMovementItem['status'] != array_search('Confirmed', \App\Models\InventoryMovement::STATUSES))
                                                                    <button class="btn btn-sm btn-success" wire:click.prevent="receivedSingleInventoryMovementItem({{$inventoryMovementItem['id']}})">
                                                                        <i class="fas fa-check-square"></i>
                                                                    </button>
                                                                @endif
                                                                @if($inventoryMovementItem['status'] != array_search('Cancelled', \App\Models\InventoryMovement::STATUSES))
                                                                    <button class="btn btn-sm btn-danger" wire:click.prevent="voidSingleInventoryMovementItem({{$inventoryMovementItem['id']}})">
                                                                        <i class="fas fa-minus-circle"></i>
                                                                    </button>
                                                                @endif
                                                            </div> --}}
                                                        @else
                                                            <button class="btn btn-sm btn-danger" wire:click.prevent="deleteSingleInventoryMovementItem({{$inventoryMovementItemIndex}})">
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
                            @if(!$inventoryMovementForm->id or $inventoryMovementForm->status == 1)
                                @if($inventoryMovementForm->status == 1)
                                    <button class="btn btn-danger" wire:click.prevent="deleteInventoryMovement()">
                                        <i class="fas fa-trash"></i>
                                        Delete
                                    </button>
                                @endif
                                <button
                                    type="submit"
                                    class="btn btn-outline-secondary"
                                    wire:click.prevent="saveInventoryMovementForm('Pending')"
                                >
                                    Pending
                                </button>
                                <button
                                    type="submit"
                                    class="btn btn-success btn-xs-block"
                                    wire:click.prevent="saveInventoryMovementForm('Confirmed')"
                                    {{$negativeAvailableQtyAlert ? 'disabled' : ''}}
                                >
                                    Confirm
                                </button>
                            @else
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

                <x-modal id="inventory-movement-item-modal">
                    <x-slot name="title">
                        Edit Movement Item
                        @if($editInventoryMovementItemForm)
                            ({{\App\Models\InventoryMovementItem::RECEIVING_STATUSES[$editInventoryMovementItemForm->status]}})
                        @endif
                    </x-slot>
                    <x-slot name="content">
                        <div class="form-group">
                            <label>
                                ETA
                            </label>
                            <div class="input-group">
                                <input type="date" class="form-control" wire:model="editInventoryMovementItemForm.date">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateEditInventoryMovementItemFormClicked(-1, 'date')">
                                        <i class="fas fa-caret-left"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateEditInventoryMovementItemFormClicked(1, 'date')">
                                        <i class="fas fa-caret-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>
                                Remarks
                            </label>
                            <textarea wire:model="editInventoryMovementItemForm.remarks" class="form-control" name="remarks" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="file">
                                Upload File(s)
                            </label>
                            <input type="file" class="form-control-file" wire:model.defer="file">
                        </div>

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
                    </x-slot>
                    <x-slot name="footer">
                        <div class="btn-group">
                            @if($editInventoryMovementItemForm and $editInventoryMovementItemForm->status != array_search('Received', \App\Models\InventoryMovementItem::RECEIVING_STATUSES))
                                <button wire:click.prevent="receivedSingleInventoryMovementItem({{$editInventoryMovementItemForm->id}})" class="btn btn-success" {{ isset($attachments) && count($attachments) > 0 ? '' : 'disabled'}}>
                                    <i class="fas fa-check-square"></i>
                                    Received
                                </button>
                            @endif
                            <button wire:click.prevent="saveEditInventoryMovementItemForm()" class="btn btn-outline-success">
                                <i class="far fa-save"></i>
                                Save
                            </button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </x-slot>
                </x-modal>
        </div>
    </div>

</div>
