<section class="col-sm-8 col-sm-offset-2">
    <h4 class="col-sm-12 page-header">Project By Discipline</h4>

    <div class="col-sm-8">
        <table class="table table-condensed table-striped table-hover">
            <thead>
            <tr>
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
        </table>
    </div>
    <div class="col-sm-4">
        <div id="disciplines-chart">

        </div>
    </div>
</section>