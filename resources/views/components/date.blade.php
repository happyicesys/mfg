@props(['model'])

<div
    x-data
    x-init="new Pikaday({field: $refs.input, format: 'YYYY-MM-DD'})"
    @change="$dispatch('input', $event.target.value)"
    class="form-group"
>
    <label for="{{$model}}">
        {{$slot}}
    </label>
    <input {{$attributes}} x-ref="input" class="form-control
    @if($errors->has($model)) is-invalid @endif" wire:model.defer="{{$model}}" placeholder="{{$slot}}">
    @if($errors->has($model))<span class="invalid-feedback" role="alert"><strong>{{ $errors->first($model) }}</strong></span>@endif
</div>

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.6.0/css/pikaday.min.css" integrity="sha512-yFCbJ3qagxwPUSHYXjtyRbuo5Fhehd+MCLMALPAUar02PsqX3LVI5RlwXygrBTyIqizspUEMtp0XWEUwb/huUQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.6.0/pikaday.min.js" integrity="sha512-AWC8WaJpos1L8xD++XDtqY3znmqhqDY/o4KZ3wo7kmt1Hx6YjP4ZqPnYDrLg1ymG6iidGzq/UKHS/MxBwVAlwQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush