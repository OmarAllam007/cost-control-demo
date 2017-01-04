<div id="ProductivityArea" class="project-tab">
    <div class="form-group tab-actions clearfix">
        <div class="pull-right">
            <a href="{{route('productivity.export', ['project' => $project->id])}}" class="btn btn-info btn-sm">
                <i class="fa fa-cloud-download"></i> Export
            </a>
        </div>
    </div>
    
    @if ($project->productivities->count())
        @php
        $projectProductivities = App\Productivity::where('project_id', $project->id)->get()->keyBy('productivity_id');
        @endphp
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
            @foreach($project->productivities as $productivity)
                <tr>
                    <td class="col-xs-2">{{$productivity->csi_code}}</td>
                    <td class="col-xs-2">{{$productivity->description}}</td>
                    <td class="col-xs-2">{!! nl2br(e($productivity->crew_structure)) !!}}</td>
                    <td class="col-xs-2">{{isset($projectProductivities[$productivity->id])? $projectProductivities[$productivity->id]->after_reduction : $productivity->after_reduction}}</td>
                    <td class="col-xs-2">{{$productivity->units->type or ''}}</td>
                    <td class="col-xs-2">
                        @can('productivity', $project)
                            <a href="{{route('productivity.override', compact('project', 'productivity'))}}" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Override</a>
                        @endcan
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No productivity found</div>
    @endif
</div>