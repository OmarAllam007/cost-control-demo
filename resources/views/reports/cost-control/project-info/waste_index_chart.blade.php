<div class="card-group-item">
    <h4 class="card-group-item-heading">Waste Index</h4>

    <div class="chart"
         id="wasteIndexChart"
         data-type="line"
         data-labels="{{$wasteIndex->pluck('p_name')}}"
         data-datasets="[{{json_encode([
                                'label' => 'Waste Index', 'data' => $wasteIndex->pluck('value'),
                                'backgroundColor' => 'rgba(240, 255, 243, 0.6)',
                                'borderColor' => '#8ed3d8'
                            ])}}]"
         style="height: 200px"></div>
</div>