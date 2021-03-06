@props(['model', 'sortKey', 'sortAscending'])

<th class="text-center" >
    <a href="#" wire:click.prevent="sortBy('{{$model}}')">
        {{$slot}}
    </a>
    @if(isset($sortKey) and isset($sortAscending))
    @php

    // dd((bool)$sortKey == $model, (bool)$sortAscending);
@endphp
        @if($sortKey == "{{$model}}" and $sortAscending)
            <span class="fa fa-caret-down"></span>
        @endif
        @if($sortKey == "{{$model}}" and !$sortAscending)
            <span class="fa fa-caret-up"></span>
        @endif
    @endif
</th>