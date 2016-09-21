{{-- Filter Form --}}
{{Form::open(['route' => 'productivity.filter', 'class' => 'row filter-form'])}}
<div class="col-sm-1">
    <div class="form-group-sm">
        <label class="control-label" for="productivityCode">Code</label>
        <input type="text" id="productivityCode" name="code" class="form-control" value="{{session('filters.productivity.code')}}">
    </div>
</div>
<div class="col-sm-3">
    <div class="form-group-sm">
        <label class="control-label" for="productivityDescription">Description</label>
        <input type="text" id="productivityDescription" name="description" class="form-control" value="{{session('filters.productivity.description')}}">
    </div>
</div>

<div class="col-sm-3">
    <div class="form-group-sm">
        <label class="control-label" for="productivityCrewStructure">Crew Structure</label>
        <input type="text" id="productivityCrewStructure" name="crew_structure" class="form-control" value="{{session('filters.productivity.crew_structure')}}">
    </div>
</div>

<div class="col-sm-1">
    <div class="form-group-sm">
        <label class="control-label" for="resourceUnit">Unit</label>
        {{Form::select('unit', App\Unit::options(), session('filters.productivity.unit'), ['class' => 'form-control', 'id' => 'resourceUnit'])}}
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


{{Form::close()}}
{{-- End filter form --}}