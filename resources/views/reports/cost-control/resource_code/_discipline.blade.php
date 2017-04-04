<tr class="{{slug($name)}} discipline hidden">
    <td><strong><a href="#" data-target=".{{trim(slug($name) . '-' . slug($discipline))}}"><i class="fa fa-plus-square-o"></i> {{title_case($discipline)}} </a></strong></td>
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

@foreach($disciplineData as $topMaterial => $topMaterialData)
    @if ($topMaterial)
        @include('reports.cost-control.resource_code._top-material')
    @else
        @foreach($topMaterialData as $resource)
            @include('reports.cost-control.resource_code._resource', ['class' => 'resource', 'slug' => slug($name) . '-' . slug($discipline)])
        @endforeach
    @endif
@endforeach