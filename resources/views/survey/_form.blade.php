{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group {{$errors->first('name', 'has-error')}}">

                {{Form::label('category_id','Category')}}
                {{Form::select('category_id',$categories,['class'=>'form-control'],['class'=>'form-control'])}}

        </div>

        <div class="form-group {{$errors->first('name', 'has-error')}}">

                {{Form::label('cost_account','Cost Acount ID')}}
            {{Form::text('cost_account',null,['class'=>'form-control'])}}

        </div>

        <div class="form-group {{$errors->first('name', 'has-error')}}">

            {{Form::label('item','Item Description')}}
            {{Form::textarea('description',null,['class'=>'form-control'])}}

        </div>


        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{Form::label('units','Unit')}}

                {{Form::select('unit_id',$units_drop,['class'=>'form-control'],['class'=>'form-control'])}}


        </div>

        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{Form::label('budget_qty','Budget Quantity')}}
            {{Form::text('budget_qty',null,['class'=>'form-control'])}}

        </div>

        <div class="form-group {{$errors->first('name', 'has-error')}}">
            {{Form::label('eng_qty','Eng Quantity')}}
            {{Form::text('eng_qty',null,['class'=>'form-control'])}}

        </div>

    <?php echo $errors->first('name', '<div class="help-block"></div>'); ?>
    <!-- Continue working on your fields here -->

        <div class="form-group">
            <button class="btn btn-success"><i class="fa fa-check"></i> Add</button>
        </div>
    </div>
</div>
</div>

