@php
    $activities = collect(json_decode($issue->data, true));
    $wbs_ids = $activities->pluck('newRows.*.resource.wbs_id')->flatten()->unique();
    $levels = \App\WbsLevel::find($wbs_ids->toArray())->keyBy('id')->map(function($level) {
        return $level->path;
    });

    $activities = $activities->groupBy(function($row) use ($levels) {
        $resource = $row['newRows'][0]['resource'];
        return $levels[$resource['wbs_id']] . ' &mdash; ' . $resource['activity'];
    });
@endphp

@if ($activities->count())
    <article class="panel panel-warning">
        <div class="panel-heading">
            <h4 class="panel-title">Multiple Cost Account</h4>
        </div>

        <div class="panel-body">
            @foreach($activities as $name => $data)
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">{{$name}}</h4>
                    </div>

                    <table class="table table-condensed table-bordered">
                        <thead>
                        <tr>
                            <th>Store Resource Code</th>
                            <th>Store Resource Name</th>
                            <th>Store Qty</th>
                            <th>Store Unit Price</th>
                            <th>Store Cost</th>
                            <th>Cost Account</th>
                            <th>Budget Unit</th>
                            <th>Qty</th>
                            <th>Price/Unit</th>
                            <th>Cost</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $resource)
                            @php
                                $oldRow = $resource['oldRow'];
                                $newRows = $resource['newRows'];
                                $rowSpan = count($newRows);
                            @endphp
                            <tr>
                                <td rowspan="{{$rowSpan}}">{{$oldRow[2]}}</td>
                                <td rowspan="{{$rowSpan}}">{{$oldRow[7]}}</td>
                                <td rowspan="{{$rowSpan}}">{{number_format($oldRow[4], 2)}}</td>
                                <td rowspan="{{$rowSpan}}">{{number_format($oldRow[5], 2)}}</td>
                                <td rowspan="{{$rowSpan}}">{{number_format($oldRow[6], 2)}}</td>
                                @php
                                    $firstRow = array_shift($newRows);
                                @endphp
                                <td>{{$firstRow['resource']['cost_account']}}</td>
                                <td>{{$firstRow['resource']['budget_unit']}}</td>
                                <td>{{number_format($firstRow[4], 2)}}</td>
                                <td>{{number_format($firstRow[5], 2)}}</td>
                                <td>{{number_format($firstRow[6], 2)}}</td>
                            </tr>
                            @foreach($newRows as $row)
                                <tr>
                                    <td>{{$row['resource']['cost_account']}}</td>
                                    <td>{{$row['resource']['budget_unit']}}</td>
                                    <td>{{number_format($row[4], 2)}}</td>
                                    <td>{{number_format($row[5], 2)}}</td>
                                    <td>{{number_format($row[6], 2)}}</td>
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </article>
@endif