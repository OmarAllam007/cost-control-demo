<form action="" class="card">
    <div class="row">
        <div class="form-group form-group-sm col-sm-3">
            <label for="selectPeriod">Select Period</label>
            <select type="text" name="period" id="seletPeriod" class="form-control">
                <option value="">[Select Period]</option>
                @php $period_id = request('period', $period->id) @endphp
                @foreach ($periods as $id => $name)
                    <option value="{{ $id }}" {{ $id == $period_id? 'selected' : ''}}>{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group form-group-sm col-sm-2">
            <label for="thresholdPercent">Threshold Percentage</label>
            <div class="input-group">
                <input type="number" name="threshold" id="thresholdPercent" class="form-control" value="{{ $threshold }}" step="0.1">
                <span class="input-group-addon">%</span>
            </div>
        </div>

        <div class="col-sm-3">
                @include('reports.partials.activity-filter')
        </div>

        <div class="form-group form-group-sm col-sm-2">
            <label for="thresholdValue">Threshold Value</label>
            <input type="number" name="threshold_value" id="thresholdValue" class="form-control" value="{{ $threshold_value }}">
        </div>

        <div class="form-group filter-btn text-right col-sm-2">
            <button class="btn btn-sm btn-primary"><i class="fa fa-filter"></i> Filter</button>
        </div>

    </div>
</form>