<article class="card-group-item">
    <h4 class="card-group-item-heading">Revenue Statement</h4>

    <div class="row">
    <div class="col-md-12">
        <div class="chart"
             id="wasteIndexChart"
             data-type="horizontalBar"
             data-labels="{{collect(['Planned Value', 'Earned Value', 'Actual Invoice Value'])}}"
             data-datasets="[{{json_encode([
                                'label' => '',
                                'data' => [$period->planned_value, $period->earned_value, $period->actual_invoice_value],
                                'backgroundColor' => ['rgba(65,108,182,0.6)', 'rgba(104,160,72,0.6)', "rgba(214,117,53,.7)"],
                                   'borderColor' => '#5B9BD5',
                                'datalabels' => ['align' => 'center']
                            ])}}]"
             style="height: 200px"></div>
    </div>
    </div>
</article>

