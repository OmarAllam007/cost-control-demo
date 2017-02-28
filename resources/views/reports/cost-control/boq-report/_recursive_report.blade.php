<li>
    <p class="
       @if($tree_level ==0)
            blue-first-level
         @elseif($tree_level ==1)
            blue-third-level
           @else
            blue-fourth-level
                @endif
            "
    >
        <a href="#col-{{$level['id']}}" data-toggle="collapse"
           style="@if($tree_level==0) color: white;  @elseif($tree_level==1) color: black; @elseif($tree_level==2) color: black; @endif text-decoration: none;">{{$level['name']}}</a>
    </p>
    <article id="col-{{$level['id']}}" class="tree--child collapse">
        @if(count($level['division']))
            <ul class="tree list-unstyled">
                @foreach($level['division'] as $divKey=>$division)
                    <li>
                        <p class="blue-second-level"><label
                                    href="#{{$level['id']}}{{$divKey}}"
                                    data-toggle="collapse">{{$division['name']}}</label></p>
                        <article class="tree--child collapse" id="{{$level['id']}}{{$divKey}}">
{{--                            @if(count($division['activities']))--}}
                                {{--@foreach($division['activities'] as $activity)--}}
                                    @if($division['cost_accounts'])
                                        <ul class="tree list-unstyled">
                                            <li>
                                                <article class="tree--child">
                                                    <table class="table table-condensed">
                                                        <thead>
                                                        <tr>
                                                            <td class="thirdGroup">Boq Item</td>
                                                            <td class="thirdGroup">Item Description</td>
                                                            <td class="firstGroup">Dry Unit / Price</td>
                                                            <td class="firstGroup">Boq Unit Price</td>
                                                            <td class="firstGroup">Budget Unit Rate</td>
                                                            <td class="firstGroup">Todate Budget Unit Rate</td>
                                                            <td class="firstGroup">Variance (Unit Rate)</td>
                                                            <td class="secondGroup">Boq Qty</td>
                                                            <td class="secondGroup">Budget Qty</td>
                                                            <td class="secondGroup">Physical Qty</td>
                                                            <td class="secondGroup">Dry Cost</td>
                                                            <td class="secondGroup">BOQ Cost</td>
                                                            <td class="names">Budget Cost</td>
                                                            <td class="names">Allowable Cost</td>
                                                            <td class="names">Todate Cost</td>
                                                            <td class="names">Todate Cost Var</td>
                                                            <td class="names">Remaining Cost</td>
                                                            <td class="names">At Completion Cost</td>
                                                            <td class="names">At Completion Cost Var</td>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach(collect($division['cost_accounts'])->sortBy('cost_account') as $key=>$cost_account)
                                                            <tr >
                                                                <td class="cell-borders">{{$key}} </td>
                                                                <td class="cell-borders">{{$cost_account['description']}} </td>
                                                                <td class="cell-borders">{{number_format($cost_account['dry']) ?? 0}} </td>
                                                                <td class="cell-borders">{{number_format($cost_account['unit_price']) ?? 0}} </td>
                                                                <td class="cell-borders">{{number_format($cost_account['budget_unit_rate']) ?? 0}} </td>
                                                                <td class="cell-borders">{{number_format($cost_account['todate_budget_unit_rate']) ?? 0}}</td>
                                                                <td class="cell-borders" style="@if($cost_account['var_unit_rate']<0) color: red; @endif">{{number_format($cost_account['var_unit_rate'] ?? 0)}}</td>
                                                                <td class="cell-borders">{{number_format($cost_account['quantity']) ?? 0}}</td>
                                                                <td class="cell-borders">{{number_format($cost_account['budget_qty']) ?? 0}}</td>
                                                                <td class="cell-borders">{{number_format($cost_account['physical_unit']) ?? 0}}</td>
                                                                <td class="cell-borders">{{number_format($cost_account['dry_cost']) ?? 0}}</td>
                                                                <td class="cell-borders">{{number_format($cost_account['boq_cost']) ?? 0}}</td>
                                                                <td class="cell-borders">{{number_format($cost_account['budget_cost']) ?? 0}}</td>
                                                                <td class="cell-borders">{{number_format($cost_account['allowable_cost']) ?? 0}}</td>
                                                                <td class="cell-borders">{{number_format($cost_account['to_date_cost']) ?? 0}}</td>
                                                                <td class="cell-borders" style="@if($cost_account['var_unit_rate']<0) color: red; @endif">{{number_format($cost_account['to_date_cost_var']) ?? 0}}</td>
                                                                <td class="cell-borders">{{number_format($cost_account['remaining_cost']) ?? 0}}</td>
                                                                <td class="cell-borders">{{number_format($cost_account['at_comp']) ?? 0}}</td>
                                                                <td class="cell-borders" style="@if($cost_account['var_unit_rate']<0) color: red; @endif">{{number_format($cost_account['at_comp_var']) ?? 0}}</td>
                                                            </tr>

                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </article>
                                            </li>
                                        </ul>
                                    @endif
                                {{--@endforeach--}}
                            {{--@endif--}}
                        </article>
                    </li>
                @endforeach
            </ul>
        @endif
        @if ($level['children'] && count($level['children']))
            <ul class="list-unstyled">
                @foreach($level['children'] as $child)
                    @include('reports.cost-control.boq-report._recursive_report', ['level' => $child, 'tree_level' => $tree_level + 1])
                @endforeach
            </ul>
        @endif

    </article>


</li>

<div class="modal fade" id="Concern" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <form action="" method="post" class="modal-content">
            {{csrf_field()}} {{method_field('delete')}}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i>
                    Are you sure you want to delete this project?
                </div>
                <input type="hidden" name="wipe" value="1">
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger"><i class="fa fa-fw fa-trash"></i> Delete</button>
            </div>
        </form>
    </div>
</div>