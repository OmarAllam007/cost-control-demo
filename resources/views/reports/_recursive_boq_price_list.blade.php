@foreach($data as $wbs_level=>$attributes)
    <li class="list-unstyled">
        <div class="tree--item">
            <strong>{{$attributes['name']}}</strong>
        </div>
        @if(isset($attributes['items']))
            <ul class="list-unstyled">
            @foreach($attributes['items'] as $item=>$value)
                @foreach($value['cost_accounts'] as $account)
                    <li>
                    <div class="tree--item collapse">
                        <strong>{{$item}}</strong>

                        <article id="children-{{$value['id']}}">
                            <table class="table table-condensed table-striped " style="margin: 3px; padding: 5px;">
                                <thead>
                                <tr>
                                    <th class="col-md-3 bg-success">Cost Account</th>
                                    <th class="col-md-1 bg-success">LABORS</th>
                                    <th class="col-md-1 bg-success">MATERIAL</th>
                                    <th class="col-md-1 bg-success">Subcontractors</th>
                                    <th class="col-md-1 bg-success">EQUIPMENT</th>
                                    <th class="col-md-1 bg-success">SCAFFOLDING</th>
                                    <th class="col-md-1 bg-success">OTHERS</th>
                                    <th class="col-md-3 bg-success">Grand Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    {{--<td class="col-md-3">{{$resource['type'] or ''}}</td>--}}
                                    <td class="col-md-3 ">{{$account['cost_account']}}</td>
                                    <td class="col-md-1">{{$account['LABORS']}}</td>
                                    <td class="col-md-1">{{$account['MATERIAL']}}</td>
                                    <td class="col-md-1">{{$account['Subcontractors']}}</td>
                                    <td class="col-md-1">{{$account['EQUIPMENT']}}</td>
                                    <td class="col-md-1">{{$account['SCAFFOLDING']}}</td>
                                    <td class="col-md-1">{{$account['OTHERS']}}</td>
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
