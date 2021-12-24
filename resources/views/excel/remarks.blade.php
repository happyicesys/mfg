<table>
    <tr>
        <th>Scope</th>
        <th>{{ $scope->name }}</th>
    </tr>
    <tr>
        <th>Date From</th>
        <th>{{ $filters['date_from'] }}</th>
    </tr>
    <tr>
        <th>Date To</th>
        <th>{{ $filters['date_to'] }}</th>
    </tr>
    <tr>
        <th>Is Completed?</th>
        <th>{{ $filters['is_completed'] }}</th>
    </tr>
</table>

@foreach($scope->vmmfgTitles()->orderBy('sequence')->get() as $title)
<table>
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
</table>
@endforeach