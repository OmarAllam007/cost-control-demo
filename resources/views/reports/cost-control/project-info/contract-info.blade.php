<section class="card-group-item">
    <h4 class="card-title card-group-item-heading">Contract information</h4>
    <section class="card-body">
        <div class="row">
            <article class="col-xs-4">
                <dl>
                    <dt>Contract Value</dt>
                    <dd>{{number_format($project->project_contract_signed_value, 2)}}</dd>
                </dl>
            </article>

            <article class="col-xs-4">
                <dl>
                    <dt>Duration</dt>
                    <dd>{{$project->project_duration?:0}} <small>(Days)</small></dd>
                </dl>
            </article>

            <article class="col-xs-4">
                <dl>
                    <dt>Planned Start Date</dt>
                    <dd>{{$project->project_start_date? \Carbon\Carbon::parse($project->project_start_date)->format('d M Y') : ''}}</dd>
                </dl>
            </article>

            <article class="col-xs-4">
                <dl>
                    <dt>Initial Profit</dt>
                    <dd>{{number_format($project->tender_initial_profit, 2)}}</dd>
                </dl>
            </article>

            <article class="col-xs-4">
                <dl>
                    <dt>Initial Profitability</dt>
                    <dd>{{number_format($project->tender_initial_profitability_index, 2)}}%</dd>
                </dl>
            </article>

            <article class="col-xs-4">
                <dl>
                    <dt>Planned Finish Date</dt>
                    <dd>{{$project->expected_finish_date? \Carbon\Carbon::parse($project->expected_finish_date)->format('d M Y') : ''}}</dd>
                </dl>
            </article>
        </div>
    </section>
</section>