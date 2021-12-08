@props(['model'])

<div
    x-data
    x-init="new moment()"
></div>

<div class="input-group">
    <input type="date" class="form-control" wire:model.defer="{{$model}}" {{$attributes}}>
    <div class="input-group-append">

        <button class="btn btn-outline-secondary" wire:click="onPrevDateClicked({{$model}})">
            <i class="fas fa-caret-left"></i>
        </button>

        <button class="btn btn-outline-secondary" wire:click="onNextDateClicked({{$model}})">
            <i class="fas fa-caret-right"></i>
        </button>
    </div>
</div>