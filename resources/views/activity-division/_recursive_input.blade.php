<li>
    <div class="tree--item">
        <label class="tree--item--label">
            <input type="radio" class="tree-radio" name="parent_id" value="{{$division->id}}" {{Form::getValueAttribute('parent_id') == $division->id? 'checked' : ''}}>
            <a href="#children-{{$division->id}}" class="node-label" data-toggle="collapse">{{$division->label}}</a>
        </label>
    </div>
    @if ($division->children && $division->children->count())
        <ul class="list-unstyled collapse" id="children-{{$division->id}}">
            @foreach($division->children as $child)
                @include('activity-division._recursive_input', ['division' => $child])
            @endforeach
        </ul>
    @endif
</li>