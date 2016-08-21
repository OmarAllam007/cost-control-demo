@extends('layouts.app')

@section('header')
    <h2>Productivity</h2>
    <a href="{{ route('productivity.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i>
        Add productivity</a>
@stop

@section('body')
    @if ($productivities->total())
        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th>csi_code</th>
                <th>csi category</th>
                <th>description</th>
                <th>unit</th>
                <th>crew structure</th>
                <th>crew hours</th>
                <th>crew equip</th>
                <th>daily output</th>
                <th>man hours</th>
                <th>equip hours</th>
                <th>reduction factor</th>
                <th>after reduction</th>
                <th>source</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($productivities as $productivity)
                <tr>
                    <td class="col-md-1">{{ $productivity->csi_code }}
                    </td>

                    <td class="col-md-1">{{ $productivity->category->name }}
                    </td>
                    <td class="col-md-1">{{ $productivity->description }}
                    </td>
                    <td class="col-md-1">{{ $productivity->units->type }}
                    </td>
                    <td class="col-md-1">{{ $productivity->crew_structure }}
                    </td>

                    <td class="col-md-1">{{ $productivity->crew_hours }}
                    </td>
                    <td class="col-md-1">{{ $productivity->crew_equip }}
                    </td>
                    <td class="col-md-1">{{ $productivity->daily_output }}
                    </td>

                    <td class="col-md-1">{{ $productivity->man_hours }}
                    </td>
                    <td class="col-md-1">{{ $productivity->equip_hours }}
                    </td>
                    <td class="col-md-1">{{ $productivity->reduction_factor }}
                    </td>

                    <td class="col-md-1">{{ $productivity->productivityAfterReduction() }}
                    </td>

                    <td class="col-md-1">{{ $productivity->source }}
                    </td>

                    <td class="col-md-2">
                        <form action="{{ route('productivity.destroy', $productivity) }}" method="post">
                            {{csrf_field()}} {{method_field('delete')}}
                            <a class="btn btn-sm btn-primary" href="{{ route('productivity.edit', $productivity) }} "><i
                                        class="fa fa-edit"></i> Edit</a>
                            <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $productivities->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No productivity found</strong>
        </div>
    @endif
@stop
