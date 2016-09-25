{{-- Filter Form --}}
{{Form::open(['route' => 'resources.filter', 'class' => 'row filter-form'])}}
<div class="col-sm-3">
    <div class="form-group-sm">
        <label class="control-label" for="resourceName">Resource Name</label>
        <input type="text" id="resourceName" name="name" class="form-control" value="{{session('filters.resources.name')}}">
    </div>
</div>

<div class="col-sm-3">
    <div class="form-group-sm">
        <label for="ResourceType">Resource Type</label>
        <p id="resourceTypeContainer">
            <a href="#ResourceTypeModal" data-toggle="modal" id="selectType">
                @if ($type = Session::get('filters.resources.resource_type_id'))
                    {{App\ResourceType::find($type)->name}}
                @else
                    Select Type
                @endif
            </a>
            <a href="#" class="text-danger reset-modal-input" id="resetType"> <i class="fa fa-times-circle"></i></a>
        </p>
    </div>
</div>

<div class="col-sm-2">
    <div class="form-group-sm">
        <label class="control-label" for="resourceCode">Resource Code</label>
        <input type="text" id="resourceCode" name="resource_code" class="form-control" value="{{session('filters.resources.resource_code')}}">
    </div>
</div>

<div class="col-sm-3">
    <div class="form-group-sm">
        <button class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button>
    </div>
</div>

<div id="ResourceTypeModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Select Parent</h4>
            </div>
            <div class="modal-body">
                <ul class="list-unstyled tree">
                    @foreach(App\ResourceType::tree()->get() as $level)
                        @include('resources._recursive_input', ['level' => $level, 'value' => session('filters.resources.resource_type_id')])
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
{{Form::close()}}
{{-- End filter form --}}