<div
    wire:ignore
    x-data
    x-init="
        @this.set('fileRefId', {{ $attributes['id'] }})
        FilePond.setOptions({
            allowMultiple: {{ isset($attributes['multiple']) ? 'true' : 'false' }},
            allowRevert: false,
            server: {
                process: (fieldName, file, metadata, load, error, progress, abort, transfer, options) => {
                    @this.upload('{{ $attributes['wire:model'] }}', file, load, error, progress)
                },
            },
            credits: false,
        });
        FilePond.create($refs.input);
">
    <input type="file" x-ref="input">
</div>