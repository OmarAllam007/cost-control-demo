<div class="row">
    <div class="col-sm-8">
        <div class="form-group {{$errors->first('name', 'has-error')}}">
            <label for="name" class="control-label">Name</label>
            <input class="form-control" id="name" name="name" value="{{old('name', $revision->name)}}" autofocus>
            {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group">
            <label for="rev-num" class="control-label">Revision Number</label>
            <input class="form-control" id="rev-num" name="rev_num" value="{{$revision->rev_num}}" readonly>
        </div>

        @if ($revision->exists())
            <div class="form-group">
                <label for="original_contract_amount" class="control-label">Original Contract Amount</label>
                <input class="form-control" id="original_contract_amount" name="original_contract_amount" value="{{$revision->original_contract_amount}}">
            </div>

            <div class="form-group">
                <label for="change_order_amount" class="control-label">Change Order Amount</label>
                <input class="form-control" id="change_order_amount" name="change_order_amount" value="{{$revision->change_order_amount}}">
            </div>
        @endif

        <div class="form-group">
            <button class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
        </div>
    </div>
</div>
