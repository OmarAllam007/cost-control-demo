@extends('layouts.app')

@section('header')
    <h2>Business partner</h2>
    <div class="btn-toolbar pull-right">
        <a href="{{ route('business-partner.create') }} " class="btn btn-sm btn-primary pull-right"><i
                    class="fa fa-plus"></i> Add Business Partner</a>
        @can('wipe')
            <a href="#WipeAlert" data-toggle="modal" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Delete
                All</a>
        @endcan
    </div>
@stop

@section('body')
    @include('business-partner._filters')
    @if ($businessPartners->total())
        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($businessPartners as $business_partner)
                <tr>
                    <td class="col-md-5"><a
                                href="{{ route('business-partner.edit', $business_partner) }}">{{ $business_partner->name }}</a>
                    </td>
                    <td class="col-md-5">{{ $business_partner->type }}</td>
                    <td class="col-md-3">
                        <form action="{{ route('business-partner.destroy', $business_partner) }}" method="post">
                            {{csrf_field()}} {{method_field('delete')}}
                            <a class="btn btn-sm btn-primary"
                               href="{{ route('business-partner.edit', $business_partner) }} "><i
                                        class="fa fa-edit"></i> Edit</a>
                            <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $businessPartners->links() }}

        <div class="modal fade" tabindex="-1" role="dialog" id="WipeAlert">
            <form class="modal-dialog" action="{{route('partner.wipe')}}" method="post">
                {{csrf_field()}}
                {{method_field('delete')}}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Delete All Standard Partners</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i>
                            Are you sure you want to delete all Partners ?
                            <input type="hidden" name="wipe" value="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Delete All</button>
                        <button type="button" class="btn btn-default"><i class="fa fa-close"></i> Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No business partner
                found</strong></div>
    @endif
@stop
