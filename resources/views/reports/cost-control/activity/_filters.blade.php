<div class="panel panel-default">
    <form class="panel-body" action="">
        <div class="row">
            <div class="col-sm-3">
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
                @include('reports.partials.wbs-filter')
            </div>

            <div class="col-sm-3">
                @include('reports.partials.activity-filter')
            </div>

            <div class="col-sm-3">
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
        </div>
        <div class="row">
            <div class="col-sm-3">
                <div class="checkbox">
                    <label>
                        <input name="negative_to_date" type="checkbox" {{request()->exists('negative_to_date') ? 'checked' : ''}}>
                        Negative Variance To Date
                    </label>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="checkbox">
                    <label>
                        <input name="negative_completion" type="checkbox" {{request()->exists('negative_completion') ? 'checked' : ''}}>
                        Negative Variance At Completion
                    </label>
                </div>
            </div>

            <div class="col-sm-6 text-right">
                <button class="btn btn-rounded btn-outline btn-primary" type="submit">
                    <i class="fa fa-filter"></i> Filter
                </button>

                <a href="?reset" class="btn-rounded btn btn-default btn-outline">Reset</a>
            </div>
        </div>

    </form>
</div>