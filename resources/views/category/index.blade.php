@extends('layouts.app')

@section('header')
    <h2>Category</h2>
    <a href="{{ route('category.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add category</a>
    <a href="{{ route('category.importcategory') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> import categories</a>
@stop

@section('body')
    @if ($categories->total())
        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th>Name</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td class="col-md-5"><a href="{{ route('category.edit', $category) }}">{{ $category->name }}</a></td>
                        <td class="col-md-3">
                            <form action="{{ route('category.destroy', $category) }}" method="post">
                                {{csrf_field()}} {{method_field('delete')}}
                                <a class="btn btn-sm btn-primary" href="{{ route('category.edit', $category) }} "><i class="fa fa-edit"></i> Edit</a>
                                <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $categories->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No category found</strong></div>
    @endif
@stop
