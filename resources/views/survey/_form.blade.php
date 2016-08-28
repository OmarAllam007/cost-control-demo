<div class="row">
    <div class="col-md-6">
        <div class="form-group {{$errors->first('category_id', 'has-error')}}">
            {{Form::label('category_id','Category')}}
            {{Form::select('category_id', App\Category::options(), null, ['class'=>'form-control'])}}
        </div>

        <div class="form-group {{$errors->first('cost_account', 'has-error')}}">
            {{Form::label('cost_account','Cost Account')}}
            {{Form::text('cost_account',null,['class'=>'form-control'])}}
        </div>

        <div class="form-group {{$errors->first('description', 'has-error')}}">
            {{Form::label('item','Item Description')}}
            {{Form::textarea('description',null,['class'=>'form-control'])}}
        </div>

        <div class="form-group {{$errors->first('unit_id', 'has-error')}}">
            {{Form::label('units','Unit of measure')}}
            {{Form::select('unit_id',App\Unit::options(),['class'=>'form-control'],['class'=>'form-control'])}}
        </div>

        <div class="form-group {{$errors->first('budget_qty', 'has-error')}}">
            {{Form::label('budget_qty','Budget Quantity')}}
            {{Form::text('budget_qty',null,['class'=>'form-control'])}}
        </div>

        <div class="form-group {{$errors->first('eng_qty', 'has-error')}}">
            {{Form::label('eng_qty','Eng Quantity')}}
            {{Form::text('eng_qty',null,['class'=>'form-control'])}}
        </div>

        <div class="form-group">
            <button class="btn btn-success"><i class="fa fa-check"></i> Save</button>
        </div>
    </div>
</div>

