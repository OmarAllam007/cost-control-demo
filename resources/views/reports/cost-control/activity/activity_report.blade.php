@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._standard-activity')
@endif
@section('header')
    <h2 class="">{{$project->name}} - Activity report</h2>
    <div class="pull-right">
        <a href="?print=1" target="_blank" class="btn btn-default btn-sm print"><i class="fa fa-print"></i>
        Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm back">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@endsection
@section('body')
    <style>
        .padding {
            padding-right: 300px;
        }
    </style>
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
        <form action="{{route('cost.activity_report',$project)}}" class="form-inline col col-md-4" method="get">
            {{Form::select('period_id', \App\Period::where('project_id',$project->id)->where('is_open',0)->lists('name','id') ,Session::has('period_id'.$project->id) ? Session::get('period_id'.$project->id) : 'Select Period',  ['placeholder' => 'Choose a Period','class'=>'form-control padding'])}}
            {{Form::submit('Submit',['class'=>'form-control btn-success'],['class'=>'form-control btn-success'])}}
        </form>
        <br>

        <div class="btn-group btn-group-sm  btn-group-block col-md-2">
            <a href="#WBSModal" data-toggle="modal" class="btn btn-default btn-block  tree-open">Select WBS-Level</a>
            <a href="#" class="remove-tree-input-wbs btn btn-warning" data-target="#WBSModal"
               data-label="Select WBS-Level"><span class="fa fa-times-circle"></span></a>

        </div>
        <div class="btn-group btn-group-sm  btn-group-block col-md-2">
            <a href="#ActivitiesModal" data-toggle="modal" class="btn btn-default btn-block  tree-open">Select
                Activity</a>
            <a href="#" class="remove-tree-input-activity btn btn-warning" data-target="#ActivitiesModal"
               data-label="Select Activity"><span class="fa fa-times-circle"></span></a>

        </div>
        <div class="btn-group btn-group-sm  btn-group-block col-md-2" style="text-align: center">
            {{--<button type="button" class=" btn btn-danger col-md-1 negative"  data-toggle="button" aria-pressed="false" >--}}
            {{--Negative Variance--}}
            {{--</button>--}}
            <input type="checkbox" name="checked" class="checkList"
                   value="Negative Variance">
            <p style="margin: 8px;font-size: larger">Negative Variance</p>
        </div>
    </div>
    <ul class="list-unstyled tree">
        @foreach($tree as $level)
            @include('reports.cost-control.activity._recursive_report', ['level'=>$level,'tree_level'=>0])
        @endforeach
    </ul>
    <input type="hidden" value="{{$project->id}}" id="project_id">

    @include('std-activity._modal', ['input' => 'activity', 'value' => 'Select Activity'])
    @include('wbs-level._modal')
@endsection
@section('javascript')
    <script>
        $(function () {
            var global_selector = '';
            var target_td = '';
            var project_id = $('#project_id').val();
            var activity = 0;
            var negative_clicked = 0;
            var wbs = 0;

//            WBS-LEVELS
            $('.wbs-radio').on('change', function () {
                if (this.checked) {
                    var value = $(this).attr('value');
                    global_selector = $('#col-' + value);
                    $('.level-container').removeClass('in').addClass('hidden');
                    global_selector.parents('.level-container').addClass('in').removeClass('hidden');
                    global_selector.addClass('in').removeClass('hidden');
                    global_selector.parents('li').addClass('target').removeClass('hidden');
                    global_selector.children().children().children('article').addClass('in').removeClass('hidden');
//                    $('.level-container').not('.target').parent('li').addClass('hidden');
//                    $('ul.stdreport > li').not('.target').addClass('hidden');
                    wbs=value;
                    activity=0;
                    negative_clicked=0;

                }
            });

            $('.remove-tree-input-wbs').on('click', function () {
                global_selector.parents('.level-container').removeClass('in').removeClass('hidden');
                global_selector.removeClass('in').addClass('hidden');
                global_selector.parents('li').removeClass('target').addClass('hidden');
                global_selector.removeClass('target');
                $('li').not('target').removeClass('hidden');
                $('.level-container').removeClass('in').removeClass('hidden');
                global_selector.children().children().children('article').removeClass('in').addClass('hidden');
                wbs=0;
                activity=0;
                negative_clicked=0;
            });
//            ACTIVITIES

            $('.activity-input').on('change', function () {
                var value = $(this).val();
                target_td = $("tr#activity-" + value);
                target_td.parents('.level-container').addClass('in').removeClass('hidden');
                target_td.addClass('in').removeClass('hidden');
                target_td.parents('li').addClass('target').removeClass('hidden');
//                target_td.parent('tr').css('background-color', '');
                wbs=0;
                activity=value;
                negative_clicked=0;
            });

            $('.remove-tree-input-activity').on('click', function () {
                target_td.parents('.level-container').removeClass('in').removeClass('hidden');
                target_td.removeClass('in').addClass('hidden');
                target_td.parents('li').removeClass('target').addClass('hidden');
                target_td.removeClass('target');
                $('li').not('target').removeClass('hidden');
                $('.level-container').removeClass('in').removeClass('hidden');
//                target_td.parent('tr').css('background-color', 'white');
//                target_td.children().children().children('article').removeClass('in').addClass('hidden');
                wbs=0;
                activity=0;
                negative_clicked=0;
            });


//            COST-ACCOUNTS
            $('.checkList').on('click', function () {
                var negative_rows = $('.negative_var');
                if ($(this).hasClass('clicked')) {
                    negative_rows.each(function () {
                        $(this).parents('.level-container').removeClass('in').removeClass('hidden');
                        $(this).removeClass('in').removeClass('hidden');
                        $(this).parents('li').removeClass('target').removeClass('hidden');
//                        $('ul.stdreport > li').not('.target').removeClass('hidden');
                    });
                    $(this).removeClass('clicked');
                    wbs=0;
                    activity=0;
                    negative_clicked=0;
                }
                else {
                    negative_rows.each(function () {
                        $(this).parents('.level-container').addClass('in').removeClass('hidden');
                        $(this).addClass('in').removeClass('hidden');
                        $(this).parents('li').addClass('target').removeClass('hidden');
                    });
                    $(this).addClass('clicked');
                    wbs=0;
                    activity=0;
                    negative_clicked=1;
                }

            })

            $('.print').on('click',function () {
                sessionStorage.removeItem('negative_var_'+project_id);
                sessionStorage.removeItem('activity_'+project_id);
                sessionStorage.removeItem('wbs_'+project_id);

                sessionStorage.setItem('negative_var_'+project_id,negative_clicked);
                sessionStorage.setItem('activity_'+project_id,activity);
                sessionStorage.setItem('wbs_'+project_id,wbs);
            })
        })
    </script>
    <script src="{{asset('/js/project.js')}}"></script>
    <script src="{{asset('/js/tree-select.js')}}"></script>

@endsection