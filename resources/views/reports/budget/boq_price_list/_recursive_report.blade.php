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
        <a href="#{{$level['id']}}" data-toggle="collapse" @if($tree_level ==0) style="color:white;text-decoration: none" @endif>
            {{$level['name']}}
        </a>

    </p>

        <article id="{{$level['id']}}" class="tree--child collapse">
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
                                            <td class="col-md-1">{{number_format($costAccount['GENERAL REQUIRMENT'],2)}}</td>
                                            <td class="col-md-1">{{number_format($costAccount['LABORS'],2)}}</td>
                                            <td class="col-md-1">{{number_format($costAccount['MATERIAL'],2)}}</td>
                                            <td class="col-md-1">{{number_format($costAccount['SUBCONTRACTORS'],2)}}</td>
                                            <td class="col-md-1">{{number_format($costAccount['EQUIPMENT'],2)}}</td>
                                            <td class="col-md-1">{{number_format($costAccount['SCAFFOLDING'],2)}}</td>
                                            <td class="col-md-1">{{number_format($costAccount['OTHERS'],2)}}</td>
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