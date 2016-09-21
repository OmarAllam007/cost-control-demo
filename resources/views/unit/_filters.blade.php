{{Form::open(['route' => ['unit.filter'], 'class' => 'row filter-form'])}}

<div class="col-sm-4">
    <div class="form-group-sm">
        <label for="ActivityName">Name</label>
        <input type="text" class="form-control" name="type" id="unit_id" value="{{session('filters.unit.type')}}">
    </div>
</div>
<div class="col-sm-4">
    <div class="form-group-sm">
        <button class="btn btn-sm btn-primary"><i class="fa fa-filter"></i> Filter</button>
    </div>
</div>


{{Form::close()}}