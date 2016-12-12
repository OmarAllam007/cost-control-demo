@extends('layouts.' . (request('iframe')? 'iframe' : 'app'))

@section('header')

    <a href="{{ route('project.show', $project) }}" class="btn btn-sm btn-warning pull-right"><i class="fa fa-remove"></i>
        Cancel</a>
@endsection

@section('body')
    <h4>Fix Duplicated Cost Accounts</h4>

    {{Form::open(['route' => ['survey.post-dublicate', $key]])}}
    <div class="row">

        <div class="col-sm-12">
            <br>
            <table class="table table-striped table-condensed table-hover">
                <thead>
                <tr>
                    <th>Cost Account</th>
                    <th>WBS-LEVEL</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($dublicated_items as $key=>$level)
                    @foreach($level['wbs'] as $lKey=>$item)
                        <tr class="{{$errors->first("wbs.$item", 'danger')}}">
                            <td>
                                {{$item}} - Found In ( {{\App\WbsLevel::where('code',$lKey)->first()->path}} )
                            </td>
                            <td>
                                <a href="#" class="select-wbs">
                                    @if (Form::getValueAttribute("data[wbs][$item]"))
                                        {{App\WbsLevel::find(Form::getValueAttribute("data[wbs][$item]"))->code}}
                                    @else
                                        Select WBS
                                    @endif
                                </a>
                                {{Form::hidden("data[wbs][$item]")}}
                            </td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
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
                    console.log($(this).data('code'));
                    target.text($(this).data('code'));
                    target.parent().find('input').val($(this).val());
                });
            });
        }(window, document, jQuery));
    </script>
@endsection