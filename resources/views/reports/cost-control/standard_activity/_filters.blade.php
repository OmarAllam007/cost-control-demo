<div class="panel panel-default">
    <div class="panel-body">
        <form action="" method="get">
            <div class="row">
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="period">Period</label>
                        <select name="period" id="period" class="form-control">
                            @php $period_id = session('period_id_' . $project->id) @endphp
                            @foreach($periods as $id => $name)
                                <option value="{{$id}}" {{$id == $period_id? 'selected' : ''}}>{{$name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="activity">Activity</label>
                        <select name="activity" id="activity" class="form-control">
                            <option value="">[All Activities]</option>
                            @php $activity_id = request('activity') @endphp
                            @foreach($activityNames as $id => $name)
                                <option value="{{$id}}" {{$id == $activity_id? 'selected' : ''}}>{{$name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="div">Division</label>
                        <select name="div" id="div" class="form-control">
                            <option value="">[All Divisions]</option>
                            @php $div = request('div') @endphp
                            @foreach($divisionNames as $id => $name)
                                <option value="{{$id}}" {{$id == $div? 'selected' : ''}}>{{$name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="status">Status</label>
                        @php $status = strtolower(request('status')) @endphp
                        <select name="status" id="status" class="form-control">
                            <option value="">[All Status]</option>
                            <option value="not started" {{$status == 'not started'? 'selected' : ''}}>Not Started</option>
                            <option value="in progress" {{$status == 'in progress'? 'selected' : ''}}>In Progress</option>
                            <option value="closed" {{$status == 'closed'? 'selected' : ''}}>Closed</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="checkbox" style="margin-top: 32px;">
                        <label>
                            <input name="negative" type="checkbox" {{request()->exists('negative') ? 'checked' : ''}}>
                            Negative Variance
                        </label>
                    </div>
                </div>
            </div>


            <button class="btn btn-rounded btn-outline btn-primary pull-right" type="submit"><i class="fa fa-filter"></i> Filter</button>
            <div class="clearfix"></div>
        </form>
    </div>
</div>