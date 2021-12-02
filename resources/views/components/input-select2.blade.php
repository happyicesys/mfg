@props(['model'])

<div wire:key="{{$model}}">
    <div wire:ignore>
        <select id="{{$model}}" class="select form-control">
            {{$slot}}
        </select>
        @error('select')<span class="error">{{ $message }}</span>@enderror
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        $('.select').select2().on('change', function (e) {
            let elementName = $(this).attr('id');
            @this.set(elementName, e.target.value);
        });
    });



    document.addEventListener("livewire:load", function (event) {
        window.livewire.hook('message.processed', () => {
            $('.select').select2();
        });
    });
</script>
@endpush
