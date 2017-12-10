@extends(request('iframe')? 'layouts.iframe' : 'layouts.app')

@section('title', 'Send Reports')

@section('header')
    <div class="display-flex">
        <h2 class="flex">Send Reports &mdash; {{$project->name}}</h2>
        <a href="{{route('project.budget', $project)}}" class="btn btn-default btn-small">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@endsection

@section('body')
    <form action="" method="post" class="row">
        {{csrf_field()}}

        @foreach($roles as $role)
            <article class="col-sm-9">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <label>
                        <input type="checkbox" name="role[{{$role->role->id}}]">
                        {{$role->role->name}}
                    </label>
                </div>

                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <h4 class="page-header">Users</h4>
                            {{dump($role->users)}}
                        </div>
                        <div class="col-sm-6">
                            <h4 class="page-header">Reports</h4>

                        </div>
                    </div>
                </div>
            </div>
            </article>
        @endforeach
    </form>
@endsection