@props(['id'])

<div wire:ignore.self class="modal fade" id="{{$id}}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
       <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {{ $title }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                {{ $content }}
            </div>
            <div class="modal-footer">
                {{ $footer }}
            </div>
       </div>
    </div>
</div>