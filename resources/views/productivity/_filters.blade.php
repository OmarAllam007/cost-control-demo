{{-- Filter Form --}}
{{Form::open(['route' => 'productivity.filter', 'class' => 'row filter-form'])}}
<div class="col-sm-2">
    <div class="form-group-sm">
        <label for="ResourceType">Productivity Category</label>
        <p>
            <a href="#CSICategoryModal" data-toggle="modal">
                @if ($type = Session::get('filters.productivity.csi_category_id'))
                    {{App\CsiCategory::find($type)->name}}
                @else
                    Select Category
                @endif
            </a>
        </p>
    </div>
</div>
<div class="col-sm-1">
    <div class="form-group-sm">
        <label class="control-label" for="productivityCode">CSI Code</label>
        <input type="text" id="productivityCode" name="code" class="form-control" value="{{session('filters.productivity.code')}}">
    </div>
</div>
<div class="col-sm-3">
    <div class="form-group-sm">
        <label class="control-label" for="productivityDescription">Description</label>
        <input type="text" id="productivityDescription" name="description" class="form-control" value="{{session('filters.productivity.description')}}">
    </div>
</div>

<div class="col-sm-1">
    <div class="form-group-sm">
        <label class="control-label" for="productivitySource">Source</label>
        <input type="text" id="productivitySource" name="source" class="form-control" value="{{session('filters.productivity.source')}}">
    </div>
</div>



<div class="col-sm-3">
    <div class="form-group-sm">
        <button class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button>
    </div>
</div>

<div id="CSICategoryModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Select Category</h4>
            </div>
            <div class="modal-body">
                <ul class="list-unstyled tree">
                    @foreach(App\CsiCategory::tree()->get() as $level)
                        @include('csi-category._recursive_input', compact('level'))
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>






{{--<div class="col-sm-3">--}}
{{--<div class="form-group-sm">--}}
{{--<label class="control-label" for="productivityCrewStructure">Crew Structure</label>--}}
{{--<input type="text" id="productivityCrewStructure" name="crew_structure" class="form-control" value="{{session('filters.productivity.crew_structure')}}">--}}
{{--</div>--}}
{{--</div>--}}

{{--<div class="col-sm-1">--}}
{{--<div class="form-group-sm">--}}
{{--<label class="control-label" for="resourceUnit">Unit</label>--}}
{{--{{Form::select('unit', App\Unit::options(), session('filters.productivity.unit'), ['class' => 'form-control', 'id' => 'resourceUnit'])}}--}}
{{--</div>--}}
{{--</div>--}}

{{Form::close()}}
{{-- End filter form --}}