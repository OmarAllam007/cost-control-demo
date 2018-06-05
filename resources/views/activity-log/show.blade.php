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
                            <option value="important">Driving Only</option>
                        </select>
                    </article>
                </div>
            </div>
        </section>

        <section class="data">
            <section class="logs" v-if="logs.length">
                <div v-for="resource in filteredLogs">
                    <resource-log v-if="!resource.rollup" :resource="resource" inline-template></resource-log>
                    <rolled-resource-log v-if="resource.rollup" :resource="resource" inline-template></rolled-resource-log>
                </div>

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
        var is_activity_rollup = {{ $is_activity_rollup? 'true' : 'false'  }};
    </script>

    <script src="{{asset('js/activity-log.js')}}"></script>
@endsection