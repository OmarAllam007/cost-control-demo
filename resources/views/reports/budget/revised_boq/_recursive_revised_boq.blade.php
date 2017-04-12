    <li>
        <div class="col-md-12 panel panel-default @if($tree_level ==0)
                boqLevelOne
             @elseif($tree_level ==1)
                boqLevelTwo
               @elseif($tree_level ==2)
                boqLevelThree
                @elseif($tree_level==3)
                boqLevelFour
                    @endif
                " >
            <div class="col-md-12  @if($tree_level ==0)
                    boqLevelOne
                 @elseif($tree_level ==1)
                    boqLevelTwo
                   @elseif($tree_level ==2)
                    boqLevelThree
                    @elseif($tree_level==3)
                    boqLevelFour
                        @endif
                    ">
                <div class="col-md-6">
                    <a href="#{{$level['id']}}" data-toggle="collapse"
                       @if($tree_level ==0) style="color:white;text-decoration: none" @endif>
                        {{$level['name']}}
                    </a>
                </div>
                <table class="col-md-6">
                    <thead>
                    <tr>
                        <td class="col-md-3">Original BOQ</td>
                        <td class="col-md-3">Revised BOQ</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="col-md-3">{{number_format($level['original_boq'])}}</td>
                        <td class="col-md-3">{{number_format($level['revised_boq'])}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <article id="{{$level['id']}}" class="tree--child collapse">
            <ul class="list-unstyled">
                <li>
                    @foreach($level['activities'] as $keyActivity=>$activity)
                        <div class="col-md-12 panel panel-default blue-second-level
                                " style="
            padding: 5px; display: inline-block">
                            <div class="col-md-12 blue-second-level">
                                <div class="col-md-6">
                                    <a href="#{{$level['id']}}{{str_replace([' ','(',')','.','/','&',','],'',$activity['name'])}}"
                                       data-toggle="collapse"
                                       @if($tree_level ==0) style="color:white;text-decoration: none" @endif>
                                        {{$activity['name']}}
                                    </a>
                                </div>
                                <table class="col-md-6">
                                    <thead>
                                    <tr>
                                        <td class="col-md-3">Original BOQ</td>
                                        <td class="col-md-3">Revised BOQ</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td class="col-md-3">{{number_format($activity['original_boq'])}}</td>
                                        <td class="col-md-3">{{number_format($activity['revised_boq'])}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        @if($activity['cost_accounts'] && count($activity['cost_accounts']))
                            <article
                                    id="{{$level['id']}}{{str_replace([' ','(',')','.','/','&',','],'',$activity['name'])}}"
                                    class="tree--child collapse">
                                <ul class="list-unstyled">
                                    <li>
                                        <table class="table table-condensed">
                                            <thead>
                                            <tr class="output-header">
                                                <th>Description</th>
                                                <th>COST ACCOUNT</th>
                                                <th>Original BOQ</th>
                                                <th>Revised BOQ</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($activity['cost_accounts'] as $keyCostAccount=>$cost_account)
                                                <tr>
                                                    <td>
                                                        {{$cost_account['description'] ?? ''}}
                                                    </td>
                                                    <td>
                                                        {{$cost_account['cost_account']}}
                                                    </td>

                                                    <td>
                                                        {{number_format($cost_account['original_boq'],2)}}
                                                    </td>

                                                    <td>
                                                        {{number_format($cost_account['revised_boq'],2)}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </li>
                                </ul>
                            </article>
                        @endif
                    @endforeach
                </li>
            </ul>

            @if ($level['children'] && count($level['children']))
            <ul class="list-unstyled">
            @foreach($level['children'] as $child)
            @include('reports.budget.revised_boq._recursive_revised_boq', ['level' => $child, 'tree_level' => $tree_level + 1])
            @endforeach
            </ul>
            @endif


        </article>

    </li>
