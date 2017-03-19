@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._standard-activity')
@endif
@section('header')
    <h2 class="">{{$project->name}} - Standard Activity</h2>
    <div class="pull-right">
        {{--<button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#AllModal">--}}
            {{--<i class="fa fa-warning"></i> Concerns--}}
        {{--</button>--}}

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
        .checkList {
            width: 28px;
            height: 28px;
            position: relative;
            margin: 20px auto;
            background: #fcfff4;
            background: linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
            box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0, 0, 0, 0.5);

        label {
            width: 20px;
            height: 20px;
            position: absolute;
            top: 4px;
            left: 4px;
            cursor: pointer;
            background: linear-gradient(top, #222 0%, #45484d 100%);
            box-shadow: inset 0px 1px 1px rgba(0, 0, 0, 0.5), 0px 1px 0px rgba(255, 255, 255, 1);



        &
        :hover::after {
            opacity: 0.3;
        }

        }
        input[type=checkbox] {
            visibility: hidden;

        &
        :checked + label:after {
            opacity: 1;
        }

        }
        }
    </style>
@endsection
@section('body')
    <div class="col-md-12 panel panel-default boqLevelFour">
        <div class="col-md-12 boqLevelFour">
            <table class="col-md-12">
                <thead>
                <tr style="text-align: center">
                    <td>Base Line</td>
                    <td>Previous Cost</td>
                    <td>Previous Allowable</td>
                    <td>Previous Var</td>
                    <td>To Date Cost</td>
                    <td>Allowable (EV) Cost</td>
                    <td>Remaining Cost</td>
                    <td>To Date Variance</td>
                    <td>At Completion Cost</td>
                    <td>Cost Variance</td>
                </tr>
                </thead>
                <tbody>
                <tr style="text-align: center">
                    <td>{{number_format($total['budget_cost']??0,2) }}</td>
                    <td>{{number_format($total['prev_cost']??0,2)}}</td>
                    <td>{{number_format($total['prev_allowable']??0,2)}}</td>
                    <td>{{number_format($total['prev_variance']??0,2)}}</td>
                    <td>{{number_format($total['to_data_cost']?? 0,2)}}</td>
                    <td>{{number_format($total['to_date_allowable_cost']??0,2)}}</td>
                    <td>{{number_format($total['allowable_var']??0,2)}}</td>
                    <td>{{number_format($total['remain_cost']??0,2)}}</td>
                    <td>{{number_format($total['completion_cost']??0,2)}}</td>
                    <td style=" @if($total['cost_var'] <0)  color: red; @endif ">{{number_format($total['cost_var']??0,2)}}</td>
                </tr>
                </tbody>
            </table>
        </div>


    </div>

    <div class="row" style="margin-bottom: 10px;">
        <form action="{{route('cost.standard_activity_report',$project)}}" class="form-inline col col-md-4" method="get">
            {{Form::select('period_id', \App\Period::where('project_id',$project->id)->where('is_open',0)->lists('name','id') ,Session::has('period_id'.$project->id) ? Session::get('period_id'.$project->id) : 'Select Period',  ['placeholder' => 'Choose a Period','class'=>'form-control padding'])}}
            {{Form::submit('Submit',['class'=>'form-control btn-success'],['class'=>'form-control btn-success'])}}
        </form>

        <div class="btn-group btn-group-sm  btn-group-block col-md-2">
            <a href="#ActivitiesModal" data-toggle="modal" class="btn btn-default btn-block  tree-open">Select Activity</a>
            <a href="#" class="remove-tree-input btn btn-warning" data-target="#ActivitiesModal" data-label="Select Activity"><span class="fa fa-times-circle"></span></a>

        </div>
        <div class="btn-group btn-group-sm  btn-group-block col-md-2" style="text-align: center">
        {{--<button type="button" class=" btn btn-danger col-md-1 negative"  data-toggle="button" aria-pressed="false" >--}}
           {{--Negative Variance--}}
        {{--</button>--}}
            <input type="checkbox" name="checked" class="checkList"
                   value="Negative Variance" > <p style="margin: 8px;font-size: larger">Negative Variance</p>
        </div>
    </div>


    <ul class="list-unstyled tree stdreport">
        @foreach($tree as $parentKey=>$division)
            @include('reports.cost-control.standard_activity._recursive_report', ['division'=>$division,'tree_level'=>0])
        @endforeach
    </ul>
    <input type="hidden" value="{{$project->id}}" id="project_id">

    @include('std-activity._modal', ['input' => 'activity', 'value' => 'Select Activity'])

    @if(count($concerns))
        @include('reports.cost-control.standard_activity._concerns')
    @endif
@endsection


@section('javascript')
    <script>

        $(function () {
            var global_selector = '';
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
                            comment: body
                        },
                    }).success((e)=> {
                        console.log('success')
                    });
                    ConcernModal.modal('hide');
                }
            })
            $('.tree-radio').on('change', function(){
                if (this.checked) {
                    var value = $(this).attr('value');
                    global_selector = $('#activity-'+value);
                    $('.divison-container,.activity-container').removeClass('in').addClass('hidden');
                    global_selector.parents('.divison-container,.activity-container').addClass('in').removeClass('hidden');
                    global_selector.addClass('in target').removeClass('hidden');
                    global_selector.parents('li').addClass('target').removeClass('hidden');
                    $('.activity-container').not('.target').parent('li').addClass('hidden');
                    $('ul.stdreport > li').not('.target').addClass('hidden');
                }
            });

            $('.remove-tree-input').on('click',function () {
                global_selector.parents('.divison-container,.activity-container').removeClass('in').removeClass('hidden');
                global_selector.removeClass('in').addClass('hidden');
                global_selector.parents('li').removeClass('target').addClass('hidden');
                global_selector.removeClass('target');
                $('li').not('target').removeClass('hidden');
                $('.divison-container,.activity-container').removeClass('in').removeClass('hidden');
                $('li.target').removeClass('target');
                $('ul.stdreport > li').not('.target').show();
            })

            $('.checkList').on('click',function () {
                var articles =$('.negative_var');
                articles.each(function () {
                    if($(this).hasClass('clicked')){
                        $(this).parents('.divison-container,.activity-container').removeClass('in').removeClass('hidden');
                        $(this).removeClass('in').removeClass('hidden');
                        $(this).parents('li').removeClass('target').removeClass('hidden');
                        $(this).removeClass('clicked');
                        $('ul.stdreport > li').not('.target').removeClass('hidden');
                        $(this).css('background-color:#FF574B');


                    }
                    else{
                        $(this).parents('.divison-container,.activity-container').addClass('in').removeClass('hidden');
                        $(this).addClass('in').removeClass('hidden');
                        $(this).parents('li').addClass('target').removeClass('hidden');
                        $(this).addClass('clicked');
                        $('ul.stdreport > li').not('.target').addClass('hidden');
                        $(this).css('background-color:#990025');
                    }
//                    $('.divison-container,.activity-container').removeClass('in').addClass('hidden');

                });

            })

        })

    </script>
    <script src="{{asset('/js/project.js')}}"></script>
    {{--    <script src="{{asset('/js/resources.js')}}"></script>--}}
    <script src="{{asset('/js/tree-select.js')}}"></script>
@endsection