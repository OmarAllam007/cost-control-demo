@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._qs_summery')
@endif
@section('header')
    <h2 class="">{{$project->name}} - Standard Activity</h2>
    <div class="pull-right">
        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#AllModal">
            <i class="fa fa-warning"></i> Concerns
        </button>

        <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i>
            Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
    <style>
        .fixed {
            position: fixed;
            top: 0;
            height: 70px;
            z-index: 1;
        }
        .padding{
            padding-right: 300px;
        }
    </style>
@endsection
@section('body')

    <div class="row" style="margin-bottom: 10px;">
        <form action="{{route('cost.resource_code_report',$project)}}" class="form-inline col col-md-8" method="get">
            {{Form::select('period_id', \App\Period::where('project_id',$project->id)->where('is_open',0)->lists('name','id') ,Session::has('period_id'.$project->id) ? Session::get('period_id'.$project->id) : 'Select Period',  ['placeholder' => 'Choose a Period','class'=>'form-control padding'])}}
            {{Form::submit('Submit',['class'=>'form-control btn-success'],['class'=>'form-control btn-success'])}}
        </form>
        <br>
    </div>

    <ul class="list-unstyled tree">
        @foreach($tree as $parentKey=>$division)
            @include('reports.cost-control.standard_activity._recursive_report', ['division'=>$division,'tree_level'=>0])
        @endforeach
    </ul>
    <input type="hidden" value="{{$project->id}}" id="project_id">

    @if(count($concerns))
        @include('reports.cost-control.standard_activity._concerns')
    @endif
@endsection


@section('javascript')
    <script>

        $(function () {
            var ConcernModal = $('#ConcernModal');
            var ConcernModalForm = ConcernModal.find('form');
            var title = ConcernModal.find('.modal-title');
            var project_id = $('#project_id').val();
            $('.concern-btn').on('click', function (e) {
                e.preventDefault();
                var data = ($(this).attr('data-json'));
                ConcernModal.data('json', data).modal();

            });

            $('.apply_concern').on('click', function (e) {
                e.preventDefault();
                var report_name = 'Standard Activity';
                var body = $('#mytextarea').val();
                var data = ConcernModal.data('json');
                if (body.length != 0) {
                    $.ajax({
                        url: '/concern/' + project_id,
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            info: data,
                            report_name: report_name,
                            comment: body,
                        },
                    }).success((e) => {
                        console.log('success')
                    });
                    ConcernModal.modal('hide');
                }
            })

        })

    </script>
@endsection