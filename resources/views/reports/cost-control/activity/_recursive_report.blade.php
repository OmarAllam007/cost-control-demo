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
        <label href="#col-{{$level['id']}}" data-toggle="collapse" style="text-decoration: none;">{{$level['name']}}</label></p>
    <article id="col-{{$level['id']}}" class="tree--child collapse">
        <table class="table table-condensed">
            <thead>
            <tr class="output-cell">
                <td>Base Line</td>
                <td>To Date Cost</td>
                <td>Previous Cost</td>
                <td>Allawable (EV) Cost</td>
                <td>Remaining Cost</td>
                <td>To Date Variance</td>
                <td>At Compeletion Cost</td>
                <td>Cost Variance</td>
            </tr>
            </thead>
            <tbody >
            <tr>

            </tr>
            </tbody>
        </table>
        @if ($level['children'] && count($level['children']))
            <ul class="list-unstyled">
                @foreach($level['children'] as $child)
                    @include('reports.cost-control.boq-report._recursive_report', ['division' => $child, 'tree_level' => $tree_level + 1])
                @endforeach
            </ul>
        @endif



    </article>


</li>