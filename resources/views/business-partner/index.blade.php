@extends('layouts.app')

@section('header')
    <h2>Business partner</h2>
    <a href="{{ route('business-partner.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add business_partner</a>
@stop

@section('body')
    @if ($businessPartners->total())
        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th>Name</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
                @foreach($businessPartners as $business_partner)
                    <tr>
                        <td class="col-md-5"><a href="{{ route('business-partner.edit', $business_partner) }}">{{ $business_partner->name }}</a></td>
                        <td class="col-md-3">
                            <form action="{{ route('business-partner.destroy', $business_partner) }}" method="post">
                                {{csrf_field()}} {{method_field('delete')}}
                                <a class="btn btn-sm btn-primary" href="{{ route('business-partner.edit', $business_partner) }} "><i class="fa fa-edit"></i> Edit</a>
                                <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $businessPartners->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No business partner found</strong></div>
    @endif
@stop
