<li>
    <div class="tree--item">
        <label class="tree--item--label">
            <input type="radio" class="tree-radio" name="{{$input or 'csi_category_id'}}" value="{{$level->id}}" {{Form::getValueAttribute('csi_category_id')==$level->id ? 'checked' : ''}}>
            <a href="#children-{{$level->id}}" class="node-label" data-toggle="collapse">{{$level->name}}</a>
        </label>
    </div>
    @if ($level->children && $level->children->count())
        <ul class="list-unstyled collapse" id="children-{{$level->id}}">
            @foreach($level->children as $child)
                @include('csi-category._recursive_input', ['level' => $child, 'input' => isset($input)? $input : 'csi_category_id'])
            @endforeach
        </ul>
    @endif
</li>

