<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">Projects Summary</h4>
    </div>

    <div class="panel-body">
        <div id="projectChart"></div>
    </div>

    <table class="table table-condensed table-striped table-hover table-bordered">
        <thead>
        <tr>
            <th class="col-xs-6">Project</th>
            <th class="col-xs-3">Budget Cost</th>
            <th class="col-xs-3">Actual Cost</th>
        </tr>
        </thead>
        <tbody>
        @foreach($projectNames as $id => $project)
            <tr>
                <td>{{$project}}</td>
                <td>{{number_format($projectStats[$id]['budget_cost'], 2)}}</td>
                <td>{{number_format($projectStats[$id]['actual_cost'], 2)}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@section('javascript')
    @php
        $chartRows = [['Budget', 'Actual']];
        $labels = [];
        foreach ($projectNames as $id => $project) {
            $chartRows[] = [
                $projectStats[$id]['budget_cost'], $projectStats[$id]['actual_cost']
            ];
            $labels[] = $project;
        }
    @endphp

    <script>
        var resourceChart = c3.generate({
            bindto: '#projectChart',
            data: {
                rows: {!! json_encode($chartRows) !!},
                type: 'bar',
            },
            axis: {
                x: {
                    type: 'category',
                    categories: {!! json_encode($labels)!!},
                    tick: {
                        rotate: 75
                    }
                }
            }
        });
    </script>
@append