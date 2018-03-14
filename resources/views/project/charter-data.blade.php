@extends('layouts.app')

@section('header')
    <div class="display-flex">
        <h2 class="flex">Edit Project Charter</h2>
        <a href="{{ route('project.budget', $project)}}" class="btn btn-sm btn-default"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>

@stop

@section('body')
    {{ Form::model($project, ['route' => ['project.charter-data', $project]]) }}

    {{ method_field('patch') }}

    <div class="row mb-1">
        <section class="col-sm-6 br-1">
            <fieldset class="form-horizontal">
                <legend>Basic Information</legend>

                <div class="form-group">
                    {{Form::label('project_type', 'Project Type', ['class' => 'control-label col-sm-4'])}}
                    <div class="col-sm-8">
                        {{Form::text('project_type', null, ['class' => 'form-control'])}}
                    </div>
                </div>

                <div class="form-group">
                    {{Form::label('contract_type', 'Contract Type', ['class' => 'control-label col-sm-4'])}}
                    <div class="col-sm-8">
                        {{Form::text('contract_type', null, ['class' => 'form-control'])}}
                    </div>
                </div>

                <div class="form-group">
                    {{Form::label('client_name', 'Client Name', ['class' => 'control-label col-sm-4'])}}
                    <div class="col-sm-8">
                        {{Form::text('client_name', null, ['class' => 'form-control'])}}
                    </div>
                </div>


                <div class="form-group">
                    {{Form::label('consultant', 'Consultant Name', ['class' => 'control-label col-sm-4'])}}
                    <div class="col-sm-8">
                        {{Form::text('consultant', null, ['class' => 'form-control'])}}
                    </div>
                </div>

                <div class="form-group">
                    {{Form::label('project_location', 'Project Location', ['class' => 'control-label col-sm-4'])}}
                    <div class="col-sm-8">
                        {{Form::text('project_location', null, ['class' => 'form-control'])}}
                    </div>
                </div>

                <div class="form-group">
                    {{Form::label('project_duration', 'Project Duration', ['class' => 'control-label col-sm-4'])}}
                    <div class="col-sm-8">
                        {{Form::text('project_duration', null, ['class' => 'form-control'])}}
                    </div>
                </div>

                <div class="form-group">
                    {{Form::label('project_start_date', 'Planned Start Date', ['class' => 'control-label col-sm-4'])}}
                    <div class="col-sm-8">
                        {{Form::date('project_start_date', null, ['class' => 'form-control'])}}
                    </div>
                </div>

                <div class="form-group">
                    {{Form::label('expected_finish_date', 'Planned Finish Date', ['class' => 'control-label col-sm-4'])}}
                    <div class="col-sm-8">
                        {{Form::date('expected_finish_date', null, ['class' => 'form-control'])}}
                    </div>
                </div>

                <div class="form-group">
                    {{Form::label('project_contract_signed_value', 'Original Signed Contract Value', ['class' => 'control-label col-sm-4'])}}
                    <div class="col-sm-8">
                        {{Form::text('project_contract_signed_value', null, ['class' => 'form-control'])}}
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-horizontal">
                <legend>Tender</legend>

                <div class="form-group">
                    {{Form::label('tender_direct_cost', 'Direct Cost', ['class' => 'control-label col-sm-4'])}}
                    <div class="col-sm-8">
                        {{Form::text('tender_direct_cost', null, ['class' => 'form-control'])}}
                    </div>
                </div>

                <div class="form-group">
                    {{Form::label('tender_indirect_cost', 'Indirect Cost', ['class' => 'control-label col-sm-4'])}}
                    <div class="col-sm-8">
                        {{Form::text('tender_indirect_cost', null, ['class' => 'form-control'])}}
                    </div>
                </div>

                <div class="form-group">
                    {{Form::label('tender_risk', 'Risk and Escalation', ['class' => 'control-label col-sm-4'])}}
                    <div class="col-sm-8">
                        {{Form::text('tender_risk', null, ['class' => 'form-control'])}}
                    </div>
                </div>

                <div class="form-group">
                    {{Form::label('tender_initial_profit', 'Initial Profit', ['class' => 'control-label col-sm-4'])}}
                    <div class="col-sm-8">
                        {{Form::text('tender_initial_profit', null, ['class' => 'form-control'])}}
                    </div>
                </div>
            </fieldset>
        </section>

        <section class="col-sm-6">
            <fieldset>
                <legend>Brief</legend>
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
            </fieldset>
        </section>
    </div>

    <div class="form-group text-center">
        <button class="btn btn-primary"><i class="fa fa-check"></i> Update</button>
    </div>





    {{ Form::close() }}
@stop