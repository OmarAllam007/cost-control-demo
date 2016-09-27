@extends('layouts.app')

@section('header')
    <h2>Fix import</h2>

    <a href="{{ route('resources.index') }}" class="btn btn-sm btn-warning pull-right">
        <i class="fa fa-remove"></i> Cancel
    </a>
@endsection

@section('body')
    {{Form::open()}}
    <div class="row">
        <div class="col-sm-6">
            <table class="table table-condensed table-hover table-striped">
                <thead>
                <tr>
                    <th>Unit</th>
                    <th>Equivalent</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($items->pluck('orig_unit')->unique() as $unit)
                    <tr class="{{$errors->first($unit, 'danger')}}">
                        <td class="col-sm-6">
                            {{$unit}}
                        </td>
                        <td class="col-sm-6">
                            {{Form::select("data[units][$unit]", App\Unit::options(), null, ['class' => 'form-control input-sm'])}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="form-group">
                <button class="btn btn-primary">
                    <i class="fa fa-check"></i> Update
                </button>
            </div>
        </div>
    </div>
    {{Form::close()}}
@endsection