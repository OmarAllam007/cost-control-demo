<article class="card-group-item">
    <h4 class="card-group-item-heading">Revenue Statement</h4>

    <div class="row">
    <div class="col-md-12">
        <div class="chart"
             id="wasteIndexChart"
             data-type="horizontalBar"
             data-labels="{{collect(['Planned Value', 'Earned Value', 'Actual Invoice Value'])}}"
             data-datasets="[{{json_encode([
                                'label' => 'Revenue Statement',
                                'data' => [$period->planned_cost, $period->earned_value, $period->actual_invoice_amount],
                                'backgroundColor' => ['#169ec0', '#E0F8FF', "#A0F0ED"],
                                'datalabels' => ['align' => 'center']
                            ])}}]"
             style="height: 200px"></div>
    </div>
    </div>
</article>

