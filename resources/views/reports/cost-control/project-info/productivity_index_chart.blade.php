<div class="card-group-item">
    <h3 class="card-group-item-heading">Productivity Index</h3>

    <div class="panel-body">
        <div class="chart"
             id="cpiChart"
             data-type="line"
             data-labels="{{$productivityIndexTrend->pluck('name')}}"
             data-datasets="[{{json_encode([
                                'label' => 'Productivity Index', 'data' => $productivityIndexTrend->pluck('value'),//
                                  'backgroundColor' => 'rgba(151,153,155,0.7)',
                                'borderColor' => 'rgba(214,117,53,.7)'
                            ])}}]"
             style="height: 200px"></div>
    </div>
</div>