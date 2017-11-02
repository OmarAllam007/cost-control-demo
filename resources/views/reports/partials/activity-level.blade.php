<li>
    <div class="tree--item">
        <span class="tree--item--label">
            <input type="checkbox" name="division[{{$division->id}}]" value="{{$division->id}}" {{request("division.{$division->id}")? 'checked' : ''}}>
            <strong><a href="#" class="node-label open-level">{{$division->name}} <small>({{$division->code}})</small></a></strong>
        </span>
    </div>

    @if ($division->activities->count())
        <ul class="list-unstyled hidden">
            @foreach($division->activities as $activity)
                <li>
                    <div class="tree--item">
                        <span class="tree--item--label">
                            <input type="checkbox" name="activity[{{$activity->id}}]" value="{{$activity->id}}" {{request("activity.{$activity->id}")? 'checked' : ''}}>
                            {{$activity->name}} <small>({{$activity->code}})</small>
                        </span>
                </li>
            @endforeach
        </ul>
    @endif

    @if ($division->subtree->count())
        <ul class="list-unstyled hidden">
            @foreach($division->subtree as $subdivision)
                @include('reports.partials.activity-level', ['division' => $subdivision])
            @endforeach
        </ul>
    @endif
</li>