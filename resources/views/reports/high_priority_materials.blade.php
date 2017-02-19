@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._high_priority_materials')
@endif
@section('header')
    <h2>High Priority Materials</h2>
    <div class="pull-right">
        <a href="?print=1&paint=high-priority" target="_blank" class="btn btn-default btn-sm"><i
                    class="fa fa-print"></i> Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@endsection
@section('image')
    <img src="{{asset('images/reports/high-priority.jpg')}}">
@endsection
@section('body')

    <form action="{{route('high_priority.report',$project)}}" method="get">
        @if($visible || $generate)
            <table class="table table-condensed">
                <thead>
                <tr class="tbl-children-division">
                    <th class="col-xs-1"></th>
                    <th class="col-xs-5" >Description</th>
                    <th class="col-xs-2">Budget Cost</th>
                    <th class="col-xs-2">Budget Unit</th>
                    <th class="col-xs-2">Unit</th>

                </tr>
                </thead>
                <tbody>
                @foreach($data as $key=>$row)

                    <tr class="tbl-content">
                        <td class="col-sm-1">@if($button) @else  <input type="checkbox" name="checked[]"
                                                                        value="{{$key}}"> @endif</td>

                        <td class="col-xs-4">{{$row['name']}}</td>
                        <td class="col-xs-2">{{number_format($row['budget_cost'],2)}}</td>
                        <td class="col-xs-2">{{number_format($row['budget_unit'],2)}}</td>
                        <td class="col-xs-2">{{$row['unit']}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @if($button) @else<input type="submit" value="Select" class="btn btn-success">@endif
        @else
            @foreach($data as $key=>$row)
                <p class="blue-third-level">{{$row['name']}} <span
                            class="pull-right  badge col-md-1">{{number_format($row['budget_cost'],2)}}</span>}</p>
                @foreach($row['resources'] as $resource)
                    <p class="blue-fourth-level tree--item"><input type="checkbox" name="resources[]"
                                                                   value="{{$resource['resource_id']}}">{{$resource['name']}}
                        <span class="pull-right badge col-md-1">{{number_format($resource['budget_cost'],2)}}</span> {{number_format($resource['budget_cost'],2)}}
                    </p>  <br>
                @endforeach
            @endforeach
            <input type="submit" value="Generate" class="btn btn-success">
        @endif
        <br>
    </form>


    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="exampleModalLabel">Description</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <textarea class="texta form-control" title="new"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>

@section('javascript')

    <script type="text/javascript">

        $('.accept').on('click', function () {
            $(this).parent('td').attr('contenteditable', 'false');
            $(this).hide();
            $('.edit').show();
        });

        $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var recipient = button.data('whatever')
            var modal = $(this)
            var value = button.attr('value');
            modal.find('.modal-body textarea').val(recipient);
            $('#exampleModal').on('hidden.bs.modal', function () {
                var areaText = modal.find('.modal-body textarea').val();
                var value = button.attr('value');
                $('button[value='+value+']').parent('td').next('td').html(' ' + areaText);
            })

        })



    </script>
@endsection
@stop
