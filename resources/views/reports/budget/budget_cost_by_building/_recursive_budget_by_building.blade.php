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

        <a href="#{{$level['id']}}" data-toggle="collapse" @if($tree_level ==0) style="color:white;text-decoration: none" @endif>
            {{$level['name']}}  @if($level['weight']>0) &emsp;||&emsp; {{number_format($level['weight'],2)}} % @endif
        </a>

        <span class="pull-right">{{number_format($level['budget_cost'],2)}} </span>

    </p>

    @if (isset($level['children']) && count($level['children']))
        <article id="{{$level['id']}}" class="tree--child collapse">

            <ul class="list-unstyled">
                @foreach($level['children'] as $child)
                    @include('reports.budget.budget_cost_by_building._recursive_budget_by_building', ['level' => $child, 'tree_level' => $tree_level + 1])
                @endforeach
            </ul>

        </article>

    @endif
</li>