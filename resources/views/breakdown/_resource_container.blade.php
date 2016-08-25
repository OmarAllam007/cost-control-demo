<div class="container-row">
    <table class="table" id="resourcesTable">
        <thead>
        <tr>
            <th>Resource Type</th>
            <th>Resource Name</th>
            <th>Budget Qty</th>
            <th>Eng Qty</th>
            <th>Resource Waste</th>
            <th>Labors Count</th>
            <th>Productivity Ref</th>
            <th>Remarks</th>
        </tr>
        </thead>
        <tbody>
        @if (!empty($include))
            @foreach(old('resources') as $index => $resource)
                @include('breakdown._resource_template', compact('index'))
            @endforeach
        @endif
        </tbody>
    </table>
</div>