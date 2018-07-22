@extends('home.master-data')

@section('header')
    <div class="display-flex">
        <h2 class="flex">Global Financial Periods</h2>
        <a href="{{route('global-periods.create')}}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add Period</a>
    </div>
@endsection

@section('content')

    @if ($globalPeriods->total())
    <table class="table table-condensed table-striped">
        <thead>
        <tr>
            <th>Name</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($globalPeriods as $period)
            <tr>
            <td>{{$period->name}}</td>
            <td>{{$period->start_date->format('d M Y')}}</td>
            <td>{{$period->end_date->format('d M Y')}}</td>
            <td>
                <form action="{{route('global-periods.destroy', $period)}}" method="post">
                    {{csrf_field()}}
                    {{method_field('delete')}}

                    <a href="{{route('global-periods.edit', $period)}}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                    @if (!$period->hasProjectPeriods())
                        <button class="btn btn-danger btn-sm btn-delete"><i class="fa fa-trash"></i> Delete</button>
                    @endif
                </form>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>

        {{$globalPeriods->links()}}
    @else
        <div class="alert alert-info"><i class="fa fa-info-circle"></i> No periods found</div>
    @endif
@endsection

@section('javascript')
    <script>
        $(function() {
            $('form').on('submit', function() {
                return confirm('Are you sure you want to delete this period. It will not be deleted if used in projects.');
            });
        })
    </script>
@endsection