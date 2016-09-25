{{Form::open(['route' => 'breakdown-template.filters', 'class' => 'row filter-form'])}}

<div class="col-sm-4">
    <div class="form-group">
        {{Form::label('name', null, ['class' => 'control-label'])}}
        {{Form::text('name', session('filters.breakdown-template.name'), ['class' => 'form-control'])}}
    </div>
</div>

<div class="col-sm-4">
    <div class="form-group">
        {{Form::label('resource_id', 'Has resource', ['class' => 'control-label'])}}
        {{Form::select('resource_id', App\Resources::options(), session('filters.breakdown-template.resource_id'), ['class' => 'form-control'])}}
    </div>
</div>

<div class="col-sm-2">
    <div class="form-group">
        <button class="btn btn-primary">
            <i class="fa fa-filter"></i> Filter
        </button>
    </div>
</div>
{{Form::close()}}