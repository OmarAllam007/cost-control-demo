@extends('layouts.iframe'))

@section('header')
    <h2>Quantity Survey</h2>
    <a href="" class="btn btn-sm btn-default pull-right"><i class="fa fa-arrow-left"></i> Back </a>
@stop

@section('body')
    <table class="table table-condensed table-striped">
        <thead>

        <tr>
            <th>Dublicated Cost Accounts</th>

        </tr>
        </thead>
        <tbody>
        @foreach(\Cache::get('qs-dublicated') as $item)
            <tr>
                <td>
                    {{$item}}
                </td>
            </tr>
        @endforeach

        <?php \Cache::forget('qs-dublicated')?>
        </tbody>
    </table>

@stop
