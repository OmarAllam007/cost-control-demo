<form action="" class="row">
    <div class="form-group col-sm-4">
        <label for="selectPeriod" class="sr-only"></label>
        <div class="input-group">

            <select name="period" id="selectPeriod" class="form-control">
                <option value="">-- Select Reporting Period --</option>
                @foreach($globalPeriods as $p)
                    <option value="{{$p->id}}" {{$p->id == $reportPeriod->id? 'selected' : ''}}>{{$p->name}}</option>
                @endforeach
            </select>
            <span class="input-group-btn">
                <button class="btn btn-primary"><i class="fa fa-check"></i> Update</button>
            </span>
        </div>
    </div>
    <div class="form-group col-sm-2">

    </div>
</form>