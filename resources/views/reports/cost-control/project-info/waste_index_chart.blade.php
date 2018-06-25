<div class="card-group-item">
    <h4 class="card-group-item-heading">Material Consumption Index Trend</h4>

    <div class="chart"
         id="wasteIndexChart"
         data-type="line"
         data-labels="{{$wasteIndexTrend->pluck('name')}}"
         data-datasets="[{{json_encode([
                                'label' => '', 'data' => $wasteIndexTrend->pluck('value'),
                                'backgroundColor' => 'rgba(217, 225, 242, 0.6)',
                                'borderColor' => 'rgba(0, 32, 96, 0.9)'
                            ])}}]"
         style="height: 200px"></div>
</div>