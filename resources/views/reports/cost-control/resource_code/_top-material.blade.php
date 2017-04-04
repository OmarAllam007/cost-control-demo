@php
$target = trim(slug($name)) . '-' . trim(slug($discipline)) . '-' . trim(slug($topMaterial));
@endphp
<tr class="top-material hidden {{trim(slug($name) . '-' . slug($discipline))}}">
    <td><strong><a href="#" data-target=".{{$target}}"><i class="fa fa-plus-square-o"></i> {{$topMaterial}}</a></strong></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
</tr>
@foreach($topMaterialData as $resource)
    @include('reports.cost-control.resource_code._resource', ['class' => 'top-material-resource', 'slug' => $target])
@endforeach