<div>
    <div>
        <div>
            <x-flash></x-flash>
            <h2>Excel Report</h2>
            <hr>
            <div class="">
                <div>
                    <div class="bg-light pt-2 pb-2 pl-2 pr-2 mb-2">
                        <div class="form-row">
                            <div class="form-group col-md-4 col-xs-12">
                                <label>
                                    Scope
                                </label>
                                <select name="vmmfg_scope_id" wire:model="filters.vmmfg_scope_id" class="select form-control">
                                    <option value="">Select..</option>
                                    @foreach($scopes as $scope)
                                        <option value="{{$scope->id}}">
                                            {{$scope->name}}
                                            @if($scope->remarks)({{$scope->remarks}})@endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-xs-12">
                                <label>
                                    Start Date From
                                </label>
                                <div class="input-group">
                                    <input type="date" class="form-control" wire:model.defer="filters.date_from">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateClicked(-1, 'date_from')">
                                            <i class="fas fa-caret-left"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateClicked(1, 'date_from')">
                                            <i class="fas fa-caret-right"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- <x-input-date model="filters.date_from" placeholder="Date From"></x-input-date> --}}
                                {{-- <input wire:model="filters.date_from" type="date" class="form-control" placeholder="Date From"> --}}
                            </div>
                            <div class="form-group col-md-4 col-xs-12">
                                <label>
                                    Start Date To
                                </label>
                                <div class="input-group">
                                    <input type="date" class="form-control" wire:model.defer="filters.date_to">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateClicked(-1, 'date_to')">
                                            <i class="fas fa-caret-left"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" wire:click.prevent="onPrevNextDateClicked(1, 'date_to')">
                                            <i class="fas fa-caret-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4 col-xs-12">
                                <label>
                                    Is Completed?
                                </label>
                                <select name="is_completed" wire:model="filters.is_completed" class="select form-control">
                                    <option value="">All</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row d-flex justify-content-end">
                            <div class="btn-group">
                                <button class="btn btn-outline-primary btn-md" wire:click="exportExcel()">
                                    <i class="far fa-file-excel"></i>
                                    Excel
                                </button>
                                <button wire:click="resetFilters()" class="btn btn-outline-dark">Reset</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
