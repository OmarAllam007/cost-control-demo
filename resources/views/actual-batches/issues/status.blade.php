@php
    $activities = collect(json_decode($issue->data, true) ?: [])->map(function($log){
        if (isset($log['resource'])) {
            $attributes = $log['resource'];
        } else {
            $attributes = $log;
        }

        $log['resource'] = new \App\BreakDownResourceShadow($attributes);
        $log['resource']->id = $attributes['id'];
        return $log;
    })->groupBy(function($log) {
        if (isset($log['resource'])) {
            $resource = $log['resource'];
        } else {
            $resource = $log;
        }

        return $resource->wbs->path . ' / ' . $resource->activity;
    })->sortByKeys();
@endphp

@if ($activities->count())
    <article class="panel panel-warning">
        <div class="panel-heading">
            <h4 class="panel-title">Status</h4>
        </div>

        <div class="panel-body">
            @foreach($activities as $activity => $resources)
                <article class="panel panel-default">
                    <div class="panel-heading">
                        <h5 class="panel-title">{{$activity}}</h5>
                    </div>

                    <table class="table table-bordered table-condensed">
                        <thead>
                        <tr>
                            <th class="col-sm-2">Resource Code</th>
                            <th class="col-sm-4">Resource Name</th>
                            <th class="col-sm-1">Budget Unit</th>
                            <th class="col-sm-1">To date qty</th>
                            <th class="col-sm-1">Remaining Qty</th>
                            <th class="col-sm-1">Progress</th>
                            <th class="col-sm-2">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($resources as $log)
                            @php
                                if (isset($log['resource'])) {
                                    $resource = $log['resource'];
                                } else {
                                    $resource = $log;
                                }
                            @endphp
                            <tr>
                                <td>{{$resource->resource_code}}</td>
                                <td>{{$resource->resource_name}}</td>
                                <td>{{number_format($resource->budget_unit, 2)}}</td>
                                <td>{{number_format($log['to_date_qty']?? $resource->cost->to_date_qty ?? 0, 2)}}</td>
                                <td>{{number_format($log['remaining_qty']?? $resource->cost->remaining_qty ?? 0, 2)}}</td>
                                <td><strong>{{sprintf('%.02f', $resource->progress)}}%</strong></td>
                                <td><strong>{{$resource->status}}</strong></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </article>
            @endforeach
        </div>
    </article>
@endif