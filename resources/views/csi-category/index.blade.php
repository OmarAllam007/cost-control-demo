@extends('layouts.app')

@section('header')
    <h2>Csi category</h2>
    <div class="btn-toolbar pull-right">
        <a href="{{ route('csi-category.create') }} " class="btn btn-sm btn-primary pull-right"><i
                    class="fa fa-plus"></i> Add CSI Category</a>
    </div>
@stop


@section('body')
    @if ($categories->total())
        <ul class="list-unstyled tree">
            @foreach($categoryTree as $category)
                @include('productivity._recursive')
            @endforeach
        </ul>

        {{ $categories->links() }}
        <div class="modal fade" tabindex="-1" role="dialog" id="WipeAlert">
            <form class="modal-dialog" action="{{route('csi-category.wipe')}}" method="post">
                {{csrf_field()}}
                {{method_field('delete')}}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Delete All Categories of Productivities</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i>
                            Are you sure you want to delete all Categories ?
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
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No Categories found</strong>
        </div>
    @endif
@stop