@extends('layouts.app')

@section('header')
    <h2>Csi category</h2>
    <a href="{{ route('csi-category.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add CSI Category</a>
@stop


@section('body')
    @if ($categories->total())
        <ul class="list-unstyled tree">
            @foreach($categories as $category)
                @include('productivity._recursive')
            @endforeach
        </ul>

        {{ $categories->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No Categories found</strong></div>
    @endif
@stop