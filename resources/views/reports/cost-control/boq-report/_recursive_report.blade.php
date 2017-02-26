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
                                                        <thead style="background:#95DAC2;color: #000; border-bottom: solid black">
                                                        <tr>
                                                            <td>BOQ Item</td>
                                                            <td>ITEM Description</td>
                                                            <td>BOQ Unit Price</td>
                                                            <td>Boq Qunatity</td>
                                                            <td>Budget Unit Price</td>
                                                            <td>Budget Unit</td>
                                                            <td>Budget Cost</td>
                                                            <td>Todate Boq Unit Price</td>
                                                            {{--todatecost/phiscal unit--}}
                                                            <td>Physical Unit</td>
                                                            <td>Todate Cost</td>
                                                            <td>Allowable Cost</td>
                                                            <td>Todate Cost Var</td>
                                                            {{--allowable_var--}}
                                                            <td>Remaining Cost</td>
                                                            <td>At Completion Cost</td>
                                                            {{--completion_cost--}}
                                                            <td>At Completion Cost Variance</td>
                                                            <td>Concern</td>
                                                            {{--cost_var--}}

                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($division['cost_accounts'] as $cost_account)
                                                            <tr>
                                                                <td>{{$cost_account['cost_account']}} </td>
                                                                <td>{{$cost_account['description']}} </td>
                                                                <td>{{number_format($cost_account['unit_price']) ?? 0}} </td>
                                                                <td>{{number_format($cost_account['quantity']) ?? 0}} </td>
                                                                <td>{{number_format($cost_account['equavlant']) ?? 0}} </td>
                                                                <td>{{number_format($cost_account['budget_unit']) ?? 0}}</td>
                                                                <td>{{number_format($cost_account['budget_cost'] ?? 0)}}</td>
                                                                <td>{{number_format($cost_account['to_date_unit_price']) ?? 0}}</td>
                                                                <td>{{number_format($cost_account['physical_unit']) ?? 0}}</td>
                                                                <td>{{number_format($cost_account['to_date_cost']) ?? 0}}</td>
                                                                <td>{{number_format($cost_account['allowable_cost']) ?? 0}}</td>
                                                                <td>{{number_format($cost_account['to_date_cost_var']) ?? 0}}</td>
                                                                <td>{{number_format($cost_account['remaining_cost']) ?? 0}}</td>
                                                                <td>{{number_format($cost_account['at_comp']) ?? 0}}</td>
                                                                <td>{{number_format($cost_account['at_comp_var']) ?? 0}}</td>


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