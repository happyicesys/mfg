{{--
<div>
    <div>
        <select class="select2">
            <option value="">Select Option</option>
            {{$slot}}
        </select>
    </div>
</div> --}}

<div wire:ignore class="w-full">
    <select class="form-select select2" data-minimum-results-for-search="Infinity" data-placeholder="{{__('Select your option')}}">
        {{$slot}}
    </select>
</div>