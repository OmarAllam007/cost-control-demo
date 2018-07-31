@extends(request()->exists('iframe')? 'layouts.iframe' : 'layouts.app')

@section('head')
    <div class="display-flex">
        <h2 class="flex">{{$project->name}} &mdash; Chang Request</h2>
    </div>
@endsection

@section('body')

    <form action="" method="post">
        @csrf

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group {{$errors->first('qty', 'has-error')}}">
                    <label for="qty">Qty</label>
                    <input id="qty" type="text" name="qty" class="form-control" value="{{old('qty')}}">
                    {!! $errors->first('qty', '<div class="help-block">:message</div>') !!}
                </div>
            </div>
            
            <div class="col-sm-6">
                <div class="form-group {{$errors->first('unit_price', 'has-error')}}">
                    <label for="unit_price">Unit Price</label>
                    <input id="unit_price" type="text" name="unit_price" class="form-control" value="{{old('unit_price')}}">
                    {!! $errors->first('unit_price', '<div class="help-block">:message</div>') !!}
                </div>
            </div>
        </div>

        <div class="form-group {{$errors->first('description', 'has-error')}}">
            <label for="description">Description</label>
            <textarea id="description" class="form-control" rows="3">{{old('description')}}</textarea>
            {!! $errors->first('description', '<div class="help-block">:message</div>') !!}
        </div>

    </form>


@endsection

