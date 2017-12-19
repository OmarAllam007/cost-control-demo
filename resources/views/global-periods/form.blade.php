<div class="row">
    <div class="col-md-9 col-sm-12">
        <section class="row">
            <article class="col-sm-6 section b-1">
                <h4 class="section-header">Period Info</h4>

                <article class="form-group {{$errors->first('name', 'has-error')}}">
                    {{Form::label('name', null, ['class' => 'control-label'])}}
                    {{Form::text('name', null, ['class' => 'form-control'])}}
                    {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
                </article>

                <article class="form-group {{$errors->first('start_date', 'has-error')}}">
                    {{Form::label('start_date', null, ['class' => 'control-label'])}}
                    {{Form::date('start_date', old('start_date', $global_period->start_date? $global_period->start_date->format('Y-m-d') : ''), ['class' => 'form-control'])}}
                    {!! $errors->first('start_date', '<div class="help-block">:message</div>') !!}
                </article>

                <article class="form-group {{$errors->first('end_date', 'has-error')}}">
                    {{Form::label('end_date', null, ['class' => 'control-label'])}}
                    {{Form::date('end_date', old('end_date', $global_period->end_date? $global_period->end_date->format('Y-m-d') : ''), ['class' => 'form-control'])}}
                    {!! $errors->first('end_date', '<div class="help-block">:message</div>') !!}
                </article>
            </article>


            <article class="col-sm-6 section">
                <h4 class="section-header">Dashboard Info</h4>

                <article class="form-group {{$errors->first('spi_index', 'has-error')}}">
                    {{Form::label('spi_index', null, ['class' => 'control-label'])}}
                    {{Form::number('spi_index', null, ['class' => 'form-control', 'step' => '0.1'])}}
                    {!! $errors->first('spi_index', '<div class="help-block">:message</div>') !!}
                </article>
            </article>
        </section>


        <div class="form-group">
            <button class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
        </div>

    </div>
</div>