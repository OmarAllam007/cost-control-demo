<section class="panel panel-primary">

    <div class="panel-heading">
        <h4 class="panel-title">Project By Resource Type</h4>
    </div>

    <div class="panel-body row">
    <div class="col-sm-8">
        <table class="table table-condensed table-striped">
            <thead>
            <tr class="bg-primary">
                <th class="col-sm-7">Resource Type</th>
                <th class="col-sm-3">Budget Cost</th>
                <th class="col-sm-2">Wt (%)</th>
            </tr>
            </thead>
            <tbody>
            @foreach($resource_types as $type)
                <tr>
                    <td>{{$type->type}}</td>
                    <td>{{number_format($type->budget_cost, 2)}}</td>
                    <td>{{number_format($type->weight, 2)}}%</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr class="bg-primary">
                <th>Grand Total</th>
                <th>{{number_format($resource_types->sum('budget_cost'), 2)}}</th>
                <th>100%</th>
            </tr>
            </tfoot>
        </table>
    </div>

    <div class="col-sm-4">
        <div id="types-chart"></div>
    </div>
    </div>
</section>