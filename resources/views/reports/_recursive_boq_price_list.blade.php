@foreach($data as $wbs_level=>$attributes)
    <li class="list-unstyled">
        <div class="tree--item" style="background-color: #154360;
  color:white;
  padding: 3px;
  font-weight: bold;">
            <strong>{{$attributes['name']}}</strong>
        </div>
        @if(isset($attributes['items']))
            <ul class="list-unstyled">
            @foreach($attributes['items'] as $item=>$value)
                @foreach($value['cost_accounts'] as $account)
                    <li>
                    <div class="tree--item collapse">

                        <p style="background-color: #154360;
  color:white;
  padding: 3px;
  font-weight: bold;"><strong>{{$item}}</strong></p>

                        <article id="children-{{$value['id']}}">
                            <table class="table table-condensed table-striped " >
                                <thead >
                                <tr class="items-style">
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
                                <tr>
                                    {{--<td class="col-md-3">{{$resource['type'] or ''}}</td>--}}
                                    <td class="col-md-3 ">{{$account['cost_account']}}</td>
                                    <td class="col-md-1">{{number_format($account['LABORS'],2)}}</td>
                                    <td class="col-md-1">{{number_format($account['MATERIAL'],2)}}</td>
                                    <td class="col-md-1">{{number_format($account['Subcontractors']),2}}</td>
                                    <td class="col-md-1">{{number_format($account['EQUIPMENT']),2}}</td>
                                    <td class="col-md-1">{{number_format($account['SCAFFOLDING']),2}}</td>
                                    <td class="col-md-1">{{number_format($account['OTHERS'],2)}}</td>
                                    <td class="col-md-3">{{number_format($account['total_resources'],2)}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </article>
                    </div>

                    </li>
                @endforeach
            @endforeach
            </ul>
        @endif
    </li>
@endforeach
