@php
    $data = json_decode($issue->data, true);
@endphp

<article class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">Multiple Cost Account</h4>
    </div>

    <table class="table table-condensed table-bordered">
        <thead>
        <tr>

        </tr>
        </thead>
        <tbody>
        @foreach($data as $resource)
            <tr>

            </tr>
        @endforeach
        </tbody>
    </table>
</article>

