
<div class="row">
    <div class="col-sm-6">
        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{ Form::label('name', 'Name', ['class' => 'control-label']) }}
            {{ Form::text('name', null, ['class' => 'form-control']) }}
            {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('email', 'has-error')}}">
            {{ Form::label('email', 'Email', ['class' => 'control-label']) }}
            {{ Form::text('email', null, ['class' => 'form-control']) }}
            {!! $errors->first('email', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('password', 'has-error')}}">
            {{ Form::label('password', 'Password', ['class' => 'control-label']) }}
            {{ Form::password('password', ['class' => 'form-control']) }}
            {!! $errors->first('password', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group {{$errors->first('password_confirmation', 'has-error')}}">
            {{ Form::label('password_confirmation', 'Confirm Password', ['class' => 'control-label']) }}
            {{ Form::password('password_confirmation', ['class' => 'form-control']) }}
            {!! $errors->first('password_confirmation', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group">
            <div class="checkbox">
                <label>
                    {{Form::checkbox('is_admin')}}
                    Administrator
                </label>
            </div>
        </div>




        <div class="form-group">
            <button class="btn btn-success"><i class="fa fa-check"></i> Save</button>
        </div>
    </div>
    <div class="col-sm-6">
        <fieldset>
            <legend><h4>Permissions</h4></legend>

            <table class="table table-bordered table-condensed table-striped table-hover">
                <thead>
                <tr>
                    <th class="col-sm-6">Module</th>
                    <th class="text-center col-sm-2">Readonly</th>
                    <th class="text-center col-sm-2">Modify</th>
                    <th class="text-center col-sm-2">Delete</th>
                </tr>
                </thead>
                <tbody>
                @foreach(App\Module::options() as $id => $module)
                    <tr>
                        <td class="col-sm-6">{{$module}}</td>
                        <td class="text-center col-sm-2">
                            {{Form::hidden("module[$id][module_id]", $id)}}
                            {{Form::hidden("module[$id][read]", 0)}}
                            {{Form::checkbox("module[$id][read]", 1, !empty($user->permissions[$id]->pivot->read), ['class' => 'read'])}}
                        </td>
                        <td class="text-center col-sm-2">
                            {{Form::hidden("module[$id][write]", 0)}}
                            {{Form::checkbox("module[$id][write]", 1, !empty($user->permissions[$id]->pivot->write), ['class' => 'write'])}}
                        </td>
                        <td class="text-center col-sm-2">
                            {{Form::hidden("module[$id][delete]", 0)}}
                            {{Form::checkbox("module[$id][delete]", 1, !empty($user->permissions[$id]->pivot->delete), ['class' => 'delete'])}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </fieldset>
    </div>
</div>


@section('javascript')
    <script>
        $(function(){
            $('.write').on('change', function(){
                var parent = $(this).parents('tr');
                if (this.checked) {
                    parent.find('.read').prop({checked: true});
                } else {
                    parent.find('.delete').prop({checked: false});
                }
            });

            $('.delete').on('change', function(){
                var parent = $(this).parents('tr');
                if (this.checked) {
                    parent.find('.read,.write').prop({checked: true});
                }
            });

            $('.read').on('change', function(){
                var parent = $(this).parents('tr');
                if (!this.checked) {
                    parent.find('.delete,.write').prop({checked: false});
                }
            });
        });
    </script>
@endsection