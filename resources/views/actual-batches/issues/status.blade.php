@php
    $activities = collect(json_decode($issue->data, true) ?: [])->map(function($row) {
        $resource = new \App\BreakDownResourceShadow($row);
        $resource->id = $row['id'];
        if (isset($row['cost'])) {
            $resource->import_cost = $row['import_cost'];
        }
        return $resource;
    })->groupBy(function(\App\BreakDownResourceShadow $resource) {
        return $resource->wbs->path . ' / ' . $resource->activity;
    })->sortByKeys();
@endphp

@if ($activities->count())
    <article class="panel panel-warning">
        <div class="panel-heading">
            <h4 class="panel-title">Progress</h4>
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
                        @foreach($resources as $resource)
                            <tr>
                                <td>{{$resource->resource_code}}</td>
                                <td>{{$resource->resource_name}}</td>
                                <td>{{$resource->budget_unit}}</td>
                                <td>{{$resource->import_cost['to_date_qty'] ?? 'N/A'}}</td>
                                <td>{{$resource->import_cost['remaining_qty'] ?? 'N/A'}}</td>
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