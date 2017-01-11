<productivity project="{{$project->id}}" inline-template>
    <div id="ProductivityArea">
        <div class="form-group tab-actions clearfix">
            <div class="pull-right">
                <a href="{{route('productivity.export', ['project' => $project->id])}}" class="btn btn-info btn-sm">
                    <i class="fa fa-cloud-download"></i> Export
                </a>
            </div>
        </div>

        <section class="filters row">
            <div class="col-sm-3">
                <div class="form-group form-group-sm">
                    {{Form::label('productivity_code', 'Productivity Code', ['class' => 'control-label'])}}
                    {{Form::text('productivity_code', null /*session('filters.breakdown.' . $project->id . '.resource_code')*/,
                   ['class' => 'form-control', 'v-model' => 'code'])}}
                </div>
            </div>
        </section>

        <div class="scrollpane" v-if="filterd_productivity.length">
            <table class="table table-condensed table-striped table-fixed">
                <thead>
                <tr>
                    <th class="col-xs-2">Code</th>
                    <th class="col-xs-2">Description</th>
                    <th class="col-xs-2">Crew Structure</th>
                    <th class="col-xs-2">Productivity</th>
                    <th class="col-xs-2">Unit of measure</th>
                    <th class="col-xs-2">
                        @can('productivity', $project) Actions @endcan
                    </th>
                </tr>
                </thead>
                <tbody>
                {{--                @foreach($project->productivities as $productivity)--}}
                <tr v-for="productivity in filterd_productivity">
                    <td class="col-xs-2">@{{productivity.code}}</td>
                    <td class="col-xs-2">@{{productivity.description|nl2br}}</td>
                    <td class="col-xs-2" >@{{{productivity.crew_structure|nl2br}}} </td>
                    <td class="col-xs-2">@{{productivity.after_reduction}}</td>
                    <td class="col-xs-2">@{{productivity.unit}}</td>
                    <td class="col-xs-2">
                        @can('productivity', $project)
                            <a href="/productivity/override/@{{productivity.id}}/{{$project->id}}" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Override</a>
                        @endcan
                    </td>
                </tr>
                {{--@endforeach--}}
                </tbody>
            </table>
        </div>
        {{--@else--}}
        <div class="alert alert-warning" v-else><i class="fa fa-exclamation-triangle"></i> No productivity found</div>
        {{--@endif--}}
    </div>
</productivity>
