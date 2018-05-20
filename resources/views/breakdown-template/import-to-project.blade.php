@extends('layouts.' . (request('iframe') ? 'iframe' : 'app'))

@section('header')
    <div class="display-flex">
        <h2 class="flex">{{$project->name}} &mdash; Import Breakdown templates</h2>
        <a href="{{route('project.budget', $project)}}" class="btn btn-sm btn-default">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@stop

@section('body')
    <form action="" method="post">
        {{csrf_field()}}

        <div class="form-group">
            <button class="btn btn-primary" type="submit">
                <i class="fa fa-check"></i> Import
            </button>
        </div>

        <div id="breakdownTemplates">
            @php $divisions = app(App\Support\ActivityDivisionTree::class)->get(); @endphp
            <breakdown-templates
                    :divisions="{{$divisions}}" :reject="{{$project->id}}"
                    :enable-select="true">
            </breakdown-templates>
        </div>

        <div class="form-group">
            <button class="btn btn-primary" type="submit">
                <i class="fa fa-check"></i> Import
            </button>
        </div>
    </form>
@stop

@section('javascript')
    <script src="{{asset('js/breakdown-templates.js')}}"></script>
@endsection