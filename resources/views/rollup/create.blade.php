@extends('layouts.app')

@section('title', 'Resources Rollup')

@section('header')
    <div class="display-flex">
        <h2 class="flex">
            {{$project->name}} &mdash; Resource Rollup
        </h2>
    </div>
@endsection

@section('body')
    <form action="" method="post">
        <ul class="wbs-tree list-unstyled" id="wbs-tree">
            @foreach($wbsTree as $level)
                @include('rollup.wbs-recursive', ['level' => $level, 'depth' => 0])
            @endforeach
        </ul>
    </form>
@endsection

@section('javascript')
    <script>
        jQuery(function($) {
            $('#wbs-tree').on('click', '.open-level', function(e) {
                e.preventDefault();

                const target = $(this).attr( 'href');
                $(target).toggleClass('in');
            });
        });
    </script>
@endsection