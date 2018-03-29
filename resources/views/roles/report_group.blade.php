<article class="col-sm-6">
    <div class="panel panel-default  report-panel">

        <div class="panel-heading display-flex">
            <h3 class="flex panel-title">{{$key}}</h3>
            <span>
                <a href="#" class="select-all"><i class="fa fa-check-square-o"></i> Select All</a> |
                <a href="#" class="remove-all"><i class="fa fa-square-o"></i> Remove all</a>
            </span>
        </div>

        <div class="panel-body">
            @foreach($group as $report)
                <article class="checkbox">
                    <label>
                        <input type="checkbox" name="reports[{{$report->id}}]" id="report_{{$report->id}}"
                               value="{{$report->id}}" {{old("reports.$report->id", $role->hasReport($report->id))? 'checked' : ''}}>

                        {{$report->name}}
                    </label>
                </article>
            @endforeach
        </div>
    </div>
</article>