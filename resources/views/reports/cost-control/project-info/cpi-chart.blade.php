<article class="card-group-item">
    <h4 class="card-group-item-heading">CPI Trend Analysis</h4>

    <div class="chart"
         id="cpiChart"
         data-type="line"
         data-labels="{{$cpiTrend->pluck('p_name')}}"
         data-datasets="[{{ json_encode([
                                'label' => 'CPI', 'data' => $cpiTrend->pluck('value'),
                                'backgroundColor' => '#F0FFF3',
                                'borderColor' => '#8ed3d8'
                            ]) }}]"
         style="height: 200px"></div>
</article>
