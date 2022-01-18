<div>
    <div>
        <div>
            <x-flash></x-flash>
            <h2>Currency</h2>
            <hr>
            @php
                $countriesArr = $countries->toArray();
                $from = $countriesArr['from'];
                $total = $countriesArr['total'];

                $profile = \App\Models\Profile::where('is_primary', 1)->first();
            @endphp
            <div class="">
                <div>
                    <div class="bg-light pt-2 pb-2 pl-2 pr-2 mb-2">
                        <div class="form-row">
                            <div class="form-group col-md-4 col-xs-12">
                                <label>
                                   Country Name
                                </label>
                                <input wire:model="filters.name" type="text" class="form-control" placeholder="Country Name">
                            </div>
                            <div class="form-group col-md-4 col-xs-12">
                                <label>
                                   Currency Name
                                </label>
                                <input wire:model="filters.currency_name" type="text" class="form-control" placeholder="Currency Name">
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
                                Showing {{ count($countries) }} of {{$total}}
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
                        <x-th-data model="name" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Country Name
                        </x-th-data>
                        <x-th-data model="currency_name" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Currency Name
                        </x-th-data>
                        <x-th-data model="currency_symbol" sortKey="{{$sortKey}}" sortAscending="{{$sortAscending}}">
                            Symbol
                        </x-th-data>
                        <th class="text-center text-dark">
                            Current Quoted Rate (Base: 1 {{$profile->country->currency_name}})
                        </th>
                        <th class="text-center text-dark">
                            Action
                        </th>
                    </tr>
                    @forelse($countries as $index => $country)
                        <tr class="row_edit" wire:loading.class.delay="opacity-2" wire:key="row-{{$index}}">
                            {{-- <th class="text-center">
                                <input type="checkbox" wire:model="selected" value="{{$admin->id}}">
                            </th> --}}
                            <td class="text-center">
                                {{ $index + $from}}
                            </td>
                            <td class="text-left">
                                {{ $country->name }}
                            </td>
                            <td class="text-left">
                                {{ $country->currency_name }}
                            </td>
                            <td class="text-left">
                                {{ $country->currency_symbol }}
                            </td>
                            <td class="text-right">
                                {{ $country->currencyRates()->latest()->first() ? $country->currencyRates()->latest()->first()->rate : null }}
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button type="button" wire:click="edit({{$country->id}})" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#edit-currency">
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
                {{ $countries->links() }}
            </div>

            {{-- <form wire:submit.prevent="save"> --}}
                <x-modal id="edit-currency">
                    <x-slot name="title">
                        Edit Currency
                    </x-slot>
                    <x-slot name="content">
                        <x-input type="text" model="currencyForm.rate">
                            Current Quoted Rate (Base: 1 {{$profile->country->currency_name}})
                        </x-input>
                        <label for="history">
                            Rate History
                        </label>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <tr class="table-primary">
                                    <th class="text-center text-dark">
                                        #
                                    </th>
                                    <th class="text-center text-dark">
                                        Rate
                                    </th>
                                    <th class="text-center text-dark">
                                        Created At
                                    </th>
                                </tr>
                                @forelse($currencyRates as $currencyRateIndex => $currencyRate)
                                <tr>
                                    <td class="text-center">
                                        {{ $currencyRateIndex + 1 }}
                                    </td>
                                    <td class="text-center">
                                        {{ $currencyRate->rate }}
                                    </td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($currencyRate->created_at)->format('Y-m-d H:ia') }}
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
        </div>
    </div>
</div>
