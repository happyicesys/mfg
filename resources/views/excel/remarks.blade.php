<table>
    <tr>
        <th>Scope</th>
        <th>{{ $scope->name }}</th>
    </tr>
    <tr>
        <th>S.Date From</th>
        <th>{{ $filters['date_from'] }}</th>
    </tr>
    <tr>
        <th>S.Date To</th>
        <th>{{ $filters['date_to'] }}</th>
    </tr>
    <tr>
        <th>Is Completed?</th>
        <th>{{ $filters['is_completed'] ? 'Yes' : 'No' }}</th>
    </tr>
</table>

@php
    $units = $scope->vmmfgUnits;
    // dd($scope->toArray(), $units->toArray());
@endphp

<table>
    <tr>
        <th></th>
        <th></th>
        @if($units)
            @foreach($units as $unit)
                <th>
                    <b>
                        @if($unit->vmmfgJob)
                            {{ $unit->vmmfgJob->batch_no }}
                        @endif
                        #{{ $unit->unit_no }}
                    </b>
                </th>
            @endforeach
        @endif
    </tr>
    <tr>
        <th></th>
        <th></th>
        @if($units)
            @foreach($units as $unit)
                <th>
                    <b>
                        @if($unit->vend_id)
                            {{ $unit->vend_id }}
                        @endif
                    </b>
                </th>
            @endforeach
        @endif
    </tr>
    <tr>
        <th></th>
        <th></th>
        @if($units)
            @foreach($units as $unit)
                <th>
                    <b>
                        Start: {{ $unit->order_date }}
                    </b>
                </th>
            @endforeach
        @endif
    </tr>
</table>

@foreach($scope->vmmfgTitles()->orderBy('sequence')->get() as $title)
<table>
    <tr>
        <th>
            <b>
                {{ $title->sequence }} - {{ $title->name }}
                @if($title->vmmfgTitleCategory)
                ({{ $title->vmmfgTitleCategory->name }})
                @endif
            </b>
        </th>
    </tr>
    @if($title->vmmfgItems()->exists())
        @foreach($title->vmmfgItems()->orderBy('sequence')->get() as $item)
        <tr>
            <td></td>
            <td style="wrap-text: true;">
                {{ $item->sequence }} - {{ $item->name }}
            </td>

            @if($units)
                @foreach($units as $unit)
                    <td>
                        @if($task = $unit->vmmfgTasks()->where('vmmfg_item_id', $item->id)->first())
                            @if($task->status === \App\Models\VmmfgTask::STATUS_DONE)
                                Done: {{$task->doneBy->name}}
                                <br>
                                {{\Carbon\Carbon::parse($task->done_time)->format('Y-m-d H:ia')}}
                                <br>
                            @elseif($task->status === \App\Models\VmmfgTask::STATUS_CANCELLED)
                                Cancelled: {{$task->cancelledBy->name}}
                                <br>
                                {{\Carbon\Carbon::parse($task->cancelled_time)->format('Y-m-d H:ia')}}
                                <br>
                            @endif
                            {{ $task->remarks }}
                        @endif
                    </td>
                @endforeach
            @endif
        </tr>
        @endforeach
    @endif
</table>
@endforeach