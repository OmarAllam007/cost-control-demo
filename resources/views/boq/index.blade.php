@extends('layouts.app')

@section('header')
    <h2>Boq Items</h2>
    <a href="{{ route('boq.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add
        boq</a>


@stop

@section('body')
    @if ($boqs->total())
        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th>Cost Account</th>
                <th>Name</th>
                <th>Quantity</th>
                <th>Quantity(All)</th>
                <th>DRY U.R.</th>
                <th>PRICE U.R.</th>
                <th>Unit</th>
                <th>DRY (1 BLDG.)</th>
                <th>PRICE (1 BLDG.)</th>
                <th>DRY (ALL BLDG.)</th>
                <th>PRICE (ALL BLDG.)</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($boqs as $boq)
                <tr>

                    <td class="col-md-2">{{ $boq->cost_account?:'' }}</td>
                    <td class="col-md-2">{{ $boq->description?:'' }}</td>
                    <td class="col-md-1">{{ $boq->quantity?:'' }}</td>
                    <td class="col-md-1">{{ $boq->getAllQuantity($boq->quantity)?:'' }}</td>
                    <td class="col-md-1">{{ $boq->getDry($boq->subcon,$boq->materials,$boq->manpower)?:'' }}</td>
                    <td class="col-md-1">{{ $boq->price_ur?:'' }}</td>
                    <td class="col-md-1">{{ isset($boq->unit->type)?$boq->unit->type:'' }}</td>

                    <td class="col-md-1">{{$boq->getDryForBuilding($boq->getDry($boq->subcon,$boq->materials,$boq->manpower),$boq->quantity)  }}</td>
                    <td class="col-md-1">{{$boq->getPriceForBuilding($boq->price_ur,$boq->quantity)?:'' }}</td>
                    <td class="col-md-1">{{ $boq->getDryForAllBuilding($boq->quantity,$boq->getDry($boq->subcon,$boq->materials,$boq->manpower))?:'' }}</td>
                    <td class="col-md-1">{{ $boq->getPriceForAllBuilding($boq->quantity,$boq->price_ur)?:''}}</td>
                    <td class="col-md-3">
                        <form action="{{ route('boq.destroy', $boq) }}" method="post">
                            {{csrf_field()}} {{method_field('delete')}}
                            <a class="btn btn-sm btn-primary" href="{{ route('boq.edit', $boq) }} "><i
                                        class="fa fa-edit"></i> Edit</a>
                            <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $boqs->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No boq found</strong></div>
    @endif
@stop
