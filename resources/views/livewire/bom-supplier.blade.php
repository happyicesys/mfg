<div>
    <div>
        <div>
            <x-flash></x-flash>
            <h2>Supplier</h2>
            <hr>
            @php
                $suppliersArr = $suppliers->toArray();
                $from = $suppliersArr['from'];
                $total = $suppliersArr['total'];

                $profile = \App\Models\Profile::where('is_primary', 1)->first();
            @endphp
            <div class="">
                <div>
                    <div class="bg-light pt-2 pb-2 pl-2 pr-2 mb-2">
                        <div class="form-row">
                            <div class="form-group col-md-4 col-xs-12">
                                <label>
                                   Company Name
                                </label>
                                <input wire:model="filters.company_name" type="text" class="form-control" placeholder="Company Name">
                            </div>
                            <div class="form-group col-md-4 col-xs-12">
                                <label>
                                    Part
                                </label>
                                <select name="bom_item_id" wire:model="filters.bom_item_id" class="select form-control">
                                    <option value="">All</option>
                                    @foreach($bomItems as $bomItem)
                                        <option value="{{$bomItem->id}}">
                                            {{$bomItem->code}} {{$bomItem->name}}
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
                                Showing {{ count($suppliers) }} of {{$total}}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive pt-3" style="font-size: 14px;">
                <table class="table table-bordered table-hover">
                    <tr class="table-secondary">
                        <th class="text-center text-dark">
                            #
                        </th>
                        <x-th-data model="company_name" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Company Name
                        </x-th-data>
                        <x-th-data model="attn_name" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Attn Name
                        </x-th-data>
                        <x-th-data model="attn_contact" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Attn Contact
                        </x-th-data>
                        <x-th-data model="email" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Email
                        </x-th-data>
                        <th class="text-center text-dark">
                            Action
                        </th>
                    </tr>
                    @forelse($suppliers as $index => $supplier)
                        <tr class="row_edit" wire:loading.class.delay="opacity-2" wire:key="row-{{$index}}">
                            <td class="text-center">
                                {{ $index + $from}}
                            </td>
                            <td class="text-left">
                                {{ $supplier->company_name }}
                            </td>
                            <td class="text-left">
                                {{ $supplier->attn_name }}
                            </td>
                            <td class="text-center">
                                {{ $supplier->attn_contact }}
                            </td>
                            <td class="text-center">
                                {{ $supplier->email }}
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button type="button" wire:click="edit({{$supplier->id}})" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#edit-supplier">
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
                {{ $suppliers->links() }}
            </div>

            {{-- <form wire:submit.prevent="save"> --}}
                <x-modal id="edit-supplier">
                    <x-slot name="title">
                        Edit Supplier
                    </x-slot>
                    <x-slot name="content">
                        <x-input type="text" model="supplierForm.company_name">
                            Company Name
                        </x-input>
                        <x-input type="text" model="supplierForm.attn_name">
                            Name
                        </x-input>
                        <x-input type="text" model="supplierForm.attn_contact">
                            Contact
                        </x-input>
                        <x-input type="text" model="supplierForm.url">
                            URL
                        </x-input>
                        <x-input type="text" model="supplierForm.email">
                            Email
                        </x-input>
                        <div class="form-group">
                            <label>
                                Payment Term
                            </label>
                            <select name="payment_term_id" wire:model.defer="supplierForm.payment_term_id" class="select form-control">
                                <option value="">Select..</option>
                                @foreach($paymentTerms as $paymentTerm)
                                    <option value="{{$paymentTerm->id}}">
                                        {{$paymentTerm->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>
                                Transacted Currency
                            </label>
                            <select name="country_id" wire:model.defer="supplierForm.country_id" class="select form-control">
                                <option value="">Select..</option>
                                @foreach($countries as $country)
                                    <option value="{{$country->id}}">
                                        {{$country->currency_symbol}} ({{$country->currency_name}})
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
</div>
