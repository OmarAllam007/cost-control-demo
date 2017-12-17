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

        <roles :roles="{{$roles}}" :errors="{{json_encode($errors->toArray())}}" inline-template>
            <section class="col-sm-9 col-md-6">
                <role v-for="(key,role) in roles" inline-template :key="key" :role="role" :errors="errors">
                    <article class="panel panel-default">
                        <div class="panel-heading display-flex">
                            <label class="flex">
                                <input type="checkbox" :name="`roles[${role.id}][role_id]`" v-model="enabled" :value="role.id">
                                @{{ role.name }}
                            </label>

                            <a :disabled="!enabled" href="#" class="btn btn-sm btn-primary" @click.prevent="addUser" tabindex="-1"><i class="fa fa-plus"></i> Add User</a>
                        </div>

                        
                        <users inline-template :role_id="role.id" :users="users" :errors="errors">
                            <table class="table table-condensed table-striped" v-show="users.length">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr is="user" v-for="(user_key, user) in users" :user_key="user_key" :user_data="user" :role_id="role_id" :errors="errors"></tr>
                                </tbody>
                            </table>
                        </users>
                    </article>
                </role>
            </section>
        </roles>


        <div class="col-sm-12 form-group">
            <button class="btn btn-primary"><i class="fa fa-check"></i> Update</button>
        </div>
    </form>
@endsection

@section('javascript')
    <script src="/js/edit-project-roles.js"></script>
@endsection