<div>
    <div>
        <div>
            <x-flash></x-flash>
            <h2>Inventory</h2>
            <hr>
            @php
                $bomItemsArr = $bomItems->toArray();
                $from = $bomItemsArr['from'];
                $total = $bomItemsArr['total'];

                $profile = \App\Models\Profile::where('is_primary', 1)->first();
            @endphp
            <div class="">
                <div>
                    <div class="bg-light pt-2 pb-2 pl-2 pr-2 mb-2">
                        <div class="form-row">
                            <div class="form-group col-md-4 col-xs-12">
                                <label>
                                    Code
                                </label>
                                <input wire:model="filters.code" type="text" class="form-control" placeholder="Code">
                            </div>
                            <div class="form-group col-md-4 col-xs-12">
                                <label>
                                    Name
                                </label>
                                <input wire:model="filters.name" type="text" class="form-control" placeholder="Name">
                            </div>
                            <div class="form-group col-md-4 col-xs-12">
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
                            <div class="form-group col-md-4 col-xs-12">
                                <label>
                                    Is Consumable?
                                </label>
                                <select name="is_consumable" wire:model="filters.is_consumable" class="select form-control">
                                    <option value="">All</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
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
                                Showing {{ count($bomItems) }} of {{$total}}
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
                        <x-th-data model="ordered_qty">
                            Ordered Qty
                        </x-th-data>
                        <x-th-data model="available_qty" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Available Qty
                        </x-th-data>
                        <x-th-data model="planned_qty">
                            Planned Qty
                        </x-th-data>
                        <th class="text-center text-dark">
                            Base Price @if($profile->country) ({{$profile->country->currency_name}}) @endif
                        </th>
                        <th class="text-center text-dark">
                            Action
                        </th>
                    </tr>
                    @forelse($bomItems as $index => $bomItem)
                        <tr class="row_edit" wire:loading.class.delay="opacity-2" wire:key="row-{{$index}}">
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
                                {{ $bomItem->available_qty }}
                            </td>
                            <td class="text-right">
                                {{ $bomItem->supplierQuotePrices()->latest()->first() ? $bomItem->supplierQuotePrices()->latest()->first()->base_price : null }}
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button type="button" wire:click="edit({{$bomItem->id}})" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#edit-bom-item">
                                        <i class="fas fa-edit"></i>
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
                {{ $bomItems->links() }}
            </div>

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
                        <hr>
                        <div class="mr-auto pl-1">
                            <button class="btn btn-success" wire:click="createSupplierQuotePrice()" data-toggle="modal" data-target="#create-supplier-quote-price-modal">
                                <i class="fas fa-plus-circle"></i>
                                Create Pricing
                            </button>
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
                        <button type="submit" class="btn btn-success d-none d-sm-block" wire:click.prevent="save">
                            Submit
                        </button>
                        <button type="submit" class="btn btn-success btn-block d-block d-sm-none" wire:click.prevent="save">
                            Submit
                        </button>
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
                    <button type="submit" class="btn btn-success d-none d-sm-block" wire:click.prevent="saveSupplierQuotePrice">
                        Submit
                    </button>
                    <button type="submit" class="btn btn-success btn-block d-block d-sm-none" wire:click.prevent="saveSupplierQuotePrice">
                        Submit
                    </button>
                </x-slot>
            </x-modal>
        </div>
    </div>
</div>
