@extends('layouts.' . (request('print')? 'print' : 'app'))

@section('header')
    <h2 class="">{{$project->name}} - BOQ Report</h2>
    <div class="pull-right">
        <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i>
            Print</a>
        <a href="{{route('project.cost-control', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@endsection

@section('body')

    <style>
        .fixed {
            position: fixed;
            top: 0;
            height: 70px;
            z-index: 1;
        }

        .padding {
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
    <div class="row" style="margin-bottom: 10px;">
        <form action="{{route('cost.standard_activity_report',$project)}}" class="form-inline col col-md-4"
              method="get">
            {{Form::select('period_id', \App\Period::where('project_id',$project->id)->where('is_open',0)->lists('name','id') ,Session::has('period_id'.$project->id) ? Session::get('period_id'.$project->id) : 'Select Period',  ['placeholder' => 'Choose a Period','class'=>'form-control padding'])}}
            {{Form::submit('Submit',['class'=>'form-control btn-success'],['class'=>'form-control btn-success'])}}
        </form>

        <div class="btn-group btn-group-sm  btn-group-block col-md-2">
            <a href="#WBSModal" data-toggle="modal" class="btn btn-default btn-block  tree-open">Select WBS-Level</a>
            <a href="#" class="remove-tree-input btn btn-warning" data-target="#WBSModal"
               data-label="Select WBS-Level"><span class="fa fa-times-circle"></span></a>

        </div>
        <div class="btn-group btn-group-sm  btn-group-block col-md-2">
            <input type="checkbox" name="checked" class="checkList"
                   value="Negative Variance">
            <p style="margin: 8px;font-size: larger">Negative Variance</p>
        </div>
        <div class="col-md-2">
            {{Form::select('cost_account', \App\Boq::where('project_id',$project->id)->get()->lists('cost_account','cost_account') , null,  ['placeholder'=>'Select Cost Account','class'=>'form-control cost_account'])}}
        </div>
    </div>

    <ul class="list-unstyled tree">
        @foreach($tree as $key=>$wbs_level)
            @include('reports.cost-control.boq-report._recursive_report', ['level'=>$wbs_level,'tree_level'=>0])
        @endforeach
    </ul>
    @include('wbs-level._modal')

@endsection
@section('javascript')
    <script>

        $(function () {
            var global_selector = '';

            $('.tree-radio').on('change', function () {
                if (this.checked) {
                    var value = $(this).attr('value');
                    global_selector = $('#col-' + value);
                    $('.level-container,.division-container').removeClass('in').addClass('hidden');
                    global_selector.parents('.level-container').addClass('in').removeClass('hidden');
                    global_selector.addClass('in').removeClass('hidden');
                    global_selector.parents('li').addClass('target').removeClass('hidden');
                    global_selector.children().children().children('article').addClass('in').removeClass('hidden');
//                    $('.level-container').not('.target').parent('li').addClass('hidden');
//                    $('ul.stdreport > li').not('.target').addClass('hidden');
                }
            });

            $('.remove-tree-input').on('click', function () {
                global_selector.parents('.division-container,.level-container').removeClass('in').removeClass('hidden');
                global_selector.removeClass('in').addClass('hidden');
                global_selector.parents('li').removeClass('target').addClass('hidden');
                global_selector.removeClass('target');
                $('li').not('target').removeClass('hidden');
                $('.level-container,.division-container').removeClass('in').removeClass('hidden');
                global_selector.children().children().children('article').removeClass('in').addClass('hidden');

            })

            $('.checkList').on('click', function () {
                var negative_rows = $('.negative-var');
                if ($(this).hasClass('clicked')) {
                    negative_rows.each(function () {
                        $(this).parents('.division-container,.level-container').removeClass('in').removeClass('hidden');
                        $(this).removeClass('in').removeClass('hidden');
                        $(this).parents('li').removeClass('target').removeClass('hidden');
                        $('ul.stdreport > li').not('.target').removeClass('hidden');
                    });
                    $(this).removeClass('clicked');
                }
                else {
                    negative_rows.each(function () {
                        $(this).parents('.division-container,.level-container').addClass('in').removeClass('hidden');
                        $(this).addClass('in').removeClass('hidden');
                        $(this).parents('li').addClass('target').removeClass('hidden');
                    });
                    $(this).addClass('clicked');
                }

            })

            $('.cost_account').on('change',function () {
                var value = $(this).val();
                var target_td = $("td[data-account='"+value+"']");
                target_td.parents('.division-container,.level-container').addClass('in').removeClass('hidden');
                target_td.addClass('in').removeClass('hidden');
                target_td.parents('li').addClass('target').removeClass('hidden');
                target_td.parent('tr').css('background-color','#FAFBD4  ');
            })

        })

    </script>
    <script src="{{asset('/js/tree-select.js')}}"></script>
@endsection