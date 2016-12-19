@extends('layouts.app')

@section('header')
    <h2>Productivity</h2>
    <div class="btn-toolbar pull-right">
        @can('write', 'productivity')
        <a href="{{ route('productivity.create') }} " class="btn btn-sm btn-primary">
            <i class="fa fa-plus"></i> Add Productivity
        </a>

        <div class="btn dropdown" style="padding: 0px">
            <button class="btn btn-success btn-sm dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown"
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
        {{--@can('wipe')--}}
            {{--<a href="#WipeAlert" data-toggle="modal" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Delete--}}
                {{--All</a>--}}
        {{--@endcan--}}
    </div>
@stop

@section('body')
    @if(count(request('dublicate')))
        <div class="container" id="notify" style="">
            @foreach(request('dublicate') as $item)
                <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>Item With Code
                        ( {{$item}} ) Exist.</strong>
                </div>
            @endforeach

        </div>
    @endif
    @include('productivity._filters')
    @if ($productivities->total())
        @include('productivity._list')
        {{ $productivities->links() }}

        @can('wipe')
        <div class="modal fade" tabindex="-1" role="dialog" id="WipeAlert">
            <form class="modal-dialog" action="{{route('productivity.wipe')}}" method="post">
                {{csrf_field()}}
                {{method_field('delete')}}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Delete All Productivity Items</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i>
                            Are you sure you want to delete all Productivities ?
                            <input type="hidden" name="wipe" value="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Delete All</button>
                        <button type="button" class="btn btn-default"><i class="fa fa-close"></i> Cancel</button>
                    </div>
                </div>
            </form>
        </div>
            @endcan
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No productivity found</strong>
        </div>
    @endif
@stop

@section('javascript')
    <script type="text/javascript">
        $(function() {
            setTimeout(function() {
                $("#notify").hide('slow')
            }, 10000);
        });
    </script>
    <script src="{{asset('/js/tree-select.js')}}"></script>
@endsection
