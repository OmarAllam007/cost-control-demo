<div class="form-group tab-actions pull-right">
    <a href="{{route('boq.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm">
        <i class="fa fa-plus"></i> Add item
    </a>

    <a href="{{route('boq-division.index')}}" class="btn btn-primary btn-sm">Manage Divisions</a>

    <a href="{{route('boq.import', $project->id)}}" class="btn btn-success btn-sm">
        <i class="fa fa-cloud-upload"></i> Import
    </a>
</div>

<div class="clearfix"></div>

@if ($divisions->count())
    <ul class="list-unstyled tree">
        @foreach($divisions as $division)

            @include('boq-division._recursive2', compact('division'))
        @endforeach
    </ul>
@else

    <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No Boq found</div>
@endif

{{--@if ($project->boqs->count())--}}
    {{--<table class="table table-condensed table-striped table-fixed">--}}
        {{--<thead>--}}
        {{--<tr>--}}
            {{--<th class="col-xs-1">Cost Account</th>--}}
            {{--<th class="col-xs-1">Name</th>--}}
            {{--<th class="col-xs-1">Quantity</th>--}}
            {{--<th class="col-xs-1">Quantity(All)</th>--}}
            {{--<th class="col-xs-1">DRY U.R.</th>--}}
            {{--<th class="col-xs-1">PRICE U.R.</th>--}}
            {{--<th class="col-xs-1">DRY (1 BLDG.)</th>--}}
            {{--<th class="col-xs-1">PRICE (1 BLDG.)</th>--}}
            {{--<th class="col-xs-1">DRY (ALL BLDG.)</th>--}}
            {{--<th class="col-xs-1">PRICE (ALL BLDG.)</th>--}}
            {{--<th class="col-xs-2">Actions</th>--}}
        {{--</tr>--}}
        {{--</thead>--}}
        {{--<tbody>--}}

        {{--@foreach($project->boqs as $boq)--}}
                    {{--<tr>--}}
                        {{--<td class="col-xs-1">{{ $boq->cost_account?:'' }}</td>--}}
                        {{--<td class="col-xs-1">{{ isset($boq->description)? $boq->description:''}}</td>--}}
                        {{--<td class="col-sm-1">{{ isset($boq->quantity)?$boq->quantity:'' }}</td>--}}
                        {{--<td class="col-xs-1">{{ $boq->getAllQuantity($boq->quantity)?:'' }}</td>--}}
                        {{--<td class="col-xs-1">{{ $boq->getDry($boq->subcon,$boq->materials,$boq->manpower)?:''  }}</td>--}}
                        {{--<td class="col-xs-1">{{ $boq->price_ur?:'' }}</td>--}}
                        {{--<td class="col-xs-1">{{$boq->getDryForBuilding($boq->getDry($boq->subcon,$boq->materials,$boq->manpower),$boq->quantity)?:''  }}</td>--}}
                        {{--<td class="col-xs-1">{{$boq->getPriceForBuilding($boq->price_ur,$boq->quantity)?:'' }}</td>--}}
                        {{--<td class="col-xs-1">{{ $boq->getDryForAllBuilding($boq->quantity,$boq->getDry($boq->subcon,$boq->materials,$boq->manpower))?:'' }}</td>--}}
                        {{--<td class="col-xs-1">{{ $boq->getPriceForAllBuilding($boq->quantity,$boq->price_ur)?:''}}</td>--}}
                        {{--<td class="col-xs-2">--}}
                            {{--<form action="{{ route('boq.destroy', $boq) }}" method="post">--}}
                                {{--{{csrf_field()}} {{method_field('delete')}}--}}
                                {{--<a class="btn btn-sm btn-primary" href="{{ route('boq.edit', $boq) }} "><i class="fa fa-edit"></i> Edit</a>--}}
                                {{--<button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>--}}
                            {{--</form>--}}
                        {{--</td>--}}
                    {{--</tr>--}}
        {{--@endforeach--}}
        {{--</tbody>--}}
    {{--</table>--}}

{{--@else--}}
    {{--<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No Boq found</div>--}}
{{--@endif--}}


