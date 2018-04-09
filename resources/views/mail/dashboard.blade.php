@extends('mail.layout')

@section('body')
    Dear {{$recipient['name']}}, <br><br>

    Please find attached KPS Dashboard for <strong>{{$period->name}}</strong>.<br><br>

    Regards
@endsection