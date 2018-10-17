@extends(request()->exists('iframe')? 'layouts.iframe' : 'layouts.app')


@section('header')
    <div class="display-flex">
        <h2 class="flex">{{$project->name}}</h2>
    </div>
@endsection

@section('body')
    <div class="row" id="ChangeRequestForm">
        <form action="{{ route('project.change-request.store', $project)  }}{{request()->exists('iframe')? '?iframe' : ''}}" method="post" class="col-sm-12 col-md-9">
            @csrf

            <section class="row">
                <div class="col-sm-4">
                    <article class="form-group {{$errors->first('wbs_id', 'has-error')}}">
                        <label for="wbs_id">WBS</label>
                        <div class="btn-group btn-group-sm btn-group-block">
                            <a href="#WBSModal" data-toggle="modal" class="btn btn-default">
                                {{ optional(App\WbsLevel::find(old('wbs_id')))->code ?: 'Select WBS'}}
                            </a>

                            <a href="#" class="remove-tree-input btn btn-warning">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                        {!! $errors->first('wbs_id', '<div class="help-block">:message</div>') !!}
                    </article>
                </div>

                <div class="col-sm-4">
                    <article class="form-group {{$errors->first('activity_id', 'has-error')}}">
                        <label for="activity_id">Activity</label>
                        <div class="btn-group btn-group-sm btn-group-block">
                            <a href="#ActivitiesModal" data-toggle="modal" class="btn btn-default">
                                {{ optional(App\StdActivity::find(old('activity_id')))->name ?: 'Select Activity'}}
                            </a>

                            <a href="#" class="remove-tree-input btn btn-warning">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                        {!! $errors->first('activity_id', '<div class="help-block">:message</div>') !!}
                    </article>
                </div>

                <div class="col-sm-4">
                    <article class="form-group {{$errors->first('resource_id', 'has-error')}}">
                        <label for="resource_id">Resource</label>
                        <div class="btn-group btn-group-sm btn-group-block">
                            <a href="#ResourcesModal" data-toggle="modal" class="btn btn-default">
                                {{ optional(App\Resources::find(old('resource_id')))->name ?: 'Select Resource'}}
                            </a>

                            <a href="#" class="remove-tree-input btn btn-warning">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                        {!! $errors->first('resource_id', '<div class="help-block">:message</div>') !!}
                    </article>
                </div>
            </section>

            <section class="row">
                <div class="col-sm-4">
                    <article class="form-group {{$errors->first('qty', 'has-error')}}">
                        <label for="qty">Proposed Qty</label>
                        <input name="qty" type="text" class="form-control" id="qty" value="{{old('qty')}}">
                        {!! $errors->first('qty', '<div class="help-block">:message</div>') !!}
                    </article>
                </div>

                <div class="col-sm-4">
                    <article class="form-group {{$errors->first('unit_price', 'has-error')}}">
                        <label for="unit_price">Proposed Unit Price</label>
                        <input name="unit_price" type="text" class="form-control" id="unit_price"
                               value="{{old('unit_price')}}">
                        {!! $errors->first('unit_price', '<div class="help-block">:message</div>') !!}
                    </article>
                </div>
            </section>

            <article class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" cols="30" rows="5" class="form-control">{{old('description')}}</textarea>
            </article>

            <article class="form-group">
                <button class="btn btn-primary"><i class="fa fa-check"></i> Create Request</button>
            </article>

            @include('wbs-level._modal', ['input' => 'wbs_id', 'value' => old('wbs_id')])
            @include('std-activity._modal', ['input' => 'activity_id', 'value' => old('activity_id')])
            @include('std-activity-resource._resources_modal')
        </form>
    </div>
@endsection

@section('javascript')
    <script src="{{asset('js/change-request.js')}}"></script>
    <script src="{{asset('js/tree-select.js')}}"></script>
@endsection