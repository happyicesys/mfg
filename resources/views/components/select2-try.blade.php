<div wire:ignore class="w-full">
    <select class="form-select select2" data-minimum-results-for-search="Infinity" data-placeholder="{{__('Select your option')}}" {{ $attributes }}>
        <option></option>
        {{$slot}}
    </select>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: '{{__('Select your option')}}',
                allowClear: true
            });
            $('.select2').on('change', function (e) {
                let elementName = $(this).attr('id');
                var data = $(this).select2("val");
                @this.set(elementName, data);
            });
        });
    </script>
@endpush