@props(['type', 'model'])

<div class="form-group">
    <label for="{{$model}}">
        {{$slot}}
    </label>
    <input type={{$type}} class="form-control
    @if($errors->has($model)) is-invalid @endif" wire:model.defer="{{$model}}" placeholder="{{$slot}}">
    @if($errors->has($model))<span class="invalid-feedback" role="alert" {{$attributes}}><strong>{{ $errors->first($model) }}</strong></span>@endif
</div>