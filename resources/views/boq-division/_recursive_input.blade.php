<li>
    <div class="tree--item">
        <label class="tree--item--label">
            <input type="radio" class="tree-radio" name="{{$input or 'division_id'}}" value="{{$division->id}}" {{Form::getValueAttribute(isset($input)? $input : 'division_id') == $division->id? 'checked' : ''}}>
            <a href="#children-{{$division->id}}" class="node-label" data-toggle="collapse">{{$division->name}}</a>
        </label>
    </div>
    @if ($division->children && $division->children->count())
        <ul class="list-unstyled collapse" id="children-{{$division->id}}">
            @foreach($division->children as $child)
                @include('boq-division._recursive_input', ['division' => $child, 'input' => isset($input)? $input : 'division_id'])
            @endforeach
        </ul>
    @endif
</li>

