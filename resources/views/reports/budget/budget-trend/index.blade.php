@extends('layouts.app')

@section('header')
    <h2>{{$project->name}} &mdash; Budget Trend</h2>
@endsection

@section('body')
    @if ($data->count())
        @php
            $rev_id = 2;
            $totals = $data->flatten()->groupBy('revision_id')->map(function ($group) {
                return $group->sum('cost');
            });

            $firstRev = $revisions->keys()->min();
            $lastRev = $revisions->keys()->max();
        @endphp
        <div class="horizontal-scroll">
            <table class="table table-condensed table-striped table-bordered">
                <thead>
                <tr>
                    <th width="300">Description</th>
                    @foreach ($revisions as $rev)
                        <th width="150">{{$rev}}</th>
                    @endforeach
                    <th width="150">Difference</th>
                    <th width="150">% Difference</th>
                </tr>
                </thead>
            </table>

            <div class="vertical-scroll">
                <table class="table table-hover table-condensed table-striped table-bordered" id="ContentsTable">
                    <tbody>
                    @foreach($data as $discipline => $disciplineData)
                        <tr>
                            <td class="discipline" width="300"><a href=".{{slug($discipline)}}"><i
                                            class="fa fa-plus-circle"></i> {{$discipline}}</a></td>
                            @php $diff = 0; $diffPercent = 0; $firstTotal = 0; @endphp
                            @foreach($revisions as $rev_id => $rev_name)
                                @php
                                    $total = $disciplineData->map(function($activityData)use ($rev_id) {
                                        return $activityData->flatMap(function($resources) use ($rev_id){
                                            return $resources->where('revision_id', $rev_id);
                                        })->sum('cost');
                                    })->sum();

                                    if ($rev_id == $firstRev) {
                                        $firstTotal = $total;
                                    }

                                    if ($rev_id == $lastRev) {
                                        $lastTotal = $total;
                                        $diff = $lastTotal - $firstTotal;
                                        $diffPercent = $diff * 100 / $firstTotal;
                                    }
                                @endphp
                                <td width="150">{{number_format($total, 2)}}</td>
                            @endforeach
                            <td width="150" class="{{$diff > 0? 'text-danger' : ($diff < 0? 'text-success' : '')}}">{{number_format($diff, 2)}}</td>
                            <td width="150" class="{{$diff > 0? 'text-danger' : ($diff < 0? 'text-success' : '')}}">{{number_format($diffPercent, 2)}}%</td>
                        </tr>
                        @foreach ($disciplineData as $activity => $activityData)
                            <tr class="{{slug($discipline)}} hidden">
                                <td class="activity" width="300"><a href=".{{slug($activity)}}"><i
                                                class="fa fa-plus-circle"></i> {{$activity}}</a></td>
                                @php $diff = 0; $diffPercent = 0; $firstTotal = 0; @endphp
                                @foreach($revisions as $rev_id => $rev_name)
                                    @php
                                        $total = $activityData->flatMap(function($resources) use ($rev_id){
                                            return $resources->where('revision_id', $rev_id);
                                        })->sum('cost');

                                        if ($rev_id == $firstRev) {
                                            $firstTotal = $total;
                                        }

                                        if ($rev_id == $lastRev) {
                                            $lastTotal = $total;
                                            $diff = $lastTotal - $firstTotal;
                                            if ($firstTotal != 0) {
                                                $diffPercent = $diff * 100 / $firstTotal;
                                            } else {
                                                $diffPercent = 100;
                                            }
                                        }
                                    @endphp
                                    <td width="150">{{number_format($total, 2)}}</td>
                                @endforeach
                                <td width="150" class="{{$diff > 0? 'text-danger' : ($diff < 0? 'text-success' : '')}}">{{number_format($diff, 2)}}</td>
                                <td width="150" class="{{$diff > 0? 'text-danger' : ($diff < 0? 'text-success' : '')}}">{{number_format($diffPercent, 2)}}</td>
                            </tr>
                            @foreach ($activityData as $resource => $resourceData)
                                <tr class="{{slug($activity)}} hidden">
                                    <td class="resource" width="300">{{$resource}}</td>
                                    @php $firstTotal = 0; $lastTotal = 0; $diff = 0; $diffPercent = 0; @endphp
                                    @foreach($revisions as $rev_id => $rev_name)
                                        @php
                                            if ($rev_id == $firstRev) {
                                                $firstTotal = $resourceData[$rev_id]['cost'];
                                            }

                                            if ($rev_id == $lastRev) {
                                                $lastTotal = $resourceData[$rev_id]['cost'];
                                                $diff = $lastTotal - $firstTotal;
                                                if ($firstTotal != 0) {
                                                    $diffPercent = $diff * 100 / $firstTotal;
                                                } else {
                                                    $diffPercent = 100;
                                                }
                                            }
                                        @endphp
                                        <td width="150">{{number_format($resourceData[$rev_id]['cost'] ?? 0, 2)}}</td>
                                    @endforeach
                                    <td width="150" class="{{$diff > 0? 'text-danger' : ($diff < 0? 'text-success' : '')}}">{{number_format($diff, 2)}}</td>
                                    <td width="150" class="{{$diff > 0? 'text-danger' : ($diff < 0? 'text-success' : '')}}">{{number_format($diffPercent, 2)}}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                    </tbody>

                    <tfoot>
                    <tr>
                        <th>Total</th>
                        @foreach($revisions as $rev_id => $rev_name)
                            <th>{{number_format($totals[$rev_id], 2)}}</th>
                        @endforeach
                        @php
                            $diff = $totals[$lastRev] - $totals[$firstRev];
                            if ($totals[$firstRev] != 0) {
                                $diffPercent = $diff * 100 / $totals[$firstRev];
                            } else {
                                $diffPercent = 100;
                            }
                        @endphp
                        <th width="150" class="{{$diff > 0? 'text-danger' : ($diff < 0? 'text-success' : '')}}">{{number_format($diff, 2)}}</th>
                        <th width="150" class="{{$diff > 0? 'text-danger' : ($diff < 0? 'text-success' : '')}}">{{number_format($diffPercent, 2)}}</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    @else
        <div class="alert alert-info"><i class="fa fa-info circle"></i> No revision data found</div>
    @endif
@endsection

@section('javascript')
    <script>
        $(function () {
            function closeActivity(item) {
                const link = $(item).find('a');
                const targets = $(link.attr('href')).addClass('hidden');
                $(this).find('i').removeClass('fa-minus-circle').addClass('fa-plus-circle');
            }

            $('.discipline a').click(function (e) {
                const targets = $($(this).attr('href')).toggleClass('hidden');
                if (targets.hasClass('hidden')) {
                    targets.each((index, item) => closeActivity(item));
                }

                $(this).find('i').toggleClass('fa-plus-circle fa-minus-circle');
                return false;
            });

            $('.activity a').click(function (e) {
                e.preventDefault();
                const targets = $($(this).attr('href')).toggleClass('hidden');
                $(this).find('i').toggleClass('fa-plus-circle fa-minus-circle');
                return false;
            });

            const contentsTable = $('#ContentsTable').on('click', 'tr', function() {
                contentsTable.find('tr').removeClass('info');
                $(this).addClass('info');
            });
        });
    </script>
@endsection

@section('css')
    <style>
        .horizontal-scroll {
            overflow-x: auto;
        }

        .vertical-scroll {
            overflow-y: auto;
            max-height: 500px;
        }

        .horizontal-scroll table.table {
            width: auto;
            max-width: none;
            min-width: 100%;
            margin-bottom: 0;
        }

        .discipline {
            font-weight: bold;
        }

        .table-condensed > tbody > tr > td.activity {
            padding-left: 20px;
        }

        .table-condensed > tbody > tr > td.resource {
            padding-left: 40px;
        }
    </style>
@endsection