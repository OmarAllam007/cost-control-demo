{{Form::open(['route' => 'breakdown-template.filters', 'class' => 'row filter-form'])}}

<div class="col-sm-4">
    <div class="form-group-sm">
        {{Form::label('name', null, ['class' => 'control-label'])}}
        {{Form::text('name', session('filters.breakdown-template.name'), ['class' => 'form-control'])}}
    </div>
</div>

<div class="col-sm-4">
    <div class="form-group-sm">
        {{Form::label('resource_id', 'Has resource', ['class' => 'control-label'])}}
        <p>
            <a href="#ResourcesModal" data-toggle="modal" v-text="resource.name || 'Select Resource'"></a>
            <a href="#" class="text-danger" v-show="resource" @click.prevent="resource = false"><i class="fa fa-times-circle"></i></a>
        </p>
    </div>
</div>

<div class="col-sm-2">
    <div class="form-group-sm">
        <button class="btn btn-primary">
            <i class="fa fa-filter"></i> Filter
        </button>
    </div>
</div>

@include('std-activity-resource._templates')

{{Form::close()}}

