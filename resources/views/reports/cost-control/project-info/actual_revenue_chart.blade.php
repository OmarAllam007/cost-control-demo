<article class="card-group-item">
    <h4 class="card-group-item-heading">Actual Revenue</h4>

    <div class="chart"
         id="wasteIndexChart"
         data-type="bar"
         data-labels="{{$actualRevenue->pluck('name')}}"
         data-datasets="[{{json_encode([
                                'label' => 'Waste Index', 'data' => $actualRevenue->pluck('value'),
                                'backgroundColor' => '#8ed3d8'
                            ])}}]"
         style="height: 200px"></div>
</article>