@php
    $data = json_decode($issue->data, true);
@endphp

<article class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">Closed Resources</h4>
    </div>

    @if (count($data['reopened']))
        <table class="table table-bordered table-hover table-condensed table-striped">
            <thead>
            <tr>
                <th colspan="4">Reopened</th>
            </tr>
            <tr>
                <td>Activity</td>
                <td>Count Account</td>
                <td>Resource</td>
                <td>Progress</td>
            </tr>
            </thead>
            <tbody>
            @foreach($data['reopened'] as $row)
                @php
                    $shadow = \App\BreakDownResourceShadow::find($row['resource']['id']);
                @endphp

                <tr>
                    <td>{{$shadow->wbs->path}} / {{$shadow->activity}}</td>
                    <td>{{$shadow->cost_account}}</td>
                    <td>{{$shadow->resource_name}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    @if (count($data['ignored']))
        <table class="table table-bordered table-hover table-condensed table-striped">
            <thead>
            <tr>
                <th colspan="4">Ignored</th>
            </tr>
            <tr>
                <td>Activity</td>
                <td>Count Account</td>
                <td>Resource</td>
            </tr>
            </thead>
            <tbody>
            @foreach($data['ignored'] as $row)
                @php
                $shadow = \App\BreakDownResourceShadow::find($row['resource']['id']);
                @endphp

                <tr>
                    <td>{{$shadow->wbs->path}} / {{$shadow->activity}}</td>
                    <td>{{$shadow->cost_account}}</td>
                    <td>{{$shadow->resource_name}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</article>