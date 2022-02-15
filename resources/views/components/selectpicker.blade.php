<div id="for-picker" wire:ignore>
    <select {{$attributes}} class="selectpicker" data-container="#for-picker">
        {{$slot}}
    </select>
</div>

@push('scripts')
<script>
    $('#for-picker').selectpicker();
</script>
@endpush