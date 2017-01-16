@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._qs_summery')
@endif
@section('header')
    <h2 class="">Quantity Survey Report</h2>
    <div class="pull-right">
        {{--<a href="?print=1&paint=std-activity" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i>--}}
        {{--Print</a>--}}
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
    </style>
@endsection
@section('body')
    <div id="input">

        {{--<div class="row">--}}
            {{--<div class="col-md-6">--}}
                {{--<div class="form-group">--}}
                    {{--<label for="codeSearch">Search By Code <i> ( Ex:ucaosew )</i></label>--}}
                    {{--<input type="text" class="form-control" id="codeSearch">--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}

    </div>
    <br>
    <ul class="list-unstyled tree">
        @foreach($tree as $parentKey=>$level)
            @include('reports.budget.qs_summery._recursive_report', ['level'=>$level,'tree_level'=>0])
        @endforeach
    </ul>
@endsection
@section('javascript')
    <script>
//        var array_ids = [];
//        $('#codeSearch').on('keydown',funct89+6+9+ion (event) {
//            var code = $(this).val().toLowerCase();
//            if (event.keyCode === 190) {
//                var fullString = code.split('.');
//                console.log(fullString);
//            }
//            if (event.keyCode === 8) {
//                for (var i = 0, len = array_ids.length; i < len; i++) {
//                    $('article[id=' + array_ids[i] + ']').addClass('collapse');
//                    $('#codeSearch').val('');
//                }
//                return false;
//            }
//
//            $article = $('article[data-code=' + code + ']');
//            if ($article.length) {
//                array_ids.push($article.attr('id'));
//                if ($article.hasClass('collapse')) {
//                    $article.removeClass('collapse');
//                    window.scrollTo(0, $article.offset().top);
//                }
//            }
//
//        });

    </script>
@endsection