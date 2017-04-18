@extends('layouts.app')

@section('header')
    <div class="clearfix">
        <h2 class="panel-title pull-left"><i class="fa fa-dashboard"></i> Dashboard</h2>
        <a href="{{route('project.cost-control', $project)}}" class="btn btn-default btn-sm pull-right"><i class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection


@section('body')
    <div id="dashboard">

        <chart-area inline-template v-for="chart in charts">
            <div class="chart-builder">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="type" class="control-label">Chart</label>
                                    <select name="type" id="type" class="form-control" v-model="type">
                                        <option value="">Select Chart</option>
                                        <option :value="key" v-for="(key, chart) in charts" v-text="chart.name"></option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-2" v-show="show_period">
                                <div class="form-group">
                                    <label for="type" class="control-label">Period</label>
                                    <select name="type" id="type" class="form-control" v-model="period">
                                        @foreach($periods as $id => $name)
                                            <option value="{{$id}}">{{$name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-sm-2" v-show="type">
                                <div class="form-group">
                                    <label for="filter" class="control-label">Filter By</label>
                                    <select name="filter" id="filter" v-model="filter" class="form-control">
                                        <option value="">[Overall]</option>
                                        <option value="activity">Activity</option>
                                        <option value="boq">BOQ</option>
                                        <option value="resource">Resource</option>
                                        <option value="resource_type">Resource Type</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-2" v-show="filter">
                                <div class="form-group">
                                    <label for="filters" class="control-label">Filters</label>
                                    <button id="filters" type="button" class="btn btn-info btn-block" @click="openFiltersModal"><i class="fa fa-check-square-o"></i> Select Filters</button>
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <div class="form-group" style="margin-top: 25px;">
                                    <button type="button" class="btn btn-primary btn-rounded" @click="run" :disabled="loading || !can_run"><i class="fa fa-play"></i> Run</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade filters-modal" tabindex="-1" role="dialog" v-if="filter">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                <h4 class="modal-title">Chart Filters</h4>
                            </div>
                            <div class="modal-body modal-scroll">
                                <activity v-if="filter === 'activity'"></activity>
                                <boq v-if="filter === 'boq'"></boq>
                                <resource v-if="filter === 'resource'"></resource>
                                <resource-type v-if="filter === 'resource_type'"></resource-type>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-primary" v-show="show_chart">
                    <div class="panel-body chart-container">
                        <div class="loading" v-show="loading">
                            <i class="fa fa-spinner fa-spin fa-3x"></i>
                        </div>
                        <div class="chart">

                        </div>
                    </div>
                </div>
            </div>
        </chart-area>

        <div class="form-group clearfix">
            <button class="btn btn-primary pull-right" @click="add_chart"><i class="fa fa-plus"></i> Add Chart</button>
        </div>
    </div>

@verbatim
<template id="activity-template">
    <section>
        <div class="form-group">
            <input type="search" v-model="term" class="form-control" placeholder="Search activity by name">
        </div>

        <table class="table table-bordered table-striped">
            <thead>
            <th>&nbsp;</th>
            <th>Activity</th>
            </thead>

            <tbody>
            <tr v-for="item in filtered">
                <td><input type="checkbox" value="{{item.id}}" :checked="selected.includes(id)" @change="toggleItem(item.id, $event)"></td>
                <td>{{item.name}}</td>
            </tr>
            </tbody>
        </table>
    </section>
</template>

<template id="boq-template">
    <section>
        <div class="form-group">
            <input type="search" v-model="term" class="form-control" placeholder="Search by cost account or description">
        </div>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>&nbsp;</th>
                <th>Code</th>
                <th>Cost Account</th>
                <th>Description</th>
            </tr>
            </thead>

            <tbody>
            <tr v-for="item in filtered">
                <td><input type="checkbox" value="{{item.id}}" :checked="selected.includes(id)" @change="toggleItem(item.id, $event)"></td>
                <td>{{item.wbs_code}}</td>
                <td>{{item.cost_account}}</td>
                <td>{{item.description}}</td>
            </tr>
            </tbody>
        </table>
    </section>
</template>
<template id="resource-template">
    <section>
        <div class="form-group">
            <input type="search" v-model="term" class="form-control" placeholder="Search by code or name">
        </div>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>&nbsp;</th>
                <th>Resource Code</th>
                <th>Resource Name</th>
            </tr>
            </thead>

            <tbody>
            <tr v-for="item in filtered">
                <td><input type="checkbox" value="{{item.id}}" :checked="selected.includes(id)" @change="toggleItem(item.id, $event)"></td>
                <td>{{item.code}}</td>
                <td>{{item.name}}</td>
            </tr>
            </tbody>
        </table>
    </section>
</template>

<template id="resource-type-template">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>&nbsp;</th>
            <th>Resource Type</th>
        </tr>
        </thead>

        <tbody>
        <tr v-for="item in items">
            <td><input type="checkbox" value="{{item.name}}" @change="toggleItem(item.name, $event)"></td>
            <td>{{item.name}}</td>
        </tr>
        </tbody>
    </table>
</template>
    @endverbatim
@endsection

@section('head')
    <link rel="stylesheet" href="{{asset('css/c3.min.css')}}">
@endsection

@section('javascript')
    <script>
        var activities = {!! $activities !!};
        var boqs = {!! $boqs !!};
        var resources = {!! $resources !!};
        var resourceTypes = {!! $resourceTypes !!};
        var period = {{session('period_id', $periods->keys()->first())}};
        var project_id = {{$project->id}};
    </script>
    <script src="/js/cost-dashboard.js"></script>
@endsection
