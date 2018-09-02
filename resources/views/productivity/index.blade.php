@extends('home.master-data')

@section('header')
    <h2>Productivity</h2>
    <div class="btn-toolbar pull-right">
        @can('write', 'productivity')
            <a href="{{ route('productivity.create') }} " class="btn btn-sm btn-primary">
                <i class="fa fa-plus"></i> Add Productivity
            </a>

            <div class="btn dropdown" style="padding: 0px">
                <button class="btn btn-success btn-sm dropdown-toggle" type="button" id="dropdownMenu1"
                        data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="true">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                    Importing
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <li>
                        <a href="{{ route('productivity.import') }} " class="btn">
                            <i class="fa fa-cloud-upload"></i> Import
                        </a>
                    </li>
                    <li>
                        <a href="{{route('all-productivities.modify')}}" class="btn">
                            <i class="fa fa-pencil" aria-hidden="true"></i>
                            Modify
                        </a>
                    </li>
                </ul>
            </div>
        @endcan

        <a href="{{route('productivity.exportAll')}}" class="btn btn-info btn-sm">
            <i class="fa fa-cloud-download"></i> Export
        </a>
    </div>
@stop

@section('content')
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
