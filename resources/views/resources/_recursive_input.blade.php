<li>
    <div class="tree--item">
        <label class="tree--item--label">
            <input type="radio" class="tree-radio" name="{{$input or 'resource_type_id'}}" value="{{$type->id}}" {{$value == $type->id? 'checked' : ''}}>
            &nbsp;
            <a href="#children-{{$type->id}}" class="node-label" data-toggle="collapse">{{$type->name}}</a>
        </label>
    </div>

    @if (count($type->subtree))
        <ul class="list-unstyled collapse" id="children-{{$type->id}}">
            @foreach($type->subtree as $child)
                @include('resources._recursive_input', ['type' => $child, 'input' => isset($input)? $input : 'resource_type_id', 'value' => $value])
            @endforeach
        </ul>
    @endif
</li>

