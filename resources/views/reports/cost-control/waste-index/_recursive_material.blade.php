<li>
    <div class="tree--item">
        <label class="tree--item--label">
            <input type="checkbox" class="tree-radio" name="type[{{$type->id}}]" value="{{$type->id}}" {{request("type.{$type->id}")? 'checked' : ''}}>&nbsp;
            <a href="#children-{{$type->id}}" class="node-label" data-toggle="collapse">{{$type->name}}</a>
        </label>


    </div>

    @if ($type->subtree->count())
        <ul class="list-unstyled collapse" id="children-{{$type->id}}">
            @foreach($type->subtree as $subtype)
                @include('reports.cost-control.waste-index._recursive_material', ['type' => $subtype])
            @endforeach
        </ul>
    @endif
</li>

