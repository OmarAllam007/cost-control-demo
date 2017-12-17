@extends('layouts.app')

@section('title', 'Communication Plan')

@section('header')
    <div class="display-flex">
        <h2 class="flex">Communication Plan</h2>

        <a href="{{route('roles.create')}}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add Role</a>
    </div>
@endsection

@section('body')
    @if ($roles->total())
    <table class="table table-condensed table-striped" id="rolesTable">
        <thead>
        <tr>
            <th class="col-sm-8">Name</th>
            <th class="col-sm-4">Actions</th>
        </tr>
        </thead>

        <tbody>
            @foreach($roles as $role)
                <tr>
                    <td>{{$role->name}}</td>
                    <td>
                        <form action="{{route('roles.destroy', $role)}}" method="post">
                            {{csrf_field()}}
                            {{method_field('delete')}}

                            <a href="{{route('roles.edit', $role)}}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                            <button class="btn btn-danger btn-sm btn-delete"><i class="fa fa-trash"></i> Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

    {{$roles->links()}}

    @else
        <div class="alert alert-info"><i class="fa fa-info-circle"></i> No roles found</div>
    @endif
@endsection

@section('javascript')
    @if ($roles->total())
    <script>
        $(function() {
            $('#rolesTable').on('submit', 'form', function () {
                return window.confirm("Are you sure you want to delete this role? It will be deleted from all projects!");
            });
        });
    </script>
    @endif
@endsection