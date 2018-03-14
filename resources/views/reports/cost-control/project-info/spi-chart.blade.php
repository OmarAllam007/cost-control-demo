<div class="card-group-item">
    <h3 class="card-group-item-heading">SPI Trend Analysis</h3>

    <div class="chart"
         id="spiChart"
         data-type="line"
         data-labels="{{$spiTrend->pluck('name')}}"
         data-datasets="[{{ json_encode([
                                'label' => 'SPI', 'data' => $spiTrend->pluck('spi_index'),
                                 'backgroundColor' => '#E0F8FF',
                                'borderColor' => '#169ec0'
                            ]) }}]"
         style="height: 200px"></div>
</div>

