<li>
    <div class="tree--item">
        <div class="tree--item--label
@if($tree_level ==0)
                blue-second-level
        @elseif($tree_level ==1)
                blue-third-level
           @elseif($tree_level ==2)
                blue-fourth-level
           @else
                blue-fourth-level
           @endif
                ">
            {{$division->label}}
            <strong class="pull-right">{{number_format($std_activity_cost[$division->id]['budget_cost'],2)}}</strong>
        </div>
    </div>

    <article id="children-{{$division->id}}" class="tree--child">

        @if ($division->children()->whereIn('id', $all)->get() && $division->children()->whereIn('id', $all)->count())
            <ul class="list-unstyled">
                @foreach($division->children()->whereIn('id', $all)->get() as $child)
                    @include('std-activity._recursive_budget_summery', ['division' => $child ,'tree_level'=>$tree_level+1])
                @endforeach
            </ul>
        @endif

        @if ($division->activities()->whereIn('id',$activity_ids)->get()&& $division->activities()->whereIn('id',$activity_ids)->count())
            <table class="table  table-condensed">
                <thead>
                <tr class="activity-header">
                    <th>Activity</th>
                    <th width="200px"><span class="pull-right">Budget Cost</span></th>
                </tr>
                </thead>
                <tbody>
                @foreach($division->activities()->whereIn('id',$activity_ids)->get() as $activity)
                    <tr>
                        <td>{{$activity->name}}</td>
                        <td><span class="pull-right">{{number_format($activities[$activity->id]['budget_cost'],2)}}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </article>
</li>