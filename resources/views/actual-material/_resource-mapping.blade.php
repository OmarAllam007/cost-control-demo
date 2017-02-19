<div class="col-sm-6">
    <h3 class="page-header">Resources Mapping</h3>
    <table class="table table-striped table-condensed table-fixed">
        <thead>
        <tr>
            <th class="col-sm-3">Resource Code</th>
            <th class="col-sm-5">Name</th>
            <th class="col-sm-2">U.O.M</th>
            <th class="col-sm-2">Resource ID</th>
        </tr>
        </thead>
        <tbody>
        @foreach($resources as $resource)
            <tr>
                <td class="col-sm-3">{{$resource[7]}}</td>
                <td class="col-sm-5">{{$resource[2]}}</td>
                <td class="col-sm-2">{{$resource[3]}}</td>
                <td class="col-sm-2">
                    <a href="#" class="select-resource-trigger">Select Resource</a>
                    {{Form::hidden("resources[{$resource[7]}]", null, ['class' => 'resource_id'])}}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

{{-- Resources Modal --}}
<div class="modal fade" tabindex="-1" role="dialog" id="SelectResourceModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Select Resource</h4>
            </div>
            <div class="modal-body">

                <div class="form-group form-group-sm">
                    <input type="search" id="ResourcesSearch" class="form-control filter-rows">
                </div>

                <table class="table table-condensed table-striped table-fixed">
                    <thead>
                    <tr>
                        <th class="col-sm-2">Code</th>
                        <th class="col-sm-5">Name</th>
                        <th class="col-sm-3">Rate</th>
                        <th class="col-sm-2">U.O.M</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach(App\Resources::forProject($project)->get() as $resource)
                    <tr>
                        <td class="col-sm-2">
                            <a href="#" class="select-resource code" data-id="{{$resource->id}}" data-code="{{$resource->resource_code}}">
                            {{$resource->resource_code}}
                            </a>
                        </td>
                        <td class="col-sm-5">
                            <a href="#" class="select-resource name" data-id="{{$resource->id}}" data-code="{{$resource->resource_code}}">
                            {{$resource->name}}
                            </a>
                        </td>
                        <td class="col-sm-3">
                            {{number_format($resource->rate, 2)}}
                        </td>
                        <td class="col-sm-2">
                            {{$resource->units->type ?? ''}}
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>