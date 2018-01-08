<article class="card-group-item">
    <h4 class="card-group-item-heading">Planned Revenue</h4>

    <div class="row">
    <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
        <div class="chart"
             id="wasteIndexChart"
             data-type="horizontalBar"
             data-labels="{{collect(['Planned Cost', 'Earned Value', 'Actual Invoice Value'])}}"
             data-datasets="[{{json_encode([
                                'label' => 'Planned Revenue',
                                'data' => [$period->planned_cost, $period->earned_value, $period->actual_invoice_amount],
                                'backgroundColor' => ['#8ed3d8', '#6CB2EB', "#E3342F"],
                                'datalabels' => ['align' => 'center']
                            ])}}]"
             style="height: 200px"></div>
    </div>
    </div>
</article>