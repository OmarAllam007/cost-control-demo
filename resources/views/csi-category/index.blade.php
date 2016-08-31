@extends('layouts.app')

@section('header')
    <h2>Csi category</h2>
    <a href="{{ route('csi-category.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add CSI Category</a>
@stop

@section('body')
    @if ($csiCategories->total())
        <ul class="list-unstyled tree">
            @foreach($csiCategories as $csiCategory)
                @include('csi-category._recursive')
            @endforeach
        </ul>

        {{ $csiCategories->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No Categories found</strong></div>
    @endif
@stop
