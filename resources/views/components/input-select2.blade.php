@props(['model'])

<div wire:key="{{$model}}">
    <div wire:ignore id="select-picker">
        <select id="{{$model}}" class="select form-control" data-container="#select-picker">
            {{$slot}}
        </select>
        @error('select')<span class="error">{{ $message }}</span>@enderror
    </div>
</div>

@push('scripts')
<script>
        $('.select').select2().on('change', function (e) {
            let elementName = $(this).attr('id');
            @this.set(elementName, e.target.value);
        });



    document.addEventListener("livewire:load", function (event) {
        window.livewire.hook('afterDomUpdate', () => {
            $('.select').select2();
        });
    });
</script>
@endpush
