<div class="card-group-item">
    <h4 class="card-group-item-heading">SPI Trend Analysis</h4>

    <div class="chart"
         id="spiChart"
         data-type="line"
         data-labels="{{$spiTrend->pluck('name')}}"
         data-datasets="[{{ json_encode([
                                'label' => 'SPI', 'data' => $spiTrend->pluck('spi_index'),
                                'backgroundColor' => 'rgba(240, 255, 243, 0.6)',
                                'borderColor' => '#8ed3d8'
                            ]) }}]"
         style="height: 200px"></div>
</div>

