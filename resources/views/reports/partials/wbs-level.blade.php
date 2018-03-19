<li>
    <div class="tree--item">
        <span class="tree--item--label">
            <input type="checkbox" name="wbs[{{$level->id}}]" value="{{$level->id}}" {{request("wbs.{$level->id}")? 'checked' : ''}}>
            <strong><a href="#" class="node-label open-level">{{$level->name}} <small>({{$level->code}})</small></a></strong>
        </span>
    </div>

    @if ($level->subtree->count())
        <ul class="list-unstyled hidden">
            @foreach($level->subtree as $sublevel)
                @include('reports.partials.wbs-level', ['level' => $sublevel])
            @endforeach
        </ul>
    @endif
</li>