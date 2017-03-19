<li>
    <div class="tree--item">
        <label class="tree--item--label">
            <input type="radio" class="tree-radio wbs-radio" data-code="{{$level['code']}}" name="{{$input or 'parent_id'}}" value="{{$level['id']}}" @if(isset($value)) {{ $value == $level['id'] ?? 'checked' }} @endif >
            <a href="#wbs-children-{{$level['id']}}" class="node-label" data-toggle="collapse">{{$level['name']}}</a>
        </label>
    </div>
    @if ($level['children'] && count($level['children']))
        <ul class="list-unstyled collapse" id="wbs-children-{{$level['id']}}">
            @foreach($level['children'] as $level)
                @include('wbs-level._recursive_input', ['level' => $level, 'input' => isset($input)? $input : 'parent_id'])
            @endforeach
        </ul>
    @endif
</li>

