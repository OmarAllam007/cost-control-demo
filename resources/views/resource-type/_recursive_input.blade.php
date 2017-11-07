<li>
    <div class="tree--item">
        <label class="tree--item--label">
            ß<input type="radio" class="tree-radio" name="{{isset($input)? $input : 'parent_id'}}" v-model="{{isset($input)? $input : 'parent_id'}}" value="{{$type->id}}" {{$value == $type->id? 'checked' : ''}}>
            <a href="#resource-type-children-{{$type->id}}" class="node-label" data-toggle="collapse">{{$type->name}}</a>
        </label>
    </div>
    @if (count($type->subtree))
        <ul class="list-unstyled collapse" id="resource-type-children-{{$type->id}}">
            @foreach($type->subtree as $subtype)
                @include('resource-type._recursive_input', ['type' => $subtype, 'input' => isset($input)? $input : 'parent_id', 'value' => $value])
            @endforeach
        </ul>
    @endif
</li>