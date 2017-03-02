<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">Top Resources</h4>
    </div>

    <div class="panel-body">
        <div id="resourcesChart"></div>
    </div>

    <table class="table table table-bordered table-condensed table-striped table-hovered">
        <thead>
        <tr>
            <th class="col-sm-6">Resource</th>
            <th class="col-sm-3">Budget Cost</th>
            <th class="col-sm-3">Actual Cost</th>
        </tr>
        </thead>

        <tbody>
        @foreach($topResources as $resource)
            <tr>
                <td>{{$resource->resource_name}}</td>
                <td>{{number_format($resource->budget_cost ?: 0, 2)}}</td>
                <td>{{number_format($resource->actual_cost ?: 0, 2)}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@section('javascript')
    @php
        $topResources = collect($topResources);
        $budgetData = $topResources->map(function($resource) { return $resource->budget_cost; })->prepend('Budget')->values();
        $costData = $topResources->map(function($resource) { return $resource->actual_cost; })->prepend('Actual')->values();
        $labels = json_encode($topResources->map(function($resource) { return $resource->resource_name; })->values());
        $chartData = json_encode([$budgetData, $costData]);
    @endphp

    <script>
        var resourceChart = c3.generate({
            bindto: '#resourcesChart',
            data: {
                columns: {!! $chartData !!},
                type: 'bar',
            },
            axis: {
                x: {
                    type: 'category',
                    categories: {!! $labels !!},
                    tick: {
                        rotate: 75
                    }
                }
            }
        });
    </script>
@append