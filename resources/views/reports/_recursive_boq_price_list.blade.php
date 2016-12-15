<li>
    @foreach($data as $wbs_level=>$attributes)

        @foreach($attributes['parents'] as $key=>$parent)
            <p class="blue-first-level">{{$parent}}</p>
        @endforeach


        <div class="tree--item">
            <p class="blue-second-level tree--child">{{$attributes['name']}}</p>
        </div>

        @if(isset($attributes['boqs']))
            <ul class="list-unstyled">

                @foreach($attributes['boqs'] as $item=>$boq_details)

                    <p class="blue-third-level">{{$item}}</p>
                    <li>
                        <div class="tree--item collapse">


                            <article>
                                <table class="table table-condensed table-striped ">
                                    <thead>
                                    <tr class="tbl-content">
                                        <th class="col-md-2">Cost Account</th>
                                        <th class="col-md-2">Unit Of Measure</th>
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
                                            <td class="col-md-2 ">{{$costAccount['cost_account']}}</td>
                                            <td class="col-md-2 ">{{$costAccount['unit']}}</td>
                                            <td class="col-md-1">{{number_format($costAccount['LABORS'],2)}}</td>
                                            <td class="col-md-1">{{number_format($costAccount['MATERIAL'],2)}}</td>
                                            <td class="col-md-1">{{number_format($costAccount['SUBCONTRACTORS']),2}}</td>
                                            <td class="col-md-1">{{number_format($costAccount['EQUIPMENT']),2}}</td>
                                            <td class="col-md-1">{{number_format($costAccount['SCAFFOLDING']),2}}</td>
                                            <td class="col-md-1">{{number_format($costAccount['OTHERS'],2)}}</td>
                                            <td class="col-md-2">{{number_format($costAccount['total_resources'],2)}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </article>
                        </div>

                    </li>
                @endforeach
            </ul>

        @endif
</li>
@endforeach

