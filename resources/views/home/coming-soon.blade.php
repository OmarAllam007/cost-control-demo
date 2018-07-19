@extends('layouts.app')

@section('title', 'Coming Soon')

@section('header')
    <h2>Coming Soon</h2>
@endsection

@section('body')
    <div class="text-center">
        <img src="{{asset('images/coming-soon.svg')}}" width="550" alt="Under Construction">
    </div>
@endsection