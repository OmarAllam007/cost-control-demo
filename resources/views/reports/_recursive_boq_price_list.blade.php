@foreach($data as $wbs_level=>$attributes)
    <li class="list-unstyled">
        <div class="tree--item">
            <p class="blue-second-level">{{$attributes['name']}}</p>
        </div>
        @if(isset($attributes['items']))
            <ul class="list-unstyled">
                @foreach($attributes['items'] as $item=>$boq_details)

                    <li>
                        <div class="tree--item collapse">

                            <p class="blue-third-level">{{$boq_details['boq_name']}}</p>

                            <article id="children-{{$boq_details['id']}}">
                                <table class="table table-condensed table-striped ">
                                    <thead>
                                    <tr class="tbl-content">
                                        <th class="col-md-3">Cost Account</th>
                                        <th class="col-md-1">LABORS</th>
                                        <th class="col-md-1">MATERIAL</th>
                                        <th class="col-md-1">Subcontractors</th>
                                        <th class="col-md-1">EQUIPMENT</th>
                                        <th class="col-md-1">SCAFFOLDING</th>
                                        <th class="col-md-1">OTHERS</th>
                                        <th class="col-md-3">Grand Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr class="tbl-content">
                                        {{--<td class="col-md-3">{{$resource['type'] or ''}}</td>--}}
                                        <td class="col-md-3 ">{{$boq_details['cost_account']}}</td>
                                        <td class="col-md-1">{{number_format($boq_details['LABORS'],2)}}</td>
                                        <td class="col-md-1">{{number_format($boq_details['MATERIAL'],2)}}</td>
                                        <td class="col-md-1">{{number_format($boq_details['Subcontractors']),2}}</td>
                                        <td class="col-md-1">{{number_format($boq_details['EQUIPMENT']),2}}</td>
                                        <td class="col-md-1">{{number_format($boq_details['SCAFFOLDING']),2}}</td>
                                        <td class="col-md-1">{{number_format($boq_details['OTHERS'],2)}}</td>
                                        <td class="col-md-3">{{number_format($boq_details['total_resources'],2)}}</td>
                                    </tr>
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
