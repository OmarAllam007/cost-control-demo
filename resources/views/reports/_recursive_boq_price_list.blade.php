<li class="list-unstyled">
    <div class="tree--item">
        <a href="#children-{{$wbs_level}}" class="tree--item--label" data-toggle="collapse"><i
                    class="fa fa-chevron-circle-right"></i> {{$attributes['name']}}
        </a>
        </span>
    </div>
    @if(isset($attributes['items']))
        @foreach($attributes['items'] as $item=>$value)
            @foreach($value['cost_accounts'] as $account)
                <ul class="tree--item collapse">
                    <a href="#children-{{$value['id']}}" class="tree--item--label"><i
                                class="fa fa-chevron-circle-right"></i> {{$item}}
                    </a>
                    </span>
                </ul>
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
                            <th class="col-md-3 bg-success">total_resources</th>
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
                            <td class="col-md-3">{{$account['total_resources']}}</td>
                        </tr>
                </article>

            @endforeach
        @endforeach
    @endif
</li>
