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

        <div class="form-group">
            <button class="btn btn-primary"><i class="fa fa-check"></i> Update</button>
        </div>
    </div>
</div>
