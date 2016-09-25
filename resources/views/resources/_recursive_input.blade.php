<li>
    <div class="tree--item">
        <label class="tree--item--label">
            <input type="radio" class="tree-radio" name="{{$input or 'resource_type_id'}}" value="{{$level->id}}" {{$value == $level->id? 'checked' : ''}}>
            <a href="#children-{{$level->id}}" class="node-label" data-toggle="collapse">{{$level->name}}</a>
        </label>
    </div>
    @if ($level->children && $level->children->count())
        <ul class="list-unstyled collapse" id="children-{{$level->id}}">
            @foreach($level->children as $child)
                @include('resources._recursive_input', ['level' => $child, 'input' => isset($input)? $input : 'resource_type_id', 'value' => $value])
            @endforeach
        </ul>
    @endif
</li>

