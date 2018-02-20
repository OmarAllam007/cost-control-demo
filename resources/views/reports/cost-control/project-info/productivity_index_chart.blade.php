<div class="card-group-item">
    <h4 class="card-group-item-heading">Productivity Index</h4>

    <div class="panel-body">
        <div class="chart"
             id="cpiChart"
             data-type="line"
             data-labels="{{$productivityIndexTrend->pluck('name')}}"
             data-datasets="[{{json_encode([
                                'label' => 'Productivity Index', 'data' => $productivityIndexTrend->pluck('value'),
                                  'backgroundColor' => '#9ddcec',
                                'borderColor' => '#169ec0'
                            ])}}]"
             style="height: 200px"></div>
    </div>
</div>