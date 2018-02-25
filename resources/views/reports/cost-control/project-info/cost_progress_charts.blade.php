<article class="card-group-item">
    <div class="row">
        <div class="col-xs-6">
            <div class="br-1">
                <h3 class="card-group-item-heading">Cost Percentage</h3>
                <div class="chart"
                     id="costChart"
                     data-type="pie"
                     data-labels="{{json_encode(['Actual Cost', 'Remaining Cost'])}}"
                     data-datasets="[{{ json_encode([
                                'label' => 'Cost Percentage', 'data' => [$actual_cost_percentage, $remaining_cost_percentage],
                                  'backgroundColor' => ['#ED7D31', '#169ec0'],
                                    'borderColor' => '#5B9BD5'

                            ]) }}]"
                     style="height: 200px"></div>
            </div>
            </div>

        <div class="col-xs-6">
            <h4 class="card-group-item-heading">Progress Percentage</h4>
            <div class="chart"
                 id="progressChart"
                 data-type="horizontalBar"
                 data-labels="{{json_encode(['Actual Progress', 'Planned Progress'])}}"
                 data-datasets="[{{ json_encode([
                                'label' => 'Progress Percentage', 'data' => [$period->actual_progress, $period->planned_progress],
                                 'backgroundColor' => ['#5B9BD5', '#169ec0'],
                                 'borderColor' => '#ED7D31'
                            ]) }}]"
                 style="height: 200px"></div>
        </div>
    </div>
</article>
