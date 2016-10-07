<li>
    <div class="tree--item">
        <a href="#children-{{$division->id}}" class="tree--item--label" data-toggle="collapse"><i
                    class="fa fa-chevron-circle-right"></i> {{$division->label}}
<i class="pull-right">{{$std_activity_cost[$division->id]['budget_cost']}}
</i>
        </a>


    </div>

    <article id="children-{{$division->id}}" class="tree--child collapse">

        @if ($division->children()->whereIn('id', $all)->get() && $division->children()->whereIn('id', $all)->count())
            <ul class="list-unstyled">
                @foreach($division->children()->whereIn('id', $all)->get() as $child)
                    @include('std-activity._recursive_budget_summery', ['division' => $child])
                @endforeach
            </ul>
        @endif

        @if ($division->activities()->whereIn('id',$activity_ids)->get()&& $division->activities()->whereIn('id',$activity_ids)->count())
            <table class="table table-striped table-hover table-condensed">
                <thead>
                <tr>
                    <th class="col-md-8">Activity</th>
                    <th class="col-md-8">Budget Cost</th>

                </tr>
                </thead>
                <tbody>
                @foreach($division->activities()->whereIn('id',$activity_ids)->get() as $activity)
                    <tr>
                        <td>{{$activity->name}}</td>
                        <td>{{$activities[$activity->id]['budget_cost']}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </article>


</li>