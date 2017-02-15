<div class="col-sm-6">
    <h3 class="page-header">Activity Mapping</h3>
    <table class="table table-striped table-hover table-condensed table-fixed">
        <thead>
        <tr>
            <th class="col-sm-2">
                <label>
                    {{Form::checkbox('skip_all', 1, null, ['class' => 'skip-all'])}} Skip
                </label>
            </th>
            <th class="col-sm-5">Original Activity Code</th>
            <th class="col-sm-5">Activity ID</th>
        </tr>
        </thead>
        <tbody>
        @foreach($activity->pluck(0)->unique() as $activityCode)
            <tr>
                <td class="col-sm-6">{{$activityCode}}</td>
                <td class="col-sm-6">
                    <a href="#" class="select-activity-trigger">
                        Select Activity
                    </a>
                    {{Form::hidden("activity[$activityCode]", null, ['class' => 'resource_id'])}}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" tabindex="-1" role="dialog" data-target="" id="SelectActivityModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Select Activity</h4>
            </div>
            <div class="modal-body">
                <div class="form-group form-group-sm">
                    <input type="search"
                           id="activity-search"
                           placeholder="Type here to search for code or cost account"
                           class="form-control"
                    >
                </div>

                <table class="table table-striped table-condensed table-fixed">
                    <thead>
                    <tr>
                        <th class="col-sm-2">Code</th>
                        <th class="col-sm-4">WBS</th>
                        <th class="col-sm-3">Activity</th>
                        <th class="col-sm-3">Cost Account</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($projectActivityCodes as $code)
                        <tr>
                            <td class="col-sm-2">
                                <a href="#" data-dismiss="modal" data-code="{{$code->code}}"
                                   class="select-activity code" data-id="{{$code->id}}">
                                    {{$code->code}}
                                </a>
                            </td>
                            <td class="col-sm-4">{{$code->wbs->name}}</td>
                            <td class="col-sm-3">{{$code->activity}}</td>
                            <td class="col-sm-3">
                                <a href="#" data-dismiss="modal" data-code="{{$code->code}}"
                                   class="select-activity cost-account" data-id="{{$code->id}}">
                                    {{$code->cost_account}}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>


            </div>
        </div>
    </div>
</div>