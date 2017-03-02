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
        <a href="#{{$division['id']}}" data-toggle="collapse"
           @if($tree_level ==0) style="color:white;text-decoration: none" @endif>
            {{$division['name']}}
        </a>


    </p>

    <article id="{{$division['id']}}" class="tree--child collapse">

        @if (collect($division['activities'])->sortBy('name') && count($division['activities']))
            @foreach($division['activities'] as $keyActivity=>$activity)
                <ul class="list-unstyled">
                    <li>
                        <p class="blue-fourth-level">
                            <a href="#{{$activity['id']}}" data-toggle="collapse">
                                {{$activity['name']}}
                            </a>

                        </p>
                        <article id="{{$activity['id']}}" class="tree--child collapse">
                            @if(count($activity['resources']))
                                <ul class="list-unstyled">
                                    <li>
                                        <table class="table table-condensed">
                                            <thead>
                                            <tr class="tbl-children-division">
                                                <th>Base Line</th>
                                                <th>Previous Cost</th>
                                                <th>Previous allowable</th>
                                                <th>Previous Variance</th>
                                                <th>To Date Cost</th>
                                                <th>Allowable (EV) Cost</th>
                                                <th>To Date Variance</th>
                                                <th>Remaining Cost</th>
                                                <th>At Compeletion Cost</th>
                                                <th>Cost Variance</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{number_format($activity['budget_cost']??0,2) }}</td>
                                                    <td>{{number_format($activity['prev_cost']??0,2)}}</td>
                                                    <td>{{number_format($activity['prev_allowabe']??0,2)}}</td>
                                                    <td>{{number_format($activity['prev_variance']??0,2)}}</td>
                                                    <td>{{number_format($activity['to_data_cost']?? 0)}}</td>
                                                    <td>{{number_format($activity['to_date_allowable_cost']??0,2)}}</td>
                                                    <td>{{number_format($activity['allowable_var']??0,2)}}</td>
                                                    <td>{{number_format($activity['remain_cost']??0,2)}}</td>
                                                    <td>{{number_format($activity['completion_cost']??0,2)}}</td>
                                                    <td>{{number_format($activity['cost_var']??0,2)}}</td>
                                                   <td><a type="button" href="#" class="btn btn-primary btn-lg concern-btn"
                                                       title="{{$activity['name']}}"
                                                       data-json="{{json_encode($activity)}}">
                                                        <i class="fa fa-pencil-square-o " aria-hidden="true"></i>
                                                    </a></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </li>
                                </ul>
                            @endif
                        </article>
                    </li>
                </ul>
            @endforeach
        @endif
        @if (isset($division['children']) && count($division['children']))
            <ul class="list-unstyled">
                @foreach($division['children'] as $child)
                    @if(count($child['activities']))
                        @include('reports.cost-control.standard_activity._recursive_report', ['division' => $child, 'tree_level' => $tree_level + 1])
                    @endif
                @endforeach
            </ul>
        @endif

    </article>
</li>

<div class="modal" id="ConcernModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <form action="" class="modal-content">
            {{csrf_field()}} {{method_field('post')}}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Add Concern</h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label for="message-text" class="control-label">Comment:</label>
                    <textarea class="form-control" id="mytextarea"></textarea>
                </div>
                <div class="form-group">
                    <button class="btn btn-success apply_concern" data-dismiss="modal"><i class="fa fa-plus"></i>
                        Add Concern
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
