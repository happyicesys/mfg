<div>
    <div>
        <div>
            <x-flash></x-flash>
            <h2>QA/QC</h2>
            <hr>

            <div class="">
                <div>
                    <div class="bg-light p-3">
                        {{-- <div class="form-row"> --}}
                            <div class="form-group">
                                <label>
                                    Batch No
                                </label>
                                <select name="batch_no" wire:model="batch_no" class="select form-control">
                                    <option value="">Select...</option>
                                    @foreach($jobs as $job)
                                        <option value="{{$job->id}}">
                                            #{{$job->batch_no}} - {{$job->model}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if($this->batch_no)
                                <div class="form-group">
                                    <label>
                                        Unit No
                                    </label>
                                    <select name="unit_no" wire:model="unit_no" class="select form-control">
                                        <option value="">Select...</option>
                                        @foreach($this->job->vmmfgUnits as $unit)
                                            <option value="{{$unit->id}}">
                                                #{{$unit->unit_no}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                        {{-- </div> --}}
                        <div class="form-group">
                            {{-- <div class="btn-group"> --}}
                                <button wire:click="resetFilters()" class="btn btn-outline-dark btn-block">Reset</button>
                            {{-- </div> --}}
                        </div>
                    </div>
                </div>
            </div>

            @if($this->unit_no and $this->unit->vmmfgScope)
                <ul class="list-group">
                    @forelse($this->unit->vmmfgScope->vmmfgTitles as $title)
                    <li class="list-group-item mt-2" style="background-color: #9bc2cf;">
                        {{$title->sequence}}.  {{$title->name}}
                    </li>
                        @foreach($title->vmmfgItems as $item)
                        <li class="list-group-item ml-5" style="background-color: #e6f3f7;">
                            {{$item->sequence}}.  {{$item->name}}
                        </li>
                        @endforeach
                    @empty
                        <li class="list-group-item text-center">
                            No Scope Attached to this Unit.
                        </li>
                    @endforelse
                </ul>
            @else
                @if($this->unit_no)
                    <ul class="list-group">
                        <li class="list-group-item text-center">
                            No Scope Attached to this Unit.
                        </li>
                    </ul>
                @endif
            @endif
{{--
                <x-modal id="edit-scope">
                    <x-slot name="title">
                        Edit Scope
                    </x-slot>
                    <x-slot name="content">
                        <x-input type="text" model="form.name">
                            Name
                        </x-input>
                        <div class="form-group">
                            <label for="remarks">
                                Remarks
                            </label>
                            <textarea name="remarks" wire:model="form.remarks" rows="5" class="form-control"></textarea>
                        </div>
                        <hr>
                        <div class="form-group">
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
                                <div class="bg-light">
                                    <div class="form-group">
                                        <x-input type="text" model="titleForm.sequence">
                                            Sequence (Number only)
                                        </x-input>
                                    </div>
                                    <div class="form-group">
                                        <x-input type="text" model="titleForm.name">
                                            Name
                                        </x-input>
                                    </div>
                                    <button type="submit" class="btn btn-success d-none d-sm-block" wire:click.prevent="generateTitle">
                                        Create Title
                                    </button>
                                    <button type="submit" class="btn btn-success btn-block d-block d-sm-none" wire:click.prevent="generateTitle">
                                        Create Title
                                    </button>
                                </div>
                            @endif
                        </div>
                        <hr>
                        @if($form)
                            <ul class="list-group">
                                @foreach($form->vmmfgTitles as $title)
                                <li class="list-group-item" style="background-color: #d3d3d3;">
                                    {{$title->sequence}}.  {{$title->name}}
                                </li>
                                    @foreach($title->vmmfgItems as $item)
                                    <li class="list-group-item ml-5" style="background-color: #ededed;">
                                        {{$item->sequence}}.  {{$item->name}}
                                    </li>
                                    @endforeach
                                @endforeach
                            </ul>
                        @endif
                    </x-slot>
                    <x-slot name="footer">
                        <button type="submit" class="btn btn-success d-none d-sm-block" wire:click.prevent="save">
                            Save
                        </button>
                        <button type="submit" class="btn btn-success btn-block d-block d-sm-none" wire:click.prevent="save">
                            Save
                        </button>
                    </x-slot>
                </x-modal> --}}
        </div>
    </div>

</div>
