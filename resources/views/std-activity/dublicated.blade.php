@extends('layouts.app')

@section('header')
    <h2>Standard Activity</h2>
    <a href="{{URL::previous()}}" class="btn btn-sm btn-default pull-right"><i class="fa fa-arrow-left"></i> Back </a>
@stop

@section('body')
    <table class="table table-condensed table-striped">
        <thead>

        <tr>
            <th class="col-md-1">No.</th>
            <th class="col-md-11">Dublicated Codes</th>

        </tr>
        </thead>
        <tbody>
<?php $i=0;?>
        @foreach(\Cache::get('std-dublicated') as $item)
            <tr>
                <td>
                    <?php echo $i++;?>
                </td>
                <td>
                    {{$item}}
                </td>
            </tr>
        @endforeach


        </tbody>
    </table>

@stop