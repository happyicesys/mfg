@props(['model'])

<div>
    <div wire:ignore>
        <select class="form-control" id="{{$model}}" {{$attributes}}>
            <option value="">Select..</option>
            {{$slot}}
        </select>
    </div>

</div>

@push('scripts')

<script>
    let model = {!! json_encode($model) !!}
    let modelId = '#' + model;

    $(document).ready(function () {
        $(modelId).select2();
        $(modelId).on('change', function (e) {
            var data = $(modelId).select2("val");
            @this.set(model, data);
        });
    });

</script>

@endpush