<section class="card-group-item">
    <h4 class="card-title card-group-item-heading">Revised Contract information</h4>
    <section class="card-body">
        <div class="row">
            <article class="col-xs-4">
                <dl>
                    <dt>Change Order Value</dt>
                    <dd>{{number_format($period->change_order_amount, 2)}}</dd>
                </dl>
            </article>

            <article class="col-xs-4">
                <dl>
                    <dt>Time Extension</dt>
                    <dd>{{$period->time_extension ?: '0 Days'}}</dd>
                </dl>
            </article>

            <article class="col-xs-4">
                <dl>
                    <dt>Planned Start Date</dt>
                    <dd>{{ \Carbon\Carbon::parse($project->project_start_date)->format('d M Y') }}</dd>
                </dl>
            </article>

            <article class="col-xs-4">
                <dl>
                    <dt>Contract Value</dt>
                    <dd class="display-flex">
                        <span>{{number_format($period->contract_value, 2)}}</span>

                        @if ($period->contract_value > $project->project_contract_signed_value)
                            <span class="text-success"><i class="fa fa-arrow-circle-up"></i></span>
                        @else
                            <span class="text-danger"><i class="fa fa-arrow-down"></i></span>
                        @endif
                    </dd>
                </dl>
            </article>

            <article class="col-xs-4">
                <dl>
                    <dt>Duration (Days)</dt>
                    <dd class="display-flex">
                        <span>{{$period->project_duration ?: '0 Days'}}</span>

                        @if ($period->project_duration > $project->project_duration)
                            <span class="text-success"><i class="fa fa-arrow-up"></i></span>
                        @else
                            <span class="text-danger"><i class="fa fa-arrow-down"></i></span>
                        @endif
                    </dd>
                </dl>
            </article>

            <article class="col-xs-4">
                <dl>
                    <dt>Planned Finish Date</dt>
                    <dd>{{$period->planned_finish_date->format('d M Y')}}</dd>
                </dl>
            </article>
        </div>
    </section>
</section>