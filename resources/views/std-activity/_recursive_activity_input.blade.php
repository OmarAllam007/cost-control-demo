<li>
    <div class="tree--item">
        <span class="tree--item--label">
            <a href="#division-children-{{$division->id}}" class="node-label" data-toggle="collapse">{{$division->label}}</a>
        </span>
    </div>

    <div class="collapse" id="division-children-{{$division->id}}">
        @if ($division->children->count())
            <ul class="list-unstyled">
                @foreach($division->children->load('children', 'activities') as $childDivision)
                    @include('std-activity._recursive_activity_input', ['division' => $childDivision, 'input' => $input])
                @endforeach
            </ul>
        @endif
        @if ($division->activities->count())
            <ul class="list-unstyled">
                @foreach($division->activities as $activity)
                    <li>
                        <label>
                            <input type="radio" class="tree-radio activity-input" name="{{$input or 'std_activity_id'}}" value="{{$activity->id}}" data-label="{{$activity->name}}" {{$value == $activity->id? 'checked="checked' : ''}} v-model="{{$input or 'activity_id'}}">
                            {{$activity->name}}
                        </label>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</li>