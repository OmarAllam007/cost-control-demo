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
        <a href="#col-{{$level['id']}}" data-toggle="collapse" @if($tree_level ==0) style="color:white;text-decoration: none" @endif>
            {{$level['name']}}
        </a>

        <span class="pull-right">{{number_format($level['level_boq_equavalent_rate'],2)}}</span>

    </p>

        <article id="col-{{$level['id']}}" class="tree--child collapse level-container" data-code="{{mb_strtolower($level['code'])}}">
            @if($level['boqs'] && count($level['boqs']))
            @foreach($level['boqs'] as $item=>$boq_details)
                <ul class="list-unstyled">
                    <p class="blue-third-level">{{$item}}</p>

                    <li>
                        <div class="tree--item">
                            <article>
                                <table class="table table-condensed table-striped ">
                                    <thead>
                                    <tr class="tbl-content">
                                        <th class="col-md-1">Cost Account</th>
                                        <th class="col-md-1">Unit Of Measure</th>
                                        <th class="col-md-1">GENERAL REQUIRMENT</th>
                                        <th class="col-md-1">LABORS</th>
                                        <th class="col-md-1">MATERIAL</th>
                                        <th class="col-md-1">Subcontractors</th>
                                        <th class="col-md-1">EQUIPMENT</th>
                                        <th class="col-md-1">SCAFFOLDING</th>
                                        <th class="col-md-1">OTHERS</th>
                                        <th class="col-md-2">Grand Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($boq_details['items'] as $kCostAccount=>$costAccount)
                                        <tr class="tbl-content">
                                            <td class="col-md-1">{{$costAccount['cost_account']}}</td>
                                            <td class="col-md-1">{{$costAccount['unit']}}</td>
                                            <td class="col-md-1">{{number_format($costAccount['GEN'],2)}}</td>
                                            <td class="col-md-1">{{number_format($costAccount['LAB'],2)}}</td>
                                            <td class="col-md-1">{{number_format($costAccount['MAT'],2)}}</td>
                                            <td class="col-md-1">{{number_format($costAccount['SUB'],2)}}</td>
                                            <td class="col-md-1">{{number_format($costAccount['EQU'],2)}}</td>
                                            <td class="col-md-1">{{number_format($costAccount['SCA'],2)}}</td>
                                            <td class="col-md-1">{{number_format($costAccount['OTH'],2)}}</td>
                                            <td class="col-md-2">{{number_format($costAccount['total_resources'],2)}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </article>
                        </div>

                    </li>
                </ul>
            @endforeach
            @endif
            @if (isset($level['children']) && count($level['children']))
                <ul class="list-unstyled">
                    @foreach($level['children'] as $child)
                        @include('reports.budget.boq_price_list._recursive_report', ['level' => $child, 'tree_level' => $tree_level + 1])
                    @endforeach
                </ul>

        </article>

    @endif
</li>