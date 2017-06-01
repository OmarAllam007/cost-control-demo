<div class="row">
    <div class="col-sm-6">
        <div class="form-group {{$errors->first('name', 'has-error')}}">
            <label for="name" class="control-label">Name</label>
            <input class="form-control" id="name" name="name" value="{{old('name', $revision->name)}}" autofocus>
            {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group">
            <label for="rev-num" class="control-label">Revision Number</label>
            <input class="form-control" id="rev-num" name="rev_num" value="{{$revision->rev_num}}" readonly>
        </div>

        <div class="checkbox">
            <label class="control-label">
                <input type="hidden" name="is_open" value="0">
                <input type="checkbox" name="is_open" value="1" id="is-open" {{old('is_open', $revision->is_open)}}>
                Activate this revision
            </label>
        </div>

        <div class="form-group">
            <button class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
        </div>
    </div>
</div>
