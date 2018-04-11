@extends('layouts.app')

@section('header')
    <h2>Breakdown templates</h2>
    @can('write', 'breakdown-template')
        <div class="pull-right">
            {{csrf_field()}}
            {{method_field('delete')}}
            <a href="{{ route('breakdown-template.create') }} " class="btn btn-sm btn-primary">
                <i class="fa fa-plus"></i> Add template
            </a>

            <div class="dropdown" style="display: inline-block;">
                <a href="#" data-toggle="dropdown" class="btn btn-info btn-sm dropdown-toggle">Import / Export <span
                            class="caret"></span></a>

                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="{{route('breakdown-template.export')}}"><i class="fa fa-cloud-download"></i> Export</a>
                    </li>
                    <li class="divider"></li>
                    <li><a href="{{route('breakdown-template.import')}}"><i class="fa fa-cloud-upload"></i> Import</a>
                    </li>
                    <li><a href="{{route('breakdown-template.modify')}}"><i class="fa fa-pencil"></i> Modify</a></li>
                </ul>
            </div>
        </div>
    @endcan
@stop

@section('body')

    <div class="row" id="breakdownTemplates">
        <div class="col-sm-4 col-md-3 br-1">
            <divisions :divisions="{{$divisions}}"></divisions>
        </div>

        <div class="col-sm-8 col-md-9">
            <templates :url="url"
                       :can_edit="{{can('write', 'breakdown-template')}}"
            ></templates>
        </div>
    </div>

@stop

@section('javascript')
    <script src="{{asset('js/breakdown-templates.js')}}"></script>
@endsection