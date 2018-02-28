<section class="card-group-item">
    <h3 class="card-title card-group-item-heading">Contracts Information</h3>

    <section class="card-body">
        <div class="row">

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
                    <dt>Initial Profit</dt>
                    <dd>{{number_format($contracts_info['profit'], 2)}}</dd>
                </dl>
            </div>

            <div class="col-sm-4">
                <dl>
                    <dt>Initial Profitability</dt>
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



        <h4 class="card-title card-group-item-heading" style="text-decoration: underline">Project Schedule</h4>
        <table class="table table-condensed table-bordered">
            <thead>
            <tr class="bg-primary">
                <th>Project</th>
                <th>Planned Start (PS)</th>
                <th>Original Duration (Days)</th>
                <th class="br-2">Planned Finish Date (PF)</th>
                <th>Actual Start</th>
                <th>Expected Duration (Days)</th>
                <th>Forecast Finish</th>
                <th>Delay Variance</th>
            </tr>
            </thead>

            <tbody>
                @foreach($contracts_info['schedules'] as $schedule)
                    <tr>
                        <td>{{$schedule->project_name}}</td>
                        <td>{{$schedule->planned_start}}</td>
                        <td>{{$schedule->original_duration}}</td>
                        <td>{{$schedule->planned_finish}}</td>
                        <td>{{$schedule->actual_start}}</td>
                        <td>{{$schedule->expected_duration}}</td>
                        <td>{{$schedule->forecast_finish}}</td>
                        <td class="{{$schedule->delay_variance < 0? 'text-danger' : 'text-success'}}">
                            {{$schedule->delay_variance}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</section>