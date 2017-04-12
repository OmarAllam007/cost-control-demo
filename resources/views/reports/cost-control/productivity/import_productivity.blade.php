@extends('layouts.app')

@section('header')
    <h2>{{$project->name}} &mdash; Import Productivity(Man-Power) Report</h2>

@endsection

@section('body')
    <div class="row">
        <div class="col-md-6 col-sm-9">

            {{Form::open(['route' => ['productivity-report.import', $project], 'files' => true])}}

            <div class="form-group {{$errors->first('file', 'has-error')}}">
                {{Form::label('file', null, ['class' => 'control-label'])}}
                {{Form::file('file', ['class' => 'form-control'])}}
                {!! $errors->first('file', '<div class="help-block">:message</div>') !!}
            </div>

            <div class="form-group">
                <button class="btn btn-primary">
                    <i class="fa fa-check"></i> Submit
                </button>
            </div>

            {{Form::close()}}

        </div>
    </div>

    <div class="row">
        <table class="table table-striped table-hover table-condensed">
            <thead>
            <tr>
                <th>Uploaded By</th>
                <th>Uploaded At</th>
                <th>Period</th>
                <th>Uploaded File</th>
            </tr>
            </thead>

            <tbody>
            @foreach($data  as $item)

                <tr>
                    <td>{{\App\User::find($item->uploaded_by)->name}}</td>
                    <td>{{$item->created_at->format('d/m/Y H:i')}}</td>
                    <td>{{\App\Period::where('project_id',$project->id)->where('id',$item->period_id)->first()->name}}</td>
                    <td><i class="fa fa-download"></i>  <a href="{{'/download_trend/' . $item->id.'/download'}}"> {{$item->created_at}}</a></td>
                </tr>
        @endforeach
    </div>
@endsection