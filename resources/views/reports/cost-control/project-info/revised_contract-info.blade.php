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
                    <dt>Potential Change Order</dt>
                    <dd>{{number_format($period->potential_change_order_amount, 2)}}</dd>
                </dl>
            </article>

            <article class="col-xs-4">
                <dl>
                    <dt>Contract Value</dt>
                    <dd class="display-flex">
                        <span>{{number_format($period->contract_value, 2)}}</span>

                        @if ($period->contract_value < $project->project_contract_signed_value)
                            <span class="text-danger"><i class="fa fa-arrow-circle-down"></i></span>
                        @else
                            <span class="text-success"><i class="fa fa-arrow-circle-up"></i></span>
                        @endif
                    </dd>
                </dl>
            </article>

            <article class="col-xs-4">
                <dl>
                    <dt>Time Extension</dt>
                    <dd>{{$period->time_extension ?: '0'}} <small>(Days)</small></dd>
                </dl>
            </article>

            <article class="col-xs-4">
                <dl>
                    <dt>Duration</dt>
                    <dd class="display-flex">
                        <span>{{$period->expected_duration?:0}} <small>(Days)</small></span>

                        @if ($period->expected_duration >= $project->project_duration)
                            <span class="text-success"><i class="fa fa-arrow-circle-up"></i></span>
                        @else
                            <span class="text-danger"><i class="fa fa-arrow-circle-down"></i></span>
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