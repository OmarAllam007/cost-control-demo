@php
    $data = json_decode($issue->data, true);
@endphp

<article class="panel panel-default">
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
                            $shadow = \App\BreakDownResourceShadow::byRawData($resource);
                            @endphp
                            @if ($shadow)
                                {{$shadow->wbs->path . ' / ' . $shadow->activity }}
                            @endif
                        </td>
                        <td rowspan="{{$rowsCount}}">{{$resource[2]}}</td>
                        <td rowspan="{{$rowsCount}}">{{$resource[3]}}</td>
                    @endif

                    <td>{{$row[2]}}</td>
                    <td>{{$row[3]}}</td>
                    <td>{{$row[4]}}</td>
                    <td>{{$row[5]}}</td>

                    @if ($counter == 0)
                        <td rowspan="{{$rowsCount}}">{{$resource[4]}}</td>
                        <td rowspan="{{$rowsCount}}">{{$resource[5]}}</td>
                        <td rowspan="{{$rowsCount}}">{{$resource[6]}}</td>
                    @endif
                </tr>
                @php $counter ++ @endphp
            @endforeach
        @endforeach
        </tbody>
    </table>
</article>

