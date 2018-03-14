<div class="card-group-item">
    <h3 class="card-group-item-heading">Waste Index Trend</h3>

    <div class="chart"
         id="wasteIndexChart"
         data-type="line"
         data-labels="{{$wasteIndexTrend->pluck('p_name')}}"
         data-datasets="[{{json_encode([
                                'label' => 'Waste Index', 'data' => $wasteIndexTrend->pluck('value'),
                                  'backgroundColor' => '#5B9BD5',
                                'borderColor' => '#ED7D31'
                            ])}}]"
         style="height: 200px"></div>
</div>