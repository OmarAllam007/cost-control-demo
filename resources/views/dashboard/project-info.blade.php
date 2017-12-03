<section class="card">
    <h3 class="card-title">Contracts Information</h3>

    <div class="card-body row">
        <div class="col-sm-4">
            <dl>
                <dt>Contracts Value</dt>
                <dd>{{number_format($contracts_info['contracts_total'], 2)}}</dd>
            </dl>
        </div>

        <div class="col-sm-4">
            <dl>
                <dt>Change Orders Value</dt>
                <dd>{{number_format($contracts_info['change_orders'], 2)}}</dd>
            </dl>
        </div>

        <div class="col-sm-4">
            <dl>
                <dt>Revised Contracts Value</dt>
                <dd>{{number_format($contracts_info['revised'], 2)}}</dd>
            </dl>
        </div>

        <div class="col-sm-4">
            <dl>
                <dt>Profit</dt>
                <dd>{{number_format($contracts_info['profit'], 2)}}</dd>
            </dl>
        </div>

        <div class="col-sm-4">
            <dl>
                <dt>Profitability</dt>
                <dd>{{number_format($contracts_info['profitability'], 2)}}%</dd>
            </dl>
        </div>

        <div class="col-sm-4">
            <dl>
                <dt>Expected Finish Date</dt>
                <dd>{{$contracts_info['finish_date']->format('d M Y') ?? ''}}</dd>
            </dl>
        </div>
    </div>
</section>