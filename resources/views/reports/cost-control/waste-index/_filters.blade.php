<form action="" class="panel panel-default">
    <div class="panel-body">
        <div class="col-sm-2 form-group">
            {{Form::label('period', null, ['class' => 'control-label'])}}
            {{Form::select('period', \App\Period::where('project_id',$project->id)->readyForReporting()->pluck('name','id'), Session::get('period_id_'.$project->id),  ['placeholder' => 'Choose a Period','class'=>'form-control padding'])}}
        </div>

        <div class="col-sm-3 form-group">
            <label for="type">Resource Division</label>
            <a href="#ResourceTypeModal" data-toggle="modal" class="btn btn-default btn-block">Select Resource Type</a>
        </div>

        <div class="col-sm-3 form-group">
            <label for="resourceFilter">Resource</label>
            <input class="form-control" type="search" name="resource" placeholder="Search by code or name" value="{{request('resource')}}" id="resourceFilter">
        </div>

        <div class="col-sm-2 checkbox">
            <label style="margin-top: 25px;">
                <input type="checkbox" name="negative" id="negative">
                Negative variance
            </label>
        </div>

        <div class="col-sm-2 form-group text-right" style="padding-top: 25px;">
            <button class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
            <a href="{{request()->url()}}" class="btn btn-default"><i class="fa fa-refresh"></i> Reset</a>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="ResourceTypeModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        <h4 class="modal-title">Select Resource Type</h4>
                    </div>
                    <div class="modal-body">
                        <ul class="tree list-unstyled">
                            @php $resourceTypes = (new App\Support\ResourceTypesTree())->get()->get(3); @endphp
                            @foreach($resourceTypes->subtree as $type)
                                @include('reports.cost-control.waste-index._recursive_material', compact('type'))
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@section('javascript')
    <script>
        $(function() {
            $('#ResourceTypeModal').on('change', 'input', function() {
                $(this).closest('li').find('input:checkbox').prop('checked', this.checked);
            });
        });
    </script>
@append