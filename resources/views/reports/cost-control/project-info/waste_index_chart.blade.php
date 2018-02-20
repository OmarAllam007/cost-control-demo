<div class="card-group-item">
    <h4 class="card-group-item-heading">Waste Index Trend</h4>

    <div class="chart"
         id="wasteIndexChart"
         data-type="line"
         data-labels="{{$wasteIndexTrend->pluck('p_name')}}"
         data-datasets="[{{json_encode([
                                'label' => 'Waste Index', 'data' => $wasteIndexTrend->pluck('value'),
                                  'backgroundColor' => '#9ddcec',
                                'borderColor' => '#169ec0'
                            ])}}]"
         style="height: 200px"></div>
</div>