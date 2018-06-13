<section class="card-group-item">
    <h4 class="card-title card-group-item-heading">Contracts Information</h4>

    <section class="card-body">
        <div class="display-flex">

            <div class="flex mr-10">
                <dl>
                    <dt>Contracts Value</dt>
                    <dd>{{number_format($contracts_info['contracts_total'], 2)}}</dd>
                </dl>
            </div>

            <div class="flex mr-10">
                <dl>
                    <dt>Change Orders Value</dt>
                    <dd>{{number_format($contracts_info['change_orders'], 2)}}</dd>
                </dl>
            </div>

            <div class="flex">
                <dl>
                    <dt>Rev. Contracts</dt>
                    <dd>{{number_format($contracts_info['revised'], 2)}}</dd>
                </dl>
            </div>
        </div>

        <div class="display-flex">
            <div class="flex mr-10">
                <dl>
                    <dt>Initial Profit</dt>
                    <dd>{{number_format($contracts_info['profit'], 2)}}</dd>
                </dl>
            </div>

            <div class="flex mr-10">
                <dl>
                    <dt>Initial Profitability</dt>
                    <dd>{{number_format($contracts_info['profitability'], 2)}}%</dd>
                </dl>
            </div>

            <div class="flex">
                <dl>
                    <dt>Expected Finish Date</dt>
                    <dd>{{$contracts_info['finish_date']->format('d M Y') ?? ''}}</dd>
                </dl>
            </div>
        </div>



        <h4 class="card-title card-group-item-heading" style="text-decoration: underline">Projects Information</h4>
        <table class="table table-condensed table-bordered table-striped">
            <thead>
            <tr class="bg-primary">
                <th>Project</th>
                <th>Planned Start (PS)</th>
                <th>Revised Duration (Days)</th>
                <th>Revised Finish Date</th>
                <th>Forecast Finish Date</th>
                <th>Delay Variance</th>
                <th>Planned Progress</th>
                <th>Actual Progress</th>
                <th class="br-2">SPI</th>

                <th>Allowable Cost</th>
                <th>Actual cost to date</th>
                <th>Variance</th>
                <th>CPI</th>
            </tr>
            </thead>

            <tbody>
                @foreach($contracts_info['schedules'] as $project)
                    <tr>
                        <td title="{{$project->project_name}}">{{str_limit($project->project_name, 40)}}</td>
                        <td>{{$project->planned_start}}</td>
                        <td>{{$project->original_duration}}</td>
                        <td>{{$project->planned_finish}}</td>
                        <td>{{$project->forecast_finish}}</td>
                        <td class="{{$project->delay_variance < 0? 'text-danger' : 'text-success'}}">
                            {{$project->delay_variance}}
                        </td>
                        <td>{{number_format($project->planned_progress, 2)}}%</td>
                        <td>{{number_format($project->actual_progress, 2)}}%</td>
                        <td class="{{$project->spi_index < 1 ? 'text-danger' : 'text-success'}}">{{number_format($project->spi_index, 2)}}</td>

                        <td>{{number_format($project->allowable_cost, 2)}}</td>
                        <td>{{number_format($project->to_date_cost, 2)}}</td>
                        <td class="{{$project->variance < 0 ? 'text-danger' : 'text-success'}}">{{number_format($project->variance, 2)}}</td>
                        <td class="{{$project->cpi < 1 ? 'text-danger' : 'text-success'}}">{{number_format($project->cpi, 2)}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</section>