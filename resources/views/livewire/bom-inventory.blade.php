<div>
    <div>
        <div>
            <x-flash></x-flash>
            <h2>Inventory</h2>
            <hr>
            @php
                $bomItemsArr = $bomItems->toArray();
                $from = isset($bomItemsArr['from']) ? $bomItemsArr['from'] : 1;
                $total = isset($bomItemsArr['total']) ? $bomItemsArr['total'] : count($bomItems);

                $profile = \App\Models\Profile::where('is_primary', 1)->first();
            @endphp
            <div class="">
                <div>
                    <div class="bg-light pt-2 pb-2 pl-2 pr-2 mb-2">
                        <div class="form-row">
                            <div class="form-group col-md-3 col-xs-12">
                                <label>
                                    Code
                                </label>
                                <input wire:model="filters.code" type="text" class="form-control" placeholder="Code">
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
                            <div class="form-group col-md-3 col-xs-12">
                                <label>
                                    Qty Status
                                </label>
                                <select name="qty_status" wire:model="filters.qty_status" class="select form-control">
                                    <option value="">All</option>
                                    <option value="1">Planned Greater than Available</option>
                                </select>
                                <span class="text-danger">
                                    <small>
                                    * Override filter
                                    </small>
                                </span>
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

                    <div class="mr-auto pl-1">
                        <button class="btn btn-primary" wire:click.prevent="$toggle('showPlannerArea')">
                            Planner
                            @if($showPlannerArea)
                                <i class="fas fa-caret-right"></i>
                            @else
                                <i class="fas fa-sort-down"></i>
                            @endif
                        </button>
                    </div>

                    <div class="ml-auto">
                        <div class="form-inline">
                            <label for="display_num">Display </label>
                            <select wire:model="itemPerPage" class="form-control form-control-sm ml-1 mr-1" name="pageNum">
                                <option value="100">100</option>
                                <option value="200">200</option>
                                <option value="500">500</option>
                                <option value="All">All</option>
                            </select>
                            <label for="display_num2" style="padding-right: 20px"> per Page</label>
                        </div>
                        <div>
                            <label style="padding-right:18px; font-weight: bold;">
                                Showing {{ count($bomItems) }} of {{$total}}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            @if($showPlannerArea)
            <div class="bg-light pt-2 pb-2 pl-2 pr-2 mb-2">
                <div class="form-row">
                    <div class="form-group col-md-4 col-xs-12">
                        <label>
                            BOM
                        </label>
                        <select name="bom_id" wire:model="planner.bom_id" class="select form-control">
                            <option value="">Select..</option>
                            @foreach($boms as $bom)
                                <option value="{{$bom->id}}">
                                    {{$bom->name}}
                                    @if($bom->remarks)
                                        ({{$bom->remarks}})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4 col-xs-12">
                        <label>
                            Outgoing Qty
                        </label>
                        <input wire:model="planner.qty" type="text" class="form-control" placeholder="Outgoing Qty">
                    </div>
                </div>
            </div>
            @endif

            <div class="table-responsive pt-3" style="font-size: 14px;">
                <table class="table table-bordered table-hover table-sm">
                    <tr class="table-secondary">
                        {{-- <th class="text-center">
                            <input type="checkbox" name="" id="">
                        </th> --}}
                        <th class="text-center text-dark">
                            #
                        </th>
                        <x-th-data model="code" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Code
                        </x-th-data>
                        <x-th-data model="name" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Name
                        </x-th-data>
                        <x-th-data model="bom_item_type_id" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Type
                        </x-th-data>
                        <x-th-data model="ordered_qty" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Ordered Qty
                        </x-th-data>
                        <x-th-data model="available_qty" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Available Qty
                        </x-th-data>
                        <x-th-data model="planned_qty" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Plan Outgoing Qty
                        </th>
                        <th class="text-center text-dark">
                            Out Qty
                        </x-th-data>
                        <th class="text-center text-dark">
                            Supplier
                        </th>
                        <th class="text-center text-dark">
                            Unit Price
                        </th>
                        <th class="text-center text-dark">
                            Base Price @if($profile->country) ({{$profile->country->currency_name}}) @endif
                        </th>
                        <th class="text-center text-dark">
                            Parent
                        </th>
                        <th class="text-center text-dark">
                            Action
                        </th>
                        @if($showPlannerArea)
                        <th class="text-center text-dark">
                            Planner Qty
                        </th>
                        @endif
                    </tr>
                    @forelse($bomItems as $index => $bomItem)

                        @php
                            $orderedQty = $bomItem
                                            ->inventoryMovementItems()
                                            ->where('status', array_search('Ordered', \App\Models\InventoryMovementItem::RECEIVING_STATUSES))
                                            ->whereHas('inventoryMovement', function($query) {
                                                $query->where('action', array_search('Receiving', \App\Models\InventoryMovement::ACTIONS));
                                            })->latest()
                                            ->limit(5)
                                            ->get();

                            $plannedQty = $bomItem
                                            ->inventoryMovementItems()
                                            ->where('status', array_search('Planned', \App\Models\InventoryMovementItem::OUTGOING_STATUSES))
                                            ->whereHas('inventoryMovement', function($query) {
                                                $query->where('action', array_search('Outgoing', \App\Models\InventoryMovement::ACTIONS));
                                            })->latest()
                                            ->limit(5)
                                            ->get();


                            // $inQty = InventoryMovementItemQuantity::whereHas('inventoryMovementItem.bomItem', function($query) use ($bomItem) {
                            //                 $query->where('id', $bomItem->id);
                            //             })->latest()
                            //             ->limit(3)
                            //             ->get();

                            $inQtyQuery = \App\Models\InventoryMovementItemQuantity::query()
                                            ->leftJoin('inventory_movement_items', 'inventory_movement_items.id', '=', 'inventory_movement_item_quantities.inventory_movement_item_id')
                                            ->leftJoin('bom_items', 'bom_items.id', '=', 'inventory_movement_items.bom_item_id')
                                            ->leftJoin('inventory_movements', 'inventory_movements.id', '=', 'inventory_movement_items.inventory_movement_id')
                                            ->select('inventory_movement_item_quantities.date', 'inventory_movement_item_quantities.qty', 'inventory_movements.action', 'bom_items.id AS bom_item_id')
                                            ->where('bom_items.id', $bomItem->id)
                                            ->latest('inventory_movement_item_quantities.date');

                            $outQtyQuery = \App\Models\InventoryMovementItem::query()
                                            ->leftjoin('inventory_movements', 'inventory_movements.id', '=', 'inventory_movement_items.inventory_movement_id')
                                            ->select('inventory_movement_items.date', 'inventory_movement_items.qty', 'inventory_movements.action', 'inventory_movement_items.bom_item_id AS bom_item_id')
                                            ->where('inventory_movement_items.status', array_search('Delivered', \App\Models\InventoryMovementItem::OUTGOING_STATUSES))
                                            ->whereHas('inventoryMovement', function($query) {
                                                $query->where('action', array_search('Outgoing', \App\Models\InventoryMovement::ACTIONS));
                                            })
                                            ->where('inventory_movement_items.bom_item_id', $bomItem->id)
                                            ->latest('inventory_movement_items.date');

                            $qtyArr = $inQtyQuery->union($outQtyQuery)->latest('date')->limit(3)->get();

                            // dd($qtyArr->toArray());

                            $outQty = $bomItem
                                            ->inventoryMovementItems()
                                            ->where('status', array_search('Delivered', \App\Models\InventoryMovementItem::OUTGOING_STATUSES))
                                            ->whereHas('inventoryMovement', function($query) {
                                                $query->where('action', array_search('Outgoing', \App\Models\InventoryMovement::ACTIONS));
                                            })->latest()
                                            ->limit(3)
                                            ->get();

                            $planOutgoingQty = 0;
                            if($planner['bom_id'] and $planner['qty']) {
                                $bomItemQty = $bomItem
                                                ->bomContents()
                                                ->whereHas('bomHeader', function($query) use ($planner) {
                                                    $query->where('bom_id', $planner['bom_id']);
                                                })
                                                ->sum('qty');
                                $planOutgoingQty = $bomItemQty * $planner['qty'];
                            }
                        @endphp

                        <tr class="row_edit" wire:loading.class.delay="opacity-2" wire:key="row-{{$bomItem->id}}">
                            {{-- <th class="text-center">
                                <input type="checkbox" wire:model="selected" value="{{$admin->id}}">
                            </th> --}}
                            <td class="text-center">
                                {{ $index + $from}}
                            </td>
                            <td class="text-left">
                                {{ $bomItem->code }}
                            </td>
                            <td class="text-left">
                                {{ $bomItem->name }}
                            </td>
                            <td class="text-center">
                                {{ $bomItem->bomItemType ? $bomItem->bomItemType->name : '' }}
                            </td>
                            <td class="text-center">
                                <b>{{ $bomItem->ordered_qty }}</b>
                                @foreach($orderedQty as $ordered)
                                    @if($ordered->date)
                                        <br> <small class="{{\Carbon\Carbon::createFromFormat('Y-m-d', $ordered->date) < \Carbon\Carbon::today() ? 'text-danger' : ''}}">
                                        {{\Carbon\Carbon::parse($ordered->date)->format('ymd')}}(<b>{{$ordered->qty}}</b>)
                                         </small>
                                    @endif
                                @endforeach
                            </td>
                            <td class="text-center">
                                <b>{{ $bomItem->available_qty }}</b>
                                @foreach($qtyArr as $availQty)
                                    @php
                                        $movementStr = '';
                                        $movementColor = '';
                                        if($availQty->action == array_search('Receiving', \App\Models\InventoryMovement::ACTIONS)) {
                                            $movementStr = '[in]';
                                            $movementColor = 'text-primary';
                                        }elseif($availQty->action == array_search('Outgoing', \App\Models\InventoryMovement::ACTIONS)) {
                                            $movementStr = '[out]';
                                            $movementColor = 'text-danger';
                                        }
                                    @endphp
                                    <br>
                                        <small class="{{$movementColor}}">
                                            {{$movementStr}}{{\Carbon\Carbon::parse($availQty->date)->format('ymd')}}(<b>{{$availQty->qty}}</b>)
                                        </small>
                                @endforeach
                            </td>
                            <td class="text-center">
                                <b>{{ $bomItem->planned_qty }}</b>
                                @foreach($plannedQty as $planned)
                                    @if($planned->date)
                                        <br> <small class="
                                        {{\Carbon\Carbon::createFromFormat('Y-m-d', $planned->date) < \Carbon\Carbon::today() ? 'text-danger' : ''}}">
                                        {{\Carbon\Carbon::parse($planned->date)->format('ymd')}}(<b>{{$planned->qty}}</b>)
                                    </small>
                                    @endif
                                @endforeach
                            </td>
                            <td class="text-center">
                                @foreach($outQty as $out)
                                    @if($out->inventoryMovement->order_date)
                                    <small>
                                        {{\Carbon\Carbon::parse($out->inventoryMovement->order_date)->format('ymd')}}(<b>{{$out->qty}}</b>)
                                    </small>
                                    @endif
                                @endforeach
                            </td>
                            @php
                                $supplierQuotePrice = $bomItem->supplierQuotePrices()->latest()->first();
                            @endphp
                            <td class="text-left">
                                {{ $supplierQuotePrice ? $supplierQuotePrice->supplier->company_name : '' }}
                            </td>
                            <td class="text-right">
                                {{ $supplierQuotePrice ? $supplierQuotePrice->unit_price : '' }} @if(isset($supplierQuotePrice->country)) ({{ $supplierQuotePrice->country->currency_name }}) @endif
                            </td>
                            <td class="text-right">
                                {{ $supplierQuotePrice ? $supplierQuotePrice->base_price : null }}
                            </td>
                            <td class="text-center">
                                @if($bomItem->parent) {{$bomItem->parent->code}} - {{$bomItem->parent->name}} @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button type="button" wire:click="edit({{$bomItem}})" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#edit-bom-item">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                            @if($showPlannerArea)
                            <td class="text-right">
                                {{$planOutgoingQty}}
                            </td>
                            @endif
                        </tr>
                        @if($bomItem->children()->exists())
                            @foreach($bomItem->children()->orderBy('code')->get() as $childIndex => $child)
                            @php
                                $planChildOutgoingQty = 0;
                                if($planner['bom_id'] and $planner['qty']) {
                                    $bomChildItemQty = $child
                                                    ->bomContents()
                                                    ->whereHas('bomHeader', function($query) use ($planner) {
                                                        $query->where('bom_id', $planner['bom_id']);
                                                    })
                                                    ->sum('qty');
                                    $planChildOutgoingQty = $bomChildItemQty * $planner['qty'];
                                }
                            @endphp
                            <tr class="row_edit ml-2" wire:loading.class.delay="opacity-2" style="background-color: #daedf4; font-size: 13px;" wire:key="row-child-{{$child->id}}">
                                {{-- <th class="text-center">
                                    <input type="checkbox" wire:model="selected" value="{{$admin->id}}">
                                </th> --}}
                                <td class="text-center">
                                    <span class="badge badge-info">
                                        Child
                                    </span>
                                </td>
                                <td class="text-left">
                                    {{ $child->code }}
                                </td>
                                <td class="text-left">
                                    {{ $child->name }}
                                </td>
                                <td class="text-center">
                                    {{ $child->bomItemType ? $child->bomItemType->name : '' }}
                                </td>
                                <td class="text-center">
                                    <b>{{ $child->ordered_qty }}</b>
                                </td>
                                <td class="text-center">
                                    <b>{{ $child->available_qty }}</b>
                                </td>
                                <td class="text-center">
                                    <b>{{ $child->planned_qty }}</b>
                                </td>
                                <td class="text-center">
                                </td>
                                @php
                                    $childSupplierQuotePrice = $child->supplierQuotePrices()->latest()->first();
                                @endphp
                                <td class="text-left">
                                    {{ $childSupplierQuotePrice ? $childSupplierQuotePrice->supplier->company_name : '' }}
                                </td>
                                <td class="text-right">
                                    {{ $childSupplierQuotePrice ? $childSupplierQuotePrice->unit_price : '' }} @if(isset($childSupplierQuotePrice->country)) ({{ $childSupplierQuotePrice->country->currency_name }}) @endif
                                </td>
                                <td class="text-right">
                                    {{ $childSupplierQuotePrice ? $childSupplierQuotePrice->base_price : null }}
                                </td>
                                <td class="text-center">
                                    @if($child->parent) {{$child->parent->code}} - {{$child->parent->name}} @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button type="button" wire:click="edit({{$child}})" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#edit-bom-item">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                                @if($showPlannerArea)
                                <td class="text-right">
                                    {{$planChildOutgoingQty}}
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        @endif
                    @empty
                    <tr>
                        <td colspan="18" class="text-center"> No Results Found </td>
                    </tr>
                    @endforelse
                </table>
            </div>
            @if($itemPerPage != 'All')
                <div>
                    {{ $bomItems->links() }}
                </div>
            @endif

            {{-- <form wire:submit.prevent="save"> --}}
                <x-modal id="edit-bom-item">
                    <x-slot name="title">
                        Edit Part
                    </x-slot>
                    <x-slot name="content">
                        <x-input type="text" model="bomItemForm.code">
                            Code
                        </x-input>
                        <x-input type="text" model="bomItemForm.name">
                            Name
                        </x-input>
                        <div class="form-group">
                            <label for="bom_item_type_id">
                                Type
                            </label>
                            <select class="select form-control" wire:model.defer="bomItemForm.bom_item_type_id">
                                <option value="">Select..</option>
                                @foreach($bomItemTypes as $bomItemType)
                                    <option value="{{$bomItemType->id}}" {{isset($bomItemForm->bom_item_type_id) && ($bomItemType->id == $bomItemForm->bom_item_type_id) ? 'selected' : ''}}>
                                        {{ $bomItemType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <label class="form-check-label" for="is_required">Is Inventory?</label>
                                <input class="form-check-input ml-2" type="checkbox" name="is_inventory" wire:model="bomItemForm.is_inventory">
                            </div>

                            @if($bomItemForm->is_inventory == false)
                            <div class="bg-light pt-2 pb-2 pl-2 pr-2 mb-2">
                            <div class="form-row">
                                <div class="form-group col-md-12 col-xs-12">
                                    <label class="form-check-label">Is the Children for</label>
                                </div>
                                <div class="form-group col-md-3 col-xs-12">
                                    <label for="name">
                                        Code Filter
                                    </label>
                                    <input type="text" wire:model.debounce.500ms="bomItemFormFilters.code" class="form-control" placeholder="Code">
                                </div>
                                <div class="form-group col-md-3 col-xs-12">
                                    <label for="name">
                                        Name Filter
                                    </label>
                                    <input type="text" wire:model.debounce.500ms="bomItemFormFilters.name" class="form-control" placeholder="Name">
                                </div>
                                <div class="form-group col-md-3 col-xs-12">
                                    <label for="name">
                                        Type Filter
                                    </label>
                                    <select class="select form-control" wire:model="bomItemFormFilters.bom_item_type_id">
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
                                    <select class="select form-control" wire:model="bomItemFormFilters.supplier_id">
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
                                    <select wire:model.defer="bomItemForm.bom_item_parent_id" class="form-control select">
                                        <option value="">Select..</option>
                                        @foreach($bomItemsFilters as $bomItemsFilter)
                                            <option value="{{ $bomItemsFilter->id }}" {{isset($bomItemForm->bom_item_parent_id) && ($bomItemsFilter->id == $bomItemForm->bom_item_parent_id) ? 'selected' : ''}}>
                                                {{ $bomItemsFilter->code }} - {{ $bomItemsFilter->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            </div>
                            @endif
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
                        <hr>
                        <div class="mr-auto pl-1">
                            @role('admin')
                            <button class="btn btn-success" wire:click="createSupplierQuotePrice()" data-toggle="modal" data-target="#create-supplier-quote-price-modal">
                                <i class="fas fa-plus-circle"></i>
                                Create Pricing
                            </button>
                            @endrole
                        </div>

                        <div class="table-responsive pt-2">
                            <table class="table table-bordered table-hover">
                                <tr class="table-primary">
                                    <th class="text-center text-dark" colspan="18">
                                        Pricing History
                                    </th>
                                </tr>
                                <tr class="table-primary">
                                    <th class="text-center text-dark">
                                        #
                                    </th>
                                    <th class="text-center text-dark">
                                        Supplier Company
                                    </th>
                                    <th class="text-center text-dark">
                                        Unit Price
                                    </th>
                                    <th class="text-center text-dark">
                                        Base Price @if($profile->country) ({{$profile->country->currency_name}}) @endif
                                    </th>
                                    <th class="text-center text-dark">
                                        Created At
                                    </th>
                                    <th></th>
                                </tr>
                                @forelse($supplierQuotePrices as $supplierQuotePriceIndex => $supplierQuotePrice)
                                <tr>
                                    <td class="text-center">
                                        {{ $supplierQuotePriceIndex + 1 }}
                                    </td>
                                    <td class="text-left">
                                        {{ $supplierQuotePrice->supplier->company_name }}
                                    </td>
                                    <td class="text-right">
                                        {{ $supplierQuotePrice->unit_price }} @if($supplierQuotePrice->country) ({{ $supplierQuotePrice->country->currency_name }}) @endif
                                    </td>
                                    <td class="text-right">
                                        {{ $supplierQuotePrice->base_price }}
                                    </td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($supplierQuotePrice->created_at)->format('Y-m-d H:ia') }}
                                    </td>
                                    <td class="text-center">
                                        @role('admin')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete the price?') || event.stopImmediatePropagation()" wire:click.prevent="deleteSingleSupplierQuotePrice({{$supplierQuotePrice}})" {{$supplierQuotePrice->inventoryMovementItems()->exists() ? 'disabled' : ''}}>
                                            <i class="fas fa-times-circle"></i>
                                            @if($supplierQuotePrice->inventoryMovementItems()->exists())
                                                This Pricing is in Used in Receiving
                                            @endif
                                        </button>
                                        @endrole
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="18" class="text-center"> No Results Found </td>
                                </tr>
                                @endforelse
                            </table>
                        </div>
                    </x-slot>
                    <x-slot name="footer">

                        <button type="submit" class="btn btn-danger btn-xs-block" onclick="return confirm('Are you sure you want to delete the part?') || event.stopImmediatePropagation()" wire:click.prevent="delete" {{$bomItemForm->id && $bomItemForm->bomContents()->exists() ? 'disabled' : ''}}>
                            <i class="fas fa-trash"></i>
                            Delete
                            @if($bomItemForm->id && $bomItemForm->bomContents()->exists())
                                (This Item is Used in BOM)
                            @endif
                        </button>
                        @role('admin')
                            <button type="submit" class="btn btn-success btn-xs-block" wire:click.prevent="save">
                                <i class="fas fa-save"></i>
                                Save
                            </button>
                        @endrole
                    </x-slot>
                </x-modal>
            {{-- </form> --}}

            <x-modal id="create-supplier-quote-price-modal">
                <x-slot name="title">
                    Create Pricing
                </x-slot>
                <x-slot name="content">
                    <div class="form-group">
                        <label for="supplier_id">
                            Supplier
                        </label>
                        <select class="select form-control"
                            wire:model="supplierQuotePriceForm.supplier_id"
                            wire:change="calculateConvertion()"
                        >
                            <option value="">Select..</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{$supplier->id}}">
                                    {{ $supplier->company_name }}
                                    @if($supplier->attn_name)
                                        ({{ $supplier->attn_name }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if($supplierQuotePriceForm['supplier_id'])
                        <div class="form-group">
                            <label for="unit_price">
                                Unit Price
                                @if($supplierCurrencyName) ({{ $supplierCurrencyName }}) @endif
                            </label>
                            <input type="text" wire:model="supplierQuotePriceForm.unit_price" class="form-control" wire:change="calculateConvertion()" placeholder="Unit Price">
                            <small>
                                Convert to &#x2248; {{$realTimeConversionPrice}} {{$profile->country->currency_name}}
                            </small>
                        </div>
                    @endif
                </x-slot>
                <x-slot name="footer">
                    <div class="btn-group">
                        <button type="submit" class="btn btn-success btn-xs-block" wire:click.prevent="saveSupplierQuotePrice">
                            Submit
                        </button>
                    </div>
                </x-slot>
            </x-modal>
        </div>
    </div>
</div>
