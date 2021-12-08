<div
    wire:ignore
    x-data
    x-init="
        $(this.$refs.select)
        .select2({
            data: this.options,
            placeholder: 'Select..',
            allowClear: true
        })
        .on('change', function (ev, args) {
            if (!(args && 'ignore' in args)) {
                $dispatch('input', $(this).val())
            }
        });

        $nextTick(() => {
        $(this.$refs.select)
            .val(this.value)
            .trigger('change', { ignore: true })
        });

        $watch(
            value: function (value, oldValue) {
            $(this.$refs.select)
                .val(this.value)
                .trigger('change', { ignore: true });
          },
          options: function (options) {
            $(this.$refs.select).select2({ data: options })
          }
        )
">
    <select class="form-control" x-ref="input" {{$attributes}}>
        {{$slot}}
    </select>
</div>
