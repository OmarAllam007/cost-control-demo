@php
    $data = json_decode($issue->data, true);
@endphp

<article class="panel panel-warning">
    <div class="panel-heading">
        <h4 class="panel-title">Physical Quantity</h4>
    </div>
    <table class="table table-condensed table-bordered">
        <thead>
        <tr>
            <th>Activity</th>
            <th>Budget Resource Name</th>
            <th>Budget U.O.M</th>

            <th>Store Resource Name</th>
            <th>Store U.O.M</th>
            <th>Store Qty</th>
            <th>Store Unit Price</th>

            <th>Physical Qty</th>
            <th>Equivalent Unit Price</th>
            <th>Cost</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $resource)
            @php
                $counter = 0;
                $rowsCount = count($resource['rows']);
            @endphp

            @foreach($resource['rows'] as $row)
                <tr>
                    @if ($counter == 0)
                        <td rowspan="{{$rowsCount}}">
                            @php
                            $shadow = App\BreakDownResourceShadow::find($resource['resource']['id']);
                            @endphp
                            @if ($shadow)
                                {{$shadow->wbs->path . ' / ' . $shadow->activity }}
                            @endif
                        </td>
                        <td rowspan="{{$rowsCount}}">{{$shadow->resource_name}}</td>
                        <td rowspan="{{$rowsCount}}">{{$shadow->measure_unit}}</td>
                    @endif

                    <td>{{$row[2]}}</td>
                    <td>{{$row[3]}}</td>
                    <td>{{sprintf('%.02f', $row[4])}}</td>
                    <td>{{sprintf('%.02f', $row[5])}}</td>

                    @if ($counter == 0)
                        <td rowspan="{{$rowsCount}}">{{sprintf('%.02f', $resource['newResource'][4])}}</td>
                        <td rowspan="{{$rowsCount}}">{{sprintf('%.02f', $resource['newResource'][5])}}</td>
                        <td rowspan="{{$rowsCount}}">{{sprintf('%.02f', $resource['newResource'][6])}}</td>
                    @endif
                </tr>
                @php $counter ++ @endphp
            @endforeach
        @endforeach
        </tbody>
    </table>
</article>

