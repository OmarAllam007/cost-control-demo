@extends('layouts.app')

@section('header')
    <div class="display-flex">
        <h2 class="flex">Edit Project Charter</h2>
        <a href="{{ route('project.budget', $project)}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </div>

@stop

@section('body')
    {{ Form::model($project, ['route' => ['project.charter-data', $project]]) }}

    {{ method_field('patch') }}

    <div class="row">
        <section class="col-sm-6">
            <div class="form-group">
                {{Form::label('consultant', 'Consultant', ['class' => 'control-label'])}}
                {{Form::text('consultant', null, ['class' => 'form-control'])}}
            </div>

            <div class="form-group">
                {{Form::label('project_type', 'Project Type', ['class' => 'control-label'])}}
                {{Form::text('project_type', null, ['class' => 'form-control'])}}
            </div>

            <div class="form-group">
                {{Form::label('contract_type', 'Contract Type', ['class' => 'control-label'])}}
                {{Form::text('contract_type', null, ['class' => 'form-control'])}}
            </div>

            <div class="form-group">
                {{Form::label('project_contract_signed_value', 'Selling cost', ['class' => 'control-label'])}}
                {{Form::text('project_contract_signed_value', null, ['class' => 'form-control'])}}
            </div>

            <div class="form-group">
                {{Form::label('dry_cost', 'Total project dry cost', ['class' => 'control-label'])}}
                {{Form::text('dry_cost', null, ['class' => 'form-control'])}}
            </div>

            <div class="form-group">
                {{Form::label('change_order_amount', 'Total change order amount', ['class' => 'control-label'])}}
                {{Form::text('change_order_amount', null, ['class' => 'form-control'])}}
            </div>

            <div class="form-group">
                {{Form::label('overhead_and_gr', 'Overhead + General requirements', ['class' => 'control-label'])}}
                {{Form::text('overhead_and_gr', null, ['class' => 'form-control'])}}
            </div>

            <div class="form-group">
                {{Form::label('estimated_profit_and_risk', 'Estimated Profit + Risk', ['class' => 'control-label'])}}
                {{Form::text('estimated_profit_and_risk', null, ['class' => 'form-control'])}}
            </div>
        </section>

        <section class="col-sm-6">
            <div class="form-group">
                {{Form::label('description', 'Project Brief', ['class' => 'control-label'])}}
                {{Form::textarea('description', null, ['rows' => 3, 'class' => 'form-control'])}}
            </div>

            <div class="form-group">
                {{Form::label('discipline_brief', 'Discipline Brief', ['class' => 'control-label'])}}
                {{Form::textarea('discipline_brief', null, ['rows' => 5, 'class' => 'form-control'])}}
            </div>

            <div class="form-group">
                {{Form::label('assumptions', 'Assumptions', ['class' => 'control-label'])}}
                {{Form::textarea('assumptions', null, ['rows' => 5, 'class' => 'form-control'])}}
            </div>

        </section>
    </div>

    <div class="form-group">
        <button class="btn btn-primary"><i class="fa fa-check"></i> Update</button>
    </div>




    {{ Form::close() }}
@stop