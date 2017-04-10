<div class="panel panel-default">
    <div class="panel-body">
        <form action="">
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

                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="type">Resource Type</label>
                        @php $type = strtolower(request('type')) @endphp
                        <select name="type" id="type" class="form-control">
                            <option value="">[All Types]</option>
                            @foreach($types as $name)
                                <option value="{{$name}}" {{strtolower($name) == $type? 'selected' : ''}}>{{$name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="discipline">Discipline</label>
                        @php $discipline = strtolower(request('discipline')) @endphp
                        <select name="discipline" id="discipline" class="form-control">
                            <option value="">[All Disciplines]</option>
                            @foreach($disciplines as $name)
                                <option value="{{$name}}" {{strtolower($name) == $discipline? 'selected' : ''}}>{{$name}}</option>
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
                    <div class="form-group">
                        <label for="resource">Resource</label>
                        <input type="text" class="form-control" name="resource" id="resource" value="{{request('resource')}}">
                    </div>
                </div>

                <div class="col-sm-2">
                    <button class="btn btn-rounded btn-outline btn-primary" type="submit" style="margin-top: 25px;">
                        <i class="fa fa-filter"></i> Filter
                    </button>
                </div>

            </div>

            <div class="row">
                <div class="col-sm-6 text-right">

                </div>
            </div>
        </form>
    </div>
</div>