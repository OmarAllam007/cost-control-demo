{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group {{$errors->first('name', 'has-error')}}">
            @foreach($units as $unit)
                {{Form::label('cost_name','Cost Name')}}
                {{Form::text('cost_name',$unit->cost_name,['class'=>'form-control'])}}
            @endforeach
        </div>
        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{Form::label('units','Unit')}}
            @foreach($units as $unit)
                {{Form::select('unit_id',[$unit->id=>$unit->type],['class'=>'form-control'])}}
            @endforeach

        </div>

        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{Form::label('budget_qty','Budget Quantity')}}
            {{Form::text('budget_qty',$unit->budget_qty,['class'=>'form-control'])}}

        </div>

        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{Form::label('eng_qty','Eng Quantity')}}
            {{Form::text('eng_qty',$unit->eng_qty,['class'=>'form-control'])}}

        </div>

    <?php echo $errors->first('name', '<div class="help-block"></div>'); ?>
    <!-- Continue working on your fields here -->

        <div class="form-group">
            <button class="btn btn-success"><i class="fa fa-check"></i> Add</button>
        </div>
    </div>
</div>
</div>

