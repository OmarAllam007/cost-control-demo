@php
    $data = collect(json_decode($issue->data, true) ?: []);
@endphp

@if ($data->count())
    <article class="panel panel-warning">
        <div class="panel-heading">
            <h4 class="panel-title">Activity Mapping &mdash; With Permission</h4>
        </div>


    </article>
@endif