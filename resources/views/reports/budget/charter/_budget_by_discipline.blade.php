<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">Project By Discipline</h4>
    </div>

    <section class="panel-body row">
        <div class="col-sm-8">
            <table class="table table-condensed table-striped table-hover">
                <thead>
                <tr class="info">
                    <th class="col-sm-7">Discipline</th>
                    <th class="col-sm-3">Budget Cost</th>
                    <th class="col-sm-2">Wt (%)</th>
                </tr>
                </thead>
                <tbody>
                @foreach($disciplines as $discipline)
                    <tr>
                        <td>{{$discipline->discipline}}</td>
                        <td>{{number_format($discipline->budget_cost, 2)}}</td>
                        <td>{{number_format($discipline->weight, 2)}}%</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr class="info">
                    <th>Grand Total</th>
                    <th>{{number_format($disciplines->sum('budget_cost'), 2)}}</th>
                    <th>100%</th>
                </tr>
                </tfoot>
            </table>
        </div>
        <div class="col-sm-4">
            <div id="disciplines-chart">

            </div>
        </div>
    </section>
</div>
