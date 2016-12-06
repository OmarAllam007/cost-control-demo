{{-- Filter Form --}}
{{Form::open(['route' => 'productivity.filter', 'class' => 'row filter-form'])}}
<div class="col-sm-2">
    <div class="form-group-sm">
        <label for="ResourceType">Productivity Category</label>
        <p>
            <a href="#CSICategoryModal" data-toggle="modal" class="tree-open">
                @if ($type = Session::get('filters.productivity.csi_category_id'))
                    {{App\CsiCategory::find($type)->name}}
                @else
                    Select Category
                @endif
            </a>
            <a class="remove-tree-input" data-target="#CSICategoryModal" data-label="Select Category"><span class="fa fa-times"></span></a>
        </p>
    </div>
</div>
<div class="col-sm-1">
    <div class="form-group-sm">
        <label class="control-label" for="productivityCode">CSI Code</label>
        <input type="text" id="productivityCode" name="code" class="form-control"
               value="{{session('filters.productivity.code')}}">
    </div>
</div>
<div class="col-sm-3">
    <div class="form-group-sm">
        <label class="control-label" for="productivityDescription">Description</label>
        <input type="text" id="productivityDescription" name="description" class="form-control"
               value="{{session('filters.productivity.description')}}">
    </div>
</div>

<div class="col-sm-1">
    <div class="form-group-sm">
        <label class="control-label" for="productivitySource">Source</label>
        <input type="text" id="productivitySource" name="source" class="form-control"
               value="{{session('filters.productivity.source')}}">
    </div>
</div>


<div class="col-sm-3">
    <div class="form-group-sm">
        <button class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button>
    </div>
</div>
@include('productivity._category_modal', ['value' => session('filters.productivity.csi_category_id')])



{{Form::close()}}
