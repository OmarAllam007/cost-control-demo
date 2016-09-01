<li>
    <div class="tree--item">
        <span class="tree--item--label">
            <a href="#division-children-{{$division->id}}" class="node-label" data-toggle="collapse">{{$division->label}}</a>
        </span>
    </div>
    <div class="collapse" id="division-children-{{$division->id}}">
        @if ($division->children && $division->children->count())
            <ul class="list-unstyled">
                @foreach($division->children as $child)
                    @include('std-activity._recursive_activity_input', ['division' => $child])
                @endforeach
            </ul>
        @endif
        @if ($division->activities->count())
            <ul class="list-unstyled">
                @foreach($division->activities as $activity)
                    <li>
                        <label>
                            <input type="radio" class="tree-radio" name="std_activity_id" value="{{$activity->id}}" {{Form::getValueAttribute('std_activity_id') == $activity->id? 'checked' : ''}} data-label="{{$activity->name}}">
                            {{$activity->name}}
                        </label>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</li>