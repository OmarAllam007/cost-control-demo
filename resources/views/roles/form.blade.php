<div class="row">
<div class="col-sm-9">

    <div class="form-group {{$errors->first('name', 'has-error')}}">
        {{Form::label('name', null, ['class' => 'control-label'])}}
        {{Form::text('name', null, ['class' => "form-control"])}}
        {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
    </div>

    <div class="form-group {{$errors->first('description', 'has-error')}}">
        {{Form::label('description', null, ['class' => 'control-label'])}}
        {{Form::textarea('description', null, ['class' => "form-control", 'rows' => 3])}}
        {!! $errors->first('description', '<div class="help-block">:message</div>') !!}
    </div>


    <section id="ReportsSelection">
        <div class="row">
            @foreach($reports as $key => $group)
                @include('roles.report_group', compact('key', 'group'))
            @endforeach
        </div>

        <div class="clearfix {{$errors->first('reports', 'has-error')}}">
            {!! $errors->first('reports', '<div class="help-block">:message</div>') !!}
        </div>
    </section>

    <div class="form-group">
        <button class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
    </div>
</div>
</div>
@section('javascript')
    <script>
        $(function() {
            $('.select-all').on('click', function(e) {
                e.preventDefault();

                $(this).closest('.report-panel').find('input:checkbox').prop('checked', true);
            });

            $('.remove-all').on('click', function(e) {
                e.preventDefault();

                $(this).closest('.report-panel').find('input:checkbox').prop('checked', false);
            });
        });
    </script>
@append

@section('css')
    <style>
        #ReportsSelection {
            margin-bottom: 20px;
        }

        #ReportsSelection .panel {
            margin-bottom: 0;
        }
    </style>
@append