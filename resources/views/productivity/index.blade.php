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
        @can('wipe')
            <a href="#WipeAlert" data-toggle="modal" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Delete All</a>
        @endcan
    </div>


@stop



@section('body')
    @include('productivity._filters')
    @if ($productivities->total())

        @include('productivity._list')
        {{ $productivities->links() }}

        <div class="modal fade" tabindex="-1" role="dialog" id="WipeAlert">
            <form class="modal-dialog" action="{{route('productivity.wipe')}}" method="post">
                {{csrf_field()}}
                {{method_field('delete')}}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Delete All Standard Activities</h4>
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
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No productivity found</strong>
        </div>
    @endif
@stop

@section('javascript')
    <script src="{{asset('/js/tree-select.js')}}"></script>
@endsection
