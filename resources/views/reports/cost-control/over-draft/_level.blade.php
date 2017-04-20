@php $depth = $depth ?? 0 @endphp
<tr class="success level-{{$depth}} {{slug($level['parent'])}} {{$depth? 'hidden' : ''}}">
    <td class="boq-cell"><a href="#" class="open-level" data-target=".{{slug($level['name'])}}"><i class="fa fa-plus-circle"></i> {{$level['name']}}</a></td>
    <td class="price-cell"></td>
    <td class="price-cell"></td>
    <td class="price-cell"></td>
    <td class="price-cell"></td>
    <td class="price-cell"></td>
    <td class="price-cell"></td>
    <td class="price-cell"></td>
    <td class="price-cell"></td>
    <td class="price-cell"></td>
</tr>

@php
$children = $tree->where('parent', $level['name']);
@endphp

@foreach($children as $child)
    @include('reports.cost-control.over-draft._level', ['level' => $child, 'depth' => $depth + 1])
@endforeach

@if (!empty($level['boqs']))
    @foreach($level['boqs'] as $boq)
        @php
        $var = $boq->actual_revenue - $boq->physical_revenue;
        $var_upv = $boq->actual_revenue - $boq->physical_revenue_upv;
        @endphp
        <tr class="boq-row level-{{$depth + 1}} {{slug($level['name'])}} hidden">
            <td class="boq-cell">{{$boq->description}}</td>
            <td class="price-cell">{{number_format($boq->boq_quantity, 2)}}</td>
            <td class="price-cell">{{number_format($boq->boq_unit_price, 2)}}</td>
            <td class="price-cell">{{number_format($boq->physical_unit, 4)}}</td>
            <td class="price-cell">{{number_format($boq->physical_unit_upv, 4)}}</td>
            <td class="price-cell">{{number_format($boq->physical_revenue, 2)}}</td>
            <td class="price-cell">{{number_format($boq->physical_revenue_upv, 2)}}</td>
            <td class="price-cell">{{number_format($boq->actual_revenue, 2)}}</td>
            <td class="price-cell {{$var < 0? 'text-danger' : 'text-success'}}">{{number_format($var, 2)}}</td>
            <td class="price-cell {{$var_upv < 0? 'text-danger' : 'text-success'}}">{{number_format($var_upv, 2)}}</td>
        </tr>
    @endforeach
@endif