@php
    $data = collect(json_decode($issue->data, true) ?: []);
@endphp

@if ($data->count())
    <article class="panel panel-warning">
        <div class="panel-heading">
            <h4 class="panel-title">Activity Mapping &mdash;- No Permission</h4>
        </div>

        <table class="table table-condensed table-bordered">
            <thead>
            <tr>
                <th>Activity Code</th>
                <th>Date</th>
                <th>Resource</th>
                <th>U.O.M</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Cost</th>
                <th>Resource COde</th>
                <th>Doc #</th>
            </tr>
            </thead>
            @foreach($data as $row)
                <tr>
                    <td>{{$row[0]}}</td>
                    <td>{{$row[1]}}</td>
                    <td>{{$row[2]}}</td>
                    <td>{{$row[3]}}</td>
                    <td>{{$row[4]}}</td>
                    <td>{{$row[5]}}</td>
                    <td>{{$row[6]}}</td>
                    <td>{{$row[7]}}</td>
                    <td>{{$row[8]}}</td>
                </tr>
            @endforeach
        </table>
    </article>
@endif