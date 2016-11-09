@extends('layouts.' . (request('iframe')? 'iframe' : 'app'))

@section('header')
    <h2>Fix import</h2>

    <a href="{{ route('project.show', $project) }}" class="btn btn-sm btn-warning pull-right"><i
                class="fa fa-remove"></i>
        Cancel</a>
@endsection

@section('body')
    {{Form::open(['route' => ['boq.post-fix-import', $key]])}}
    <div class="row">
        @if ($items->pluck('orig_unit_id')->count())
            <div class="col-sm-6">
                <h4>Units</h4>
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>Unit</th>
                        <th>Equivalent</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($items->pluck('orig_unit_id')->filter()->unique() as $unit)
                        <tr class="{{$errors->first("units.$unit", 'danger')}}">
                            <td class="col-sm-6">
                                {{$unit}}
                            </td>
                            <td class="col-sm-6">
                                {{Form::select("data[units][$unit]", App\Unit::options(), null, ['class' => 'form-control input-sm'])}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($items->pluck('orig_wbs_id')->count())
            <div class="col-sm-6">
                <h4>WBS</h4>
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>WBS</th>
                        <th>Equivalent</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($items->pluck('orig_wbs_id')->filter()->unique() as $level)
                        <tr class="{{$errors->first("wbs.$level", 'danger')}}">
                            <td>
                                {{$level}}
                            </td>
                            <td>
                                <a href="#" class="select-wbs">
                                    @if (Form::getValueAttribute("data[wbs][$level]"))
                                        {{App\WbsLevel::find(Form::getValueAttribute("data[wbs][$level]"))->code}}
                                    @else
                                        Select WBS
                                    @endif
                                </a>
                                {{Form::hidden("data[wbs][$level]")}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>

    <div class="form-group">
        <button class="btn btn-primary"><i class="fa fa-check"></i> Update</button>
    </div>
    {{Form::close()}}

    @include('wbs-level._modal', ['value' => '', 'project_id' => $project->id])
@endsection

@section('javascript')
    <script>
        (function (w, d, $) {
            $(function () {
                var target = null;
                var wbsModal = $('#WBSModal');

                $('.select-wbs').click(function (e) {
                    e.preventDefault();
                    target = $(this);
                    wbsModal.modal().find('.tree-radio').attr('checked', false);
                    wbsModal.find('.in').removeClass('in');
                });

                wbsModal.on('change', '.tree-radio', function () {
                    target.text($(this).data('code'));
                    target.parent().find('input').val($(this).val());
                });
            });
        }(window, document, jQuery));
    </script>
@endsection