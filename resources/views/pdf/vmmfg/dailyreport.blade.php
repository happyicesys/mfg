@extends('pdf.base')

@section('content')
    <table width="100%">
        <tr>
            <td style="padding-bottom: 0.5em; text-align: right">
                <span style="font-size: 20px">
                    <strong>Vend Mfg Daily Report</strong>
                </span>
            </td>
        </tr>
        <tr>
            <td class="text" valign="top" style="text-align: right">
                <table align="right">
                    <tr>
                        <td>User</td>
                        <td>:</td>
                        <td style="text-align: right">{{ $filtersData['userName'] }}</td>
                    </tr>
                    <tr>
                        <td>Is Done?</td>
                        <td>:</td>
                        <td style="text-align: right">{{ $filtersData['isDone'] }}</td>
                    </tr>
                    <tr>
                        <td>Is Checked?</td>
                        <td>:</td>
                        <td style="text-align: right">{{ $filtersData['isChecked'] }}</td>
                    </tr>
                    <tr>
                        <td>Batch No</td>
                        <td>:</td>
                        <td style="text-align: right">{{ $filtersData['jobBatchNo'] }}</td>
                    </tr>
                    <tr>
                        <td>Unit No</td>
                        <td>:</td>
                        <td style="text-align: right">{{ $filtersData['unitNo'] }}</td>
                    </tr>
                    <tr>
                        <td>Date From</td>
                        <td>:</td>
                        <td style="text-align: right">{{ $filtersData['dateFrom'] }}</td>
                    </tr>
                    <tr>
                        <td>Date To</td>
                        <td>:</td>
                        <td style="text-align: right">{{ $filtersData['dateTo'] }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="items">
        <table  style="width:100%;">
            <thead>
            <tr>
                <th align="center">
                    #
                </th>
                <th align="center">
                    Batch No
                </th>
                <th align="center">
                    Unit No
                </th>
                <th align="center">
                    Model
                </th>
                <th align="left">
                    Title No
                </th>
                <th align="left">
                    Title Name
                </th>
                <th align="left">
                    Task No
                </th>
                <th align="left">
                    Task Name
                </th>
                <th align="center">
                    Done By
                </th>
                <th align="center">
                    Done Time
                </th>
                <th align="center">
                    Checked By
                </th>
                <th align="center">
                    Checked Time
                </th>
                <th align="center">
                    Undo By
                </th>
                <th align="center">
                    Undo Time
                </th>
            </tr>
            </thead>
            <tbody>
                @forelse($tasks as $index => $item)
                <tr class="row_edit" wire:loading.class.delay="opacity-2" wire:key="row-{{$index}}">
                    {{-- <th class="text-center">
                        <input type="checkbox" wire:model="selected" value="{{$admin->id}}">
                    </th> --}}
                    {{-- @dd($tasks->toArray()) --}}
                    <td align="center">
                        {{ $index + 1}}
                    </td>
                    <td align="center">
                        {{ $item->vmmfgUnit->vmmfgJob->batch_no }}
                    </td>
                    <td align="center">
                        {{ $item->vmmfgUnit->unit_no }}
                    </td>
                    <td align="center">
                        {{ $item->vmmfgUnit->vmmfgJob->model }}
                    </td>
                    <td align="left">
                        {{ $item->vmmfgItem->vmmfgTitle->sequence }}
                    </td>
                    <td align="left" style="font-family: 'founder-type'; font-weight: bold; max-width: 180px; word-wrap: break-word;">
                        {{ $item->vmmfgItem->vmmfgTitle->name }}
                    </td>
                    <td align="left">
                        {{ $item->vmmfgItem->sequence }}
                    </td>
                    <td align="left" style="font-family: 'founder-type'; font-weight: bold;  max-width: 180px; word-wrap: break-word;">
                        {{ $item->vmmfgItem->name }}
                    </td>
                    <td align="center">
                        {{ $item->doneBy ? $item->doneBy->name : null }}
                    </td>
                    <td align="center">
                        {{ $item->done_time ? \Carbon\Carbon::parse($item->done_time)->format('Y-m-d h:ia') : null }}
                    </td>
                    <td align="center">
                        {{ $item->checkedBy ? $item->checkedBy->name : null }}
                    </td>
                    <td align="center">
                        {{  $item->checked_time ? \Carbon\Carbon::parse($item->checked_time)->format('Y-m-d h:ia') : null }}
                    </td>
                    <td align="center">
                        {{ $item->undoDoneBy ? $item->undoDoneBy->name : null }}
                    </td>
                    <td align="center">
                        {{  $item->undo_done_time ? \Carbon\Carbon::parse($item->undo_done_time)->format('Y-m-d h:ia') : null }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="18" align="center"> No Results Found </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td align="right" colspan="4">Total </td>
                    <td align="right" colspan="10">{{ count($tasks) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    {{-- <div class="footer" style="width: 100%; text-align: center;">
        <div style="position: absolute; width: 100%; bottom: 0;">
            <table width="100%">
                <tr>
                    <td style="padding-bottom: 10px; text-align: center">
                        This is computer generated invoice, no signature is required.
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center">
                        <p>
                            Powered by <img align="middle" width="100px" src="{{ public_path('/img/icon.png') }}">
                        </p>
                    </td>
                </tr>
            </table>
        </div>
    </div> --}}
@endsection



