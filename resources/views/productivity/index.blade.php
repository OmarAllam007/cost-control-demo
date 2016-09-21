@extends('layouts.app')

@section('header')
    <h2>Productivity</h2>
    <div class="btn-toolbar pull-right">
        <a href="{{ route('productivity.create') }} " class="btn btn-sm btn-primary">
            <i class="fa fa-plus"></i> Add Productivity
        </a>

        <a href="{{ route('productivity.import') }} " class="btn btn-sm btn-success">
            <i class="fa fa-cloud-upload"></i> Import
        </a>
    </div>

@stop



@section('body')
    @include('productivity._filters')
    @if ($productivities->total())
        @include('productivity._list')
        {{ $productivities->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No productivity found</strong>
        </div>
    @endif
@stop

@section('javascript')
    <script src="{{asset('/js/tree-select.js')}}"></script>
@endsection
