<table>
    <tr>
        <th>Scope</th>
        <th>{{ $scope->name }}</th>
    </tr>
</table>

@foreach($scope->vmmfgTitles()->orderBy('sequence')->get() as $title)
<table>
    <thead>
    <tr>
        <th>
            {{ $title->sequence }} - {{ $title->name }}
            @if($title->vmmfgTitleCategory)
             ({{ $title->vmmfgTitleCategory->name }})
            @endif
        </th>
    </tr>
    @if($title->vmmfgItems()->exists())
        @foreach($title->vmmfgItems()->orderBy('sequence')->get() as $item)
        <tr>
            <td></td>
            <td>
                {{ $item->sequence }} - {{ $item->name }}
            </td>
        </tr>
        @endforeach
    @endif
    </thead>
    <tbody>
    </tbody>
</table>
@endforeach