<li>
    <div class="tree--item">
        <div class="tree--item--label" style="background-color: #154360;
  color:white;
  padding: 3px;
  font-weight: bold;">
            {{$division->label}}
            <strong class="pull-right">{{$std_activity_cost[$division->id]['budget_cost']}}</strong>
        </div>
    </div>

    <article id="children-{{$division->id}}" class="tree--child">

        @if ($division->children()->whereIn('id', $all)->get() && $division->children()->whereIn('id', $all)->count())
            <ul class="list-unstyled">
                @foreach($division->children()->whereIn('id', $all)->get() as $child)
                    @include('std-activity._recursive_budget_summery', ['division' => $child])
                @endforeach
            </ul>
        @endif

        @if ($division->activities()->whereIn('id',$activity_ids)->get()&& $division->activities()->whereIn('id',$activity_ids)->count())
            <table class="table  table-condensed">
                <thead>
                <tr class="row-shadow items-style">
                    <th>Activity</th>
                    <th width="200px"><span class="pull-right">Budget Cost</span></th>
                </tr>
                </thead>
                <tbody>
                @foreach($division->activities()->whereIn('id',$activity_ids)->get() as $activity)
                    <tr>
                        <td>{{$activity->name}}</td>
                        <td><span class="pull-right">{{$activities[$activity->id]['budget_cost']}}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </article>
</li>