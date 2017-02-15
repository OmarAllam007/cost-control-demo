<li>

    <div class="col-md-12 panel panel-default @if($tree_level ==0)
            blue-first-level
         @elseif($tree_level ==1)
            blue-third-level
           @else
            blue-fourth-level
                @endif
            " style="
            padding: 5px; display: inline-block">
        <div class="col-md-12  @if($tree_level ==0)
                blue-first-level
             @elseif($tree_level ==1)
                blue-third-level
               @else
                blue-fourth-level
                    @endif
                ">
            <div class="col-md-6">
                <a href="#{{$level['id']}}" data-toggle="collapse" @if($tree_level ==0) style="color:white;text-decoration: none" @endif>
                    {{$level['name']}}
                </a>
            </div>
                <table class="col-md-6">
                    <thead>
                    <tr>
                        <td class="col-md-2">Dry Cost</td>
                        <td class="col-md-2">Budget Cost</td>
                        <td class="col-md-2">Different</td>
                        <td class="col-md-2">Increase</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="col-md-2" >{{number_format($level['dry_cost'],2)}}</td>
                        <td class="col-md-2">{{number_format($level['budget_cost'],2)}}</td>
                        <td class="col-md-2" @if($level['different']<0) style="color: #dd1144;" @endif>{{number_format($level['different'],2)}}</td>
                        <td class="col-md-2">{{number_format($level['increase'])}} %</td>
                    </tr>
                    </tbody>
                </table>
            </div>


    </div>

    @if (isset($level['children']) && count($level['children']))
        <article id="{{$level['id']}}" class="tree--child collapse">

            <ul class="list-unstyled">
                @foreach($level['children'] as $child)
                    @include('reports.budget.budget_cost_dry_building._recursive_budget_cost_dry_building', ['level' => $child, 'tree_level' => $tree_level + 1])
                @endforeach
            </ul>

        </article>

    @endif
</li>
