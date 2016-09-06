<div class="form-group tab-actions pull-right">
    <a href="{{route('boq.import', $project->id)}}" class="btn btn-success btn-sm">
        <i class="fa fa-cloud-upload"></i> Import
    </a>

    <a href="{{route('boq.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm">
        <i class="fa fa-plus"></i> Add item
    </a>
</div>

<div class="clearfix"></div>

@if ($project->boqs->count())
    <table class="table table-condensed table-striped">
        <thead>
        <tr>
            <th>Cost Account</th>
            <th>Name</th>
            <th>Quantity</th>
            <th>Quantity(All)</th>
            <th>DRY U.R.</th>
            <th>PRICE U.R.</th>
            <th>DRY (1 BLDG.)</th>
            <th>PRICE (1 BLDG.)</th>
            <th>DRY (ALL BLDG.)</th>
            <th>PRICE (ALL BLDG.)</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>

        @foreach($project->boqs as $boq)
                    <tr>
                        <td class="col-md-1">{{ $boq->cost_account?:'' }}</td>
                        <td class="col-md-2">{{ isset($boq->description)? $boq->description:''}}</td>
                        <td class="col-md-1">{{ isset($boq->quantity)?$boq->quantity:'' }}</td>
                        <td class="col-md-1">{{ $boq->getAllQuantity($boq->quantity)?:'' }}</td>
                        <td class="col-md-1">{{ $boq->getDry($boq->subcon,$boq->materials,$boq->manpower)?:''  }}</td>
                        <td class="col-md-1">{{ $boq->price_ur?:'' }}</td>
                        <td class="col-md-1">{{$boq->getDryForBuilding($boq->dry_ur,$boq->quantity)?:''  }}</td>
                        <td class="col-md-1">{{$boq->getPriceForBuilding($boq->price_ur,$boq->quantity)?:'' }}</td>
                        <td class="col-md-1">{{ $boq->getDryForAllBuilding($boq->quantity,$boq->dry_ur)?:'' }}</td>
                        <td class="col-md-1">{{ $boq->getPriceForAllBuilding($boq->quantity,$boq->price_ur)?:''}}</td>
                        <td class="col-md-3">
                            <form action="{{ route('boq.destroy', $boq) }}" method="post">
                                {{csrf_field()}} {{method_field('delete')}}
                                <a class="btn btn-sm btn-primary" href="{{ route('boq.edit', $boq) }} "><i class="fa fa-edit"></i> Edit</a>
                                <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
        @endforeach
        </tbody>
    </table>

@else
    <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No Boq found</div>
@endif


