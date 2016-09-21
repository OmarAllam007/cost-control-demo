{{-- Filter Form --}}
{{Form::open(['route' => 'business-partner.filter', 'class' => 'row filter-form'])}}
<div class="col-sm-3">
    <div class="form-group-sm">
        <label class="control-label" for="partnerName">Name</label>
        <input type="text" id="partnerName" name="name" class="form-control" value="{{session('filters.partner.name')}}">
    </div>
</div>

<div class="col-sm-3">
    <div class="form-group-sm">
        <label class="control-label" for="partnerType">Type</label>
        <input type="text" id="partnerType" name="type" class="form-control" value="{{session('filters.partner.type')}}">
    </div>
</div>

<div class="col-sm-3">
    <div class="form-group-sm">
        <button class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button>
    </div>
</div>

{{Form::close()}}
{{-- End filter form --}}