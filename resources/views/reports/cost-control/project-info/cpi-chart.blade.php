<section class="card-group-item">
    <h4 class="card-group-item-heading" >CPI Trend Analysis</h4>

    <div class="chart"
         id="cpiChart"
         data-type="line"
         data-labels="{{$cpiTrend->pluck('p_name')}}"
         data-datasets="[{{ json_encode([
                                'label' => 'CPI', 'data' => $cpiTrend->pluck('value'),
                                'backgroundColor' => 'rgba(217, 225, 242, 0.6)',
                                'borderColor' => 'rgba(0, 32, 96, 0.9)'
                            ]) }}]"
         style="height: 200px"></div>
</section>
