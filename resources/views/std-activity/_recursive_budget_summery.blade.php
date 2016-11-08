<li>
    <div class="tree--item">
        <div class="tree--item--label blue-first-level">
            {{$division['name']}}
            <strong class="pull-right">{{number_format($division['budget_cost'],2)}}</strong>
        </div>
    </div>
    <ul class="list-unstyled tree">
        <li>
            @if ($division['divisions'])
                @foreach($division['divisions'] as $div)
                    <div class="tree--item">
                        <div class="tree--item--label blue-second-level">
                            {{$div['division_name']}}
                            <strong class="pull-right">{{number_format($div['budget_cost'],2)}}</strong>
                            </div>
                        </div>
                            <article id="children-{{$division['id']}}" class="tree--child">
                                @if ($div['activities'])
                                    <table class="table  table-condensed">
                                        <thead>
                                        <tr class="activity-header">
                                            <th>Activity</th>
                                            <th width="200px"><span class="pull-right">Budget Cost</span></th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach($div['activities'] as $activity)
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
                @endforeach
            @endif
        </li>
    </ul>
</li>