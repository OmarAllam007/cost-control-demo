@extends(request()->exists('iframe') ? 'layouts.iframe' : 'home.master-data')

@section('header')
    <h2>
        @if ($productivity->project)
            {{$productivity->name}} &mdash;
        @endif

        Modify Productivity
    </h2>
@stop

@section(request()->exists('iframe') ? 'body' : 'content')
    {{ Form::model($productivity, ['url' => route('productivity.update', $productivity) . (request()->exists('iframe')? '?iframe' : '')]) }}

        {{ method_field('patch') }}

        @include('productivity._form', ['override' => !empty($productivity->project_id)])

    {{ Form::close() }}
@stop

@section('javascript')
    {{--@if (request()->exists('iframe'))--}}
    <script>
        $(function() {
            $('form').on('submit', function() {
                const btn = $(this).find('.btn');
                btn.prop('disabled', true);
                btn.find('i').toggleClass('fa-check fa-spinner fa-spin');
            });
        });
    </script>
    {{--@endif--}}
@append
