@extends('layouts.app')

@section('header')
    <div class="display-flex">
        <h2 class="flex">{{$project->name}} &mdash; Fix Qty Survey</h2>

        <a href="{{route('project.show', $project)}}" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back to project
        </a>
    </div>
@endsection

@section('body')

    @if ($failed)
        <div class="alert alert-warning">
            <p class="lead">Couldn't import some records. Please click below to download invalid items</p>
            <hr>
            <p><a href="{{url($failed)}}" class="btn btn-default"><i class="fa fa-cloud-download"></i> Download</a></p>
        </div>
    @endif

    <form action="" method="post">
        {{csrf_field()}}

        @foreach ($items as $boq_id => $surveys)
            @php
                $boq = $boqs->get($boq_id);
                $first = true;
                $rowspan = $surveys->count() + $surveys->pluck('wbs_level_id')->unique()->count();
            @endphp

            <article class="panel panel-primary">
                <header class="panel-heading">
                    <h4 class="panel-title">{{$boq->description}}</h4>
                </header>

                <table class="table table-bordered table-condensed">
                    <thead>
                    <tr>
                        <th>Description</th>
                        <th>Budget Qty</th>
                        <th>Eng Qty.</th>
                        <th>Unit</th>
                        <th>Boq Quantity</th>
                        <th>Boq Unit</th>
                        <th>Equivalent Budget Qty</th>
                        <th>Equivalent Eng Qty</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($surveys->groupBy('wbs_level_id') as $level => $group)
                        <tr>
                            <td class="info" colspan="6">
                                {{$group->first()->wbsLevel->path}}
                                <small>({{$group->first()->wbsLevel->code}})</small>
                            </td>
                            @if ($first)
                                <td class="col-sm-2 v-top" rowspan="{{$rowspan}}">
                                    <div class="input-group">
                                        <input type="text" class="form-control input-sm" name="budget_qty[{{$boq_id}}]" value="{{old("budget_qty.$boq_id", $surveys->flatten()->sum('budget_qty'))}}">
                                        <span class="input-group-btn">
                                            <btn type="btn" class="btn btn-primary btn-sm sum-budget-qty">&sum;</btn>
                                        </span>
                                    </div>
                                </td>

                                <td class="col-sm-2 v-top" rowspan="{{$rowspan}}">
                                    <div class="input-group">
                                        <input type="text" class="form-control input-sm" name="eng_qty[{{$boq_id}}]" value="{{old("eng_qty.$boq_id", $surveys->flatten()->sum('eng_qty'))}}">
                                        <span class="input-group-btn">
                                            <btn type="button" class="btn btn-primary btn-sm sum-eng-qty">&sum;</btn>
                                        </span>
                                    </div>
                                </td>

                                @php $first = false; @endphp
                            @endif
                        </tr>
                        @foreach($group as $survey)
                            <tr>
                                <td class="col-sm-3">{{$survey->description}}</td>
                                <td class="col-sm-1 budget-qty" data-value="{{$survey->budget_qty}}">{{number_format($survey->budget_qty, 2)}}</td>
                                <td class="col-sm-1 eng-qty" data-value="{{$survey->eng_qty}}">{{number_format($survey->eng_qty, 2)}}</td>
                                <td class="col-sm-1">{{$survey->unit->type}}</td>
                                <td class="col-sm-1">{{$boq->quantity}}</td>
                                <td class="col-sm-1">{{$boq->unit->type ?? ''}}</td>
                            </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
            </article>
        @endforeach

        <div class="form-group">
            <button class="btn btn-primary"><i class="fa fa-check"></i> Update</button>
        </div>
    </form>
@endsection

@section('javascript')
    <script>
        $(function() {
            $('.sum-budget-qty').click(function(e) {
                e.preventDefault();
                sumQty(this, 'budget');
            });

            $('.sum-eng-qty').click(function(e) {
                e.preventDefault();
                sumQty(this, 'eng');
            });

            function sumQty(element, type) {
                let qty = 0;
                $(element).parents('table').find(`.${type}-qty`).each(function(idx, item) {
                    qty += $(item).data('value');
                });

                $(element).closest('.input-group').find('input').val(qty.toFixed(2));
            }
        });

    </script>
@endsection