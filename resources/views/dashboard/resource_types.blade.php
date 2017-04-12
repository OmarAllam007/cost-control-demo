<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">Resources By Type</h4>
    </div>

    <div class="panel-body">
        <div class="row">
            <div class="col-sm-6">
                <div id="resourceTypesBudgetChart"></div>
            </div>
            <div class="col-sm-6">
                <div id="resourceTypesCostChart"></div>
            </div>
        </div>
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
        @foreach($resourceTypes as $resource)
            <tr>
                <td>{{$resource->resource_type}}</td>
                <td>{{number_format($resource->budget_cost ?: 0, 2)}}</td>
                <td>{{number_format($resource->actual_cost ?: 0, 2)}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@section('javascript')
    @php
        $resourceTypes = collect($resourceTypes);
        $budgetData = $resourceTypes->map(function($resource) { return [$resource->resource_type, $resource->budget_cost]; });
        $costData = $resourceTypes->map(function($resource) { return [$resource->resource_type, $resource->actual_cost]; });

    @endphp

    <script>
        var resourceTypeBudgetChart = c3.generate({
            bindto: '#resourceTypesBudgetChart',
            data: {
                columns: {!! $budgetData !!},
                type: 'pie',
            }
        });

        var resourceTypeCostChart = c3.generate({
            bindto: '#resourceTypesCostChart',
            data: {
                columns: {!! $costData !!},
                type: 'pie',
            }
        });
    </script>
@append