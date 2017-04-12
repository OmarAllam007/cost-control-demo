<li>
    <div class="tree--item">
        <label class="tree--item--label">
            <input type="radio" class="tree-radio" name="{{isset($input)? $input : 'parent_id'}}" v-model="{{isset($input)? $input : 'parent_id'}}" value="{{$division['id']}}" {{$value == $division['id']? 'checked' : ''}}>
            <a href="#resource-type-children-{{$division['id']}}" class="node-label" data-toggle="collapse">{{$division['name']}}</a>
        </label>
    </div>
    @if (count($division['children']))
        <ul class="list-unstyled collapse" id="resource-type-children-{{$division['id']}}">
            @foreach($division['children'] as $division)
                @include('resource-type._recursive_input', ['division' => $division, 'input' => isset($input)? $input : 'parent_id', 'value' => $value])
            @endforeach
        </ul>
    @endif
</li>