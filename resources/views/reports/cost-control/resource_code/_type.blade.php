<tr>
    <td><strong><a href="#" data-target=".{{slug($name)}}" data-toggle="collapse"><i class="fa fa-plus-square-o"></i> {{$name}} </a></strong></td>
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

@foreach ($typeData as $discipline => $disciplineData)
    @include('reports.cost-control.resource_code._discipline', ['discipline' => $discipline ?: 'General'])
@endforeach