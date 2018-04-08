<div class="card-group-item">
    <h4 class="card-group-item-heading">Schedule Performance Index (SPI) Trend</h4>

    <div class="chart"
         id="spiChart"
         data-type="line"
         data-labels="{{$spiTrend->pluck('name')}}"
         data-datasets="[{{ json_encode([
                                'label' => 'SPI', 'data' => $spiTrend->pluck('spi_index'),//
                                 'backgroundColor' => 'rgba(217, 225, 242, 0.6)',
                                'borderColor' => 'rgba(0, 32, 96, 0.9)'
                            ]) }}]"
         style="height: 200px"></div>
</div>

