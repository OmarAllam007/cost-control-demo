<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">Top Activities</h4>
    </div>

    <div class="panel-body">
        <div id="activityChart"></div>
    </div>

    <table class="table table table-bordered table-condensed table-striped table-hovered">
        <thead>
        <tr>
            <th class="col-sm-6">Activity</th>
            <th class="col-sm-3">Budget Cost</th>
            <th class="col-sm-3">Actual Cost</th>
        </tr>
        </thead>

        <tbody>
        @foreach($topActivities as $activity)
            <tr>
                <td>{{$activity->activity}}</td>
                <td>{{number_format($activity->budget_cost ?: 0, 2)}}</td>
                <td>{{number_format($activity->actual_cost ?: 0, 2)}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>


</div>

@section('javascript')
    @php
        $topActivities = collect($topActivities);
        $budgetData = $topActivities->map(function($activity) { return $activity->budget_cost; })->prepend('Budget')->values();
        $costData = $topActivities->map(function($activity) { return $activity->actual_cost; })->prepend('Actual')->values();
        $labels = json_encode($topActivities->map(function($activity) { return $activity->activity; })->values());
        $chartData = json_encode([$budgetData, $costData]);
    @endphp

    <script>
        var activityChart = c3.generate({
            bindto: '#activityChart',
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