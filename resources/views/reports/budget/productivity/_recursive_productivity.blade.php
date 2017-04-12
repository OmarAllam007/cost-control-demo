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
        <a href="#{{$category['id']}}" data-toggle="collapse"
           @if($tree_level ==0) style="color:white;text-decoration: none" @endif>
            {{$category['name']}}
        </a>


    </p>

    <article id="{{$category['id']}}" class="tree--child collapse division-container">
        @if ($category['productivities'] && count($category['productivities']))
            <ul class="list-unstyled">
                <li>
                    <table class="table  table-condensed">
                        <thead>
                        <tr class="tbl-header">
                            <th>Productivity Description</th>
                            <th>Crew Structure</th>
                            <th>Unit Of Measure</th>
                            <th>Output</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($category['productivities'] as $keyActivity=>$productivity)

                            <tr >
                                <td>{{$productivity['description']}}</td>
                                <td>
                                    {!!nl2br($productivity['crew_structure'])!!}
                                </td>
                                <td>{{$productivity['unit']}}</td>

                                <td>
                                    {{number_format($productivity['after_reduction'],2)}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>


                </li>
            </ul>
        @endif

        @if (isset($category['children']) && count($category['children']))
            <ul class="list-unstyled">
                @foreach($category['children'] as $child)
                    @include('reports.budget.productivity._recursive_productivity', ['category' => $child, 'tree_level' => $tree_level + 1])
                @endforeach
            </ul>
        @endif


    </article>
</li>