<article class="card-group-item">
    <div class="row">
        <div class="col-xs-{{$print? 12 : 6}}">
            <div class="br-1">
                <h4 class="card-group-item-heading">Cost Performance</h4>
                <div class="chart pie-chart"
                     id="costChart"
                     data-type="pie"
                     data-formatter="percent"
                     data-animate="{{$print? "false" : "true"}}"
                     data-labels="{{json_encode(['Actual Cost', 'Remaining Cost'])}}"
                     data-datasets="[{{ json_encode([
                                'label' => 'Cost Percentage', 'data' => [$actual_cost_percentage, $remaining_cost_percentage],
                                'backgroundColor' => ['rgba(38,89,137,.6)', 'rgba(214,117,53,.6)'],
                                'borderColor' => 'rgba(217, 225, 242, 0.2)'
                            ]) }}]"
                     style="height: 200px"></div>
            </div>
            </div>

        <div class="col-xs-{{$print? 12 : 6}}">
            <h4 class="card-group-item-heading">Physical performance</h4>
            <div class="chart"
                 id="progressChart"
                 data-type="horizontalBar"
                 data-formatter="percent"
                 data-labels="{{json_encode(['Actual Progress', 'Planned Progress'])}}"
                 data-datasets="[{{ json_encode([
                                'label' => '', 'data' => [$period->actual_progress, $period->planned_progress],//[$period->actual_progress, $period->planned_progress]
                                'backgroundColor' => ['rgba(38,89,137,.6)', 'rgba(214,117,53,.6)'],
                                'borderColor' => 'rgba(217, 225, 242, 0.6)'
                            ]) }}]"
                 style="height: 200px"></div>
        </div>
    </div>
</article>
