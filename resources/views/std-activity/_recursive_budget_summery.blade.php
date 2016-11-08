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
            {{$division['name']}}
            <strong class="pull-right">{{number_format($division['budget_cost'],2)}}</strong>
        </div>
    </div>

    <article id="children-{{$division['id']}}" class="tree--child">

        @if ($division['activities'])
            <table class="table  table-condensed">
                <thead>
                <tr class="activity-header">
                    <th>Activity</th>
                    <th width="200px"><span class="pull-right">Budget Cost</span></th>
                </tr>
                </thead>
                <tbody>
                @foreach($division['activities'] as $activity)
                    <tr>
                        <td>{{$activity['name']}}</td>
                        <td><span class="pull-right">{{number_format($activity['budget_cost'],2)}}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </article>
</li>