                                                                                                               <li id="full-tree">
    @foreach($data as $wbs_level=>$attributes)

        @if(isset($attributes['parents']))
            @foreach($attributes['parents'] as $key=>$parent)
                <p class="blue-first-level"><a href="#{{$key}}" style="color: white;" data-toggle="collapse">{{$parent}}</a></p>
            @endforeach
        @endif

                <p class="blue-second-level tree--child"><a href="#{{$attributes['id']}}" style="color: white;" data-toggle="collapse">{{$attributes['name']}}</a></p>

                @if(isset($attributes['boqs']))
                    <article id="{{$attributes['id']}}" class="collapse">
                        <ul class="list-unstyled">

                            @foreach($attributes['boqs'] as $item=>$boq_details)
                                <p class="blue-third-level">{{$item}}</p>
                                <li>
                                    <div class="tree--item">
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
                            @endforeach
                        </ul>
                    </article>
            @endif
    @endforeach
</li>

