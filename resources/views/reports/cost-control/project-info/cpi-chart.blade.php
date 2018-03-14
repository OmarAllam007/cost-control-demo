<section class="card-group-item">
    <h3 class="card-group-item-heading" >CPI Trend Analysis</h3>

    <div class="chart"
         id="cpiChart"
         data-type="line"
         data-labels="{{$cpiTrend->pluck('p_name')}}"
         data-datasets="[{{ json_encode([
                                'label' => 'CPI', 'data' => $cpiTrend->pluck('value'),
                                'backgroundColor' => '#5B9BD5',
                                'borderColor' => '#ED7D31'
                            ]) }}]"
         style="height: 200px"></div>
</section>
