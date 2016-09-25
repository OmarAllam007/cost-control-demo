{{Form::open(['route' => ['std-activity.filters'], 'class' => 'row filter-form'])}}

<div class="col-sm-4">
    <div class="form-group-sm">
        <label for="ActivityName">Name</label>
        <input type="text" class="form-control" name="name" id="ActivityName" value="{{session('filters.std-activity.name')}}">
    </div>
</div>
<div class="col-sm-4">
    <div class="form-group-sm">
        <label for="ActivityDivision">Division</label>
        <p>
            <a href="#ParentsModal" data-toggle="modal" id="selectDivision">
                @if ($div_id = session('filters.std-activity.division_id'))
                    {{App\ActivityDivision::find($div_id)->path}}
                @else
                    Select Division
                @endif
            </a>

            <a href="#" class="text-danger reset-modal-input" id="resetDivision"> <i class="fa fa-times-circle"></i></a>
        </p>
    </div>
</div>
<div class="col-sm-4">
    <div class="form-group-sm">
        <button class="btn btn-sm btn-primary"><i class="fa fa-filter"></i> Filter</button>
    </div>
</div>

@include('std-activity._division_modal', ['value' => session('filters.std-activity.division_id')])
{{Form::close()}}