@extends('layouts.app')

@section('header')
    <div class="display-flex">
        <h2 class="flex">{{$wbs->project->name}} &mdash; Activity Log</h2>

        <div class="btn-tollbar">
            <a href="{{route('activity-log.excel', [$wbs, $code])}}" class="btn btn-success btn-sm">
                <i class="fa fa-cloud-download"></i> Export
            </a>

            <a href="{{route('project.cost-control', $wbs->project)}}" class="btn btn-default btn-sm">
                <i class="fa fa-chevron-left"></i> Back to Project
            </a>
        </div>

    </div>
@endsection

@section('body')
    <h4 class="page-header">{{$wbs->path}}</h4>
    <div id="activityLog">
        <section class="info-box card">
            <div class="card-body">
                <div class="row mb-5">
                    <div class="col-sm-4">
                        <dl>
                            <dt>Activity Name</dt>
                            <dd>{{$activity_name}}</dd>
                        </dl>
                    </div>

                    <div class="col-sm-4">
                        <dl>
                            <dt>Budget Cost</dt>
                            <dd>{{number_format($budget_cost, 2)}}</dd>
                        </dl>
                    </div>
                    <div class="col-sm-4">
                        <dl>
                            <dt>First Upload Date</dt>
                            <dd>{{$first_upload->format('d M Y')}}</dd>
                        </dl>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-sm-4">
                        <dl>
                            <dt>Activity ID</dt>
                            <dd>{{$code}}</dd>
                        </dl>
                    </div>

                    <div class="col-sm-4">
                        <dl>
                            <dt>Actual Cost</dt>
                            <dd>{{number_format($actual_cost, 2)}}</dd>
                        </dl>
                    </div>

                    <div class="col-sm-4">
                        <dl>
                            <dt>Last Upload Date</dt>
                            <dd>{{$last_upload->format('d M Y')}}</dd>
                        </dl>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-4">
                        <dl>
                            <dt>Status</dt>
                            <dd>{{$status}}</dd>
                        </dl>
                    </div>
                    <div class="col-sm-4">
                        <dl>
                            <dt>Variance</dt>
                            <dd>{{number_format($variance, 2)}}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </section>

        <section class="filters card">
            <div class="card-body">
                <div class="row">
                    <article class="form-group col-sm-3">
                        <input type="search" id="ResourceSearch" class="form-control input-sm"
                               placeholder="Search by Resource" v-model="resource_search">
                    </article>

                    <article class="form-group col-sm-3">
                        <select id="PeriodId" class="form-control input-sm" v-model="resource_mode">
                            <option value="all">All Resources</option>
                            <option value="important">Important Only</option>
                        </select>
                    </article>
                </div>
            </div>
        </section>

        <section class="data">
            <section class="logs" v-if="logs.length">

                <resource-log v-for="resource in filteredLogs" :resource="resource" inline-template>
                    <article class="card">
                        <h4 class="card-title">
                            <span v-text="resource.name"></span> &mdash;
                            <span class="text-muted text-capitalize" v-text="resource.code"></span>
                        </h4>

                        <div class="card-body" :class="{highlight: important}">
                            <table class="table table-bordered table-condensed">
                                <thead>
                                <tr class="info">
                                    <th class="text-center" colspan="4">Budget</th>
                                    <th class="text-center" colspan="5">Actual</th>
                                </tr>
                                <tr class="info">
                                    <th width="11%" class="text-center">Unit Price</th>
                                    <th width="11%" class="text-center">Budget Unit</th>
                                    <th width="11%" class="text-center">Amount</th>
                                    <th width="11%" class="text-center">U.O.M</th>

                                    <th width="11%" class="text-center">Equiv. Unit Price</th>
                                    <th width="11%" class="text-center">Qty</th>
                                    <th width="11%" class="text-center">Amount</th>
                                    <th width="11%" class="text-center">Qty Var.</th>
                                    <th width="11%" class="text-center">Cost Var.</th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr>
                                    <td class="text-center" v-text="first.unit_price|number_format"></td>
                                    <td class="text-center" v-text="budget_unit|number_format"></td>
                                    <td class="text-center" v-text="budget_cost|number_format"></td>
                                    <td class="text-center" v-text="first.measure_unit"></td>

                                    <td class="text-center" v-text="actual_unit_price|number_format"></td>
                                    <td class="text-center" v-text="actual_qty|number_format"></td>
                                    <td class="text-center" v-text="actual_cost|number_format"></td>
                                    <td class="text-center" :class="qty_var < 0? 'text-danger' : 'text-success'" v-text="qty_var|number_format"></td>
                                    <td class="text-center" :class="cost_var < 0? 'text-danger' : 'text-success'" v-text="cost_var|number_format"></td>
                                </tr>
                                </tbody>
                            </table>

                            <div class="row">
                                <article class="col-sm-3">
                                    <table class="table table-striped table-condensed">
                                        <thead>
                                        <tr>
                                            <th class="text-center table-caption" colspan="5">Budget</th>
                                        </tr>
                                        <tr>
                                            <th>Budget Unit</th>
                                            <th>Unit Price</th>
                                            <th>Amount</th>
                                            <th>Cost Account</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="budget_resource in resource.budget_resources">
                                            <td v-text="budget_resource.budget_unit|number_format"></td>
                                            <td v-text="budget_resource.unit_price|number_format"></td>
                                            <td v-text="budget_resource.budget_cost|number_format"></td>
                                            <td v-text="budget_resource.cost_account"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </article>

                                <article class="col-sm-9 bl-1">
                                    <table class="table table-striped table-condensed">
                                        <thead>
                                        <tr>
                                            <th class="text-center table-caption" colspan="10">Actual</th>
                                        </tr>
                                        <tr>
                                            <th>Resource ID</th>
                                            <th>Resource Name</th>
                                            <th>UOM</th>
                                            <th>Unit Price</th>
                                            <th>Qty</th>
                                            <th>Amount</th>
                                            <th>Date from store</th>
                                            <th>Date uploaded</th>
                                            <th>Reference</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <tr v-for="actual_resource in resource.store_resources">
                                            <td v-text="actual_resource.item_code"></td>
                                            <td v-text="actual_resource.item_desc"></td>
                                            <td v-text="actual_resource.measure_unit"></td>
                                            <td v-text="actual_resource.unit_price|number_format"></td>
                                            <td v-text="actual_resource.qty|number_format"></td>
                                            <td v-text="actual_resource.cost|number_format"></td>
                                            <td v-text="actual_resource.store_date"></td>
                                            <td>
                                                <a class="in-iframe"
                                                   :href="`/actual-batches/${actual_resource.batch_id}`"
                                                   v-text="actual_resource.created_at">
                                                </a>
                                            </td>
                                            <td>
                                                <a :href="`/actual-batches/${actual_resource.batch_id}/download`"
                                                   v-text="actual_resource.doc_no">
                                                </a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </article>
                            </div>
                        </div>
                    </article>
                </resource-log>

            </section>

            <section class="loading" v-if="loading"><i class="fa fa-spinner fa-spin fa-3x"></i></section>
        </section>
    </div>
@endsection

@section('css')
    <style>
        .loading {
            height: 100vh;
            background: rgba(255, 255, 255, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            align-content: center;
        }

        .loading .fa {
            display: block;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .card .form-group {
            margin-bottom: 0;
        }

        h4.page-header {
            margin: 10px 0;
        }

        .bl-1 {
            border-left: 1px solid #e5e5e5;
        }
    </style>
@endsection

@section('javascript')
    <script>
        var wbs_id = {{$wbs->id}};
        var code = "{{$code}}";
    </script>

    <script src="/js/activity-log.js"></script>
@endsection