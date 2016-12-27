<li id="full-tree">
    @foreach($data as $wbs_level=>$attributes)

        @if(isset($attributes['parents']))
            @foreach($attributes['parents'] as $key=>$parent)
                <p class="blue-first-level">{{$parent}}</p>
            @endforeach
        @endif

        <div class="tree--item" id="boq-">
            <p class="blue-second-level tree--child">{{$attributes['name']}}</p>
            @if(isset($attributes['boqs']))
                <ul class="list-unstyled">
                    @foreach($attributes['boqs'] as $item=>$boq_details)
                        <article>
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
                                                    <td class="col-md-1">{{$costAccount['LABORS']}}</td>
                                                    <td class="col-md-1">{{$costAccount['MATERIAL']}}</td>
                                                    <td class="col-md-1">{{$costAccount['SUBCONTRACTORS']}}</td>
                                                    <td class="col-md-1">{{$costAccount['EQUIPMENT']}}</td>
                                                    <td class="col-md-1">{{$costAccount['SCAFFOLDING']}}</td>
                                                    <td class="col-md-1">{{$costAccount['OTHERS']}}</td>
                                                    <td class="col-md-2">{{$costAccount['total_resources']}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </article>
                                </div>

                            </li>
                        </article>
                    @endforeach
                </ul>
        </div>
        @endif
    @endforeach
</li>

