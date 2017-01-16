
<li>
    <div class="tree--item">
        <div class="tree--item--label blue-first-level">
            <a href="#{{str_replace([' ','&','/','.'],'',$key)}}" data-toggle="collapse" style="color: white">{{$key}}</a>
            <strong class="pull-right">{{number_format($division['budget_cost'],2)}}</strong>
        </div>
    </div>
    <ul class="list-unstyled tree collapse" id="{{str_replace([' ','&','/','.'],'',$key)}}">
        <li>
            @if($division['parents'])
                @foreach($division['parents'] as $parent)
                    <div class="tree--item">
                        <div class="tree--item--label blue-second-level">
                            {{$parent['name']}}
                            <strong class="pull-right">{{number_format($division['budget_cost'],2)}}</strong>
                        </div>
                    </div>
                @endforeach
            @endif
                <article class="tree--child">
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
                                    <td>
                                        <span class="pull-right">{{number_format($activity['budget_cost'],2)}}</span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </article>
        </li>
    </ul>
</li>