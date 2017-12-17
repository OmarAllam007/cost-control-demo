@php $depth = $depth ?? 0 @endphp
<tr class="success level-{{$depth}} child-{{$level['parent_id']}} {{$depth? 'hidden' : ''}}">
    <td class="boq-cell"><a href="#" class="open-level" data-target=".child-{{$level['id']}}"><i class="fa fa-plus-circle"></i> {{$level['name']}}</a></td>
    <td class="price-cell"></td>
    <td class="price-cell"></td>
    <td class="price-cell"></td>
    <td class="price-cell"></td>
    <td class="price-cell">{{number_format($level->physical_revenue, 2)}}</td>
    <td class="price-cell">{{number_format($level->physical_revenue_upv, 2)}}</td>
    <td class="price-cell">{{number_format($level->actual_revenue, 2)}}</td>
    <td class="price-cell {{$level->var < 0? 'text-danger' : 'text-success'}}">{{number_format($level->var, 2)}}</td>
    <td class="price-cell {{$level->var_upv < 0? 'text-danger' : 'text-success'}}">{{number_format($level->var_upv, 2)}}</td>
</tr>

@foreach($level->subtree as $child)
    @include('reports.cost-control.over-draft._level', ['level' => $child, 'depth' => $depth + 1])
@endforeach

@if (!empty($level['boqs']))
    @foreach($level['boqs'] as $boq)
        <tr class="boq-row level-{{$depth + 1}} child-{{$level['id']}} hidden">
            <td class="boq-cell">{{$boq->description}}</td>
            <td class="price-cell">{{number_format($boq->boq_quantity, 2)}}</td>
            <td class="price-cell">{{number_format($boq->boq_unit_price, 2)}}</td>
            <td class="price-cell">{{number_format($boq->physical_unit, 4)}}</td>
            <td class="price-cell">{{number_format($boq->physical_unit_upv, 4)}}</td>
            <td class="price-cell">{{number_format($boq->physical_revenue, 2)}}</td>
            <td class="price-cell">{{number_format($boq->physical_revenue_upv, 2)}}</td>
            <td class="price-cell">{{number_format($boq->actual_revenue, 2)}}</td>
            <td class="price-cell {{$boq->var < 0? 'text-danger' : 'text-success'}}">{{number_format($boq->var, 2)}}</td>
            <td class="price-cell {{$boq->var_upv < 0? 'text-danger' : 'text-success'}}">{{number_format($boq->var_upv, 2)}}</td>
        </tr>
    @endforeach
@endif