<li>
    <div class="tree--item">
        <a href="#children-{{$division->id}}" class="tree--item--label" data-toggle="collapse"><i class="fa fa-chevron-circle-right"></i> {{$division->label}}
        </a>
        <span class="tree--item--actions">

            <a href="{{route('activity-division.edit', $division)}}" class="label label-primary"><i class="fa fa-pencil"></i> Edit</a>
        </span>
    </div>
    <article id="children-{{$division->id}}" class="tree--child collapse">
        @if ($division->activities->count())
            <table class="table table-striped table-hover table-condensed">
                <thead>
                <tr>
                    <th class="col-md-8">Activity</th>
                    <th>
                        <div class="pull-right">
                            Actions
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($division->activities as $activity)
                    <tr>
                        <td>{{$activity->name}}</td>
                        <td>
                            <div class="pull-right">
                                <a href="{{route('std-activity.edit', $activity)}}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif

        @if ($division->children && $division->children->count())
            <ul class="list-unstyled">
                @foreach($division->children as $child)
                    @include('activity-division._recursive', ['division' => $child])
                @endforeach
            </ul>
        @endif


    </article>
</li>