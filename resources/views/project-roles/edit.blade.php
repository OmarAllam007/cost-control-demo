@extends('layouts.app')

@section('title', 'Project Communication Plan')

@section('header')
    <div class="display-flex">
        <h2 class="flex">{{$project->name}} &mdash; Communication Plan</h2>

        <a href="{{request('back', route('project.budget', $project))}}" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@endsection

@section('body')
    <form action="" method="post" id="rolesForm" class="row">
        {{method_field('put')}}
        {{csrf_field()}}

        <roles :roles="{{$roles}}" inline-template>
            <section class="col-sm-9 col-md-6">
                <role v-for="(key,role) in roles" inline-template :key="key" :role="role">
                    <article class="panel panel-default">
                        <div class="panel-heading display-flex">
                            <label class="flex">
                                <input type="checkbox" :name="`roles[${key}][role_id]`" v-model="enabled" :value="role.role_id">
                                @{{ role.name }}
                            </label>

                            <a v-if="enabled" href="#" class="btn btn-sm btn-primary" @click.prevent="addUser"><i class="fa fa-plus"></i> Add User</a>
                        </div>

                        
                        <users inline-template :role_key="key" :users="users">
                            <table class="table table-condensed table-striped" v-if="users.length">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr is="user" v-for="(user_key, user) in users" :user_key="user_key" :user_data="user" :role_key="role_key"></tr>
                                </tbody>
                            </table>
                        </users>
                    </article>
                </role>
            </section>
        </roles>
    </form>
@endsection

@section('javascript')
    <script src="/js/edit-project-roles.js"></script>
@endsection