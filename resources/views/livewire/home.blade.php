<div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            @foreach($years as $year)
                @php
                    $totalOrder = 0;
                    $totalComplete = 0;
                @endphp
                <div class="card mt-2">
                    <div class="card-header">
                        For Year {{$year}}
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">
                                            Month
                                        </th>
                                        <th scope="col" class="text-center">
                                            Start (# Units)
                                        </th>
                                        <th scope="col" class="text-center">
                                            Completed (# Units)
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataArr[$year] as $index => $data)
                                        <tr>
                                            <td scope="row" align="center">
                                                {{$data['name']}}
                                            </td>
                                            <td class="text-center">
                                                {{$data['order']}}
                                            </td>
                                            <td class="text-center">
                                                {{$data['completion']}}
                                            </td>
                                        </tr>
                                        @php
                                            $totalOrder += $data['order'];
                                            $totalComplete += $data['completion'];
                                        @endphp
                                        @if($index == count($dataArr[$year]))
                                            <tr>
                                                <td scope="row" align="center">
                                                    <strong>
                                                        Total
                                                    </strong>
                                                </td>
                                                <td class="text-center">
                                                    <strong>
                                                        {{$totalOrder}}
                                                    </strong>
                                                </td>
                                                <td class="text-center">
                                                    <strong>
                                                        {{$totalComplete}}
                                                    </strong>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    {{-- <div style="height: 32rem;">
        <livewire:livewire-line-chart :line-chart-model="$multiLineChartModel"/>
    </div> --}}
</div>
