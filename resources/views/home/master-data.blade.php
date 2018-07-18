@extends('layouts.app')

@section('header')
    <h2>Master Data</h2>
@endsection

@section('body')
    <div class="row">
        <div class="col-md-2 col-sm-3">
            <div class="br-1 pr-5">
                <ul class="list-unstyled masterdata-tree">
                    <li><a href="{{route('project.index')}}"><i class="fa fa-caret-right"></i> Projects</a></li>

                    @can('read', 'std-activity')
                        <li>
                            <a href="#std-divisions-menu" class="sub-menu-toggle" data-toggle="collapse"><i
                                        class="fa fa-caret-right"></i> <strong>Std Divisions</strong></a>
                            <ul class="list-unstyled collapse in menu" id="std-divisions-menu">
                                <li><a href="{{route('activity-division.index')}}">Divisions</a></li>
                                <li><a href="{{route('std-activity.index')}}">Standard Activities</a></li>
                                <li><a href="{{route('breakdown-template.index')}}">Breakdown Templates</a></li>
                            </ul>
                        </li>
                    @endcan

                    @can('read', 'resources')
                        <li>
                            <a href="#resources-menu" class="sub-menu-toggle" data-toggle="collapse"><i
                                        class="fa fa-caret-right"></i> <strong>Resources</strong></a>
                            <ul class="list-unstyled collapse in menu" id="resources-menu">
                                <li><a href="{{route('resource-type.index')}}">Resource Types</a></li>
                                <li><a href="{{route('resources.index')}}">Resources</a></li>
                                <li><a href="{{route('business-partner.index')}}">Business Partners</a></li>
                                <li><a href="{{route('unit.index')}}">Units of measure</a></li>
                            </ul>
                        </li>
                    @endcan

                    @can('read', 'productivity')
                        <li>
                            <a href="#productivity-menu" class="sub-menu-toggle" data-toggle="collapse"><i
                                        class="fa fa-caret-right"></i> <strong>Productivity</strong></a>
                            <ul class="list-unstyled collapse in menu" id="productivity-menu">
                                <li><a href="{{route('csi-category.index')}}">CSI Category</a></li>
                                <li><a href="{{route('productivity.index')}}">Productivity</a></li>
                            </ul>
                        </li>
                    @endcan

                    @if (Auth::user()->is_admin)
                        <li><a href="{{route('roles.index')}}"><i class="fa fa-caret-right"></i> Communication Plan</a>
                        </li>
                        <li><a href="{{route('users.index')}}"><i class="fa fa-caret-right"></i> Users</a></li>
                        <li><a href="{{route('global-periods.index')}}"><i class="fa fa-caret-right"></i> Periods</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="col-md-10 col-sm-9">
            @hasSection('content')
                @yield('content')
            @else
                <div class="display-flex-c">
                    <img src="{{asset('images/kps-logo.png')}}" width="70%" alt="">
                </div>
            @endif
        </div>
    </div>
@endsection