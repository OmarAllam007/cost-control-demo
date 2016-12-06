@extends('layouts.app')

@section('header')
    <h2>Unit</h2>
    <div class="btn-toolbar pull-right">
        <a href="{{ route('users.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add user</a>
    </div>
@stop

@section('body')
    @if ($users->total())
        <table class="table table-condensed table-striped table-fixed">
            <thead>
            <tr>
                <th class="col-xs-5">Name</th>
                <th class="col-xs-4">Email</th>
                <th class="col-xs-3">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td class="col-xs-5"><a href="{{ route('users.edit', $user) }}">{{ $user->name }}</a></td>
                    <td class="col-xs-4"><a href="{{ route('users.edit', $user) }}">{{ $user->email }}</a></td>
                    <td class="col-xs-3">
                        <form action="{{ route('users.destroy', $user) }}" method="post">
                            {{csrf_field()}} {{method_field('delete')}}
                            <a class="btn btn-sm btn-primary" href="{{ route('users.edit', $user) }} "><i class="fa fa-edit"></i> Edit</a>
                            <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $users->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No unit found</strong></div>
    @endif
@stop
