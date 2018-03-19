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
                    @include('reports.partials.activity-filter')
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
                <div class="col-sm-3 text-right" style="padding-top: 25px">
                    <button class="btn btn-rounded btn-outline btn-primary" type="submit"><i class="fa fa-filter"></i> Filter</button>
                    <a href="?reset" class="btn-rounded btn btn-default btn-outline">Reset</a>
                </div>
            </div>



            <div class="clearfix"></div>
        </form>
    </div>
</div>