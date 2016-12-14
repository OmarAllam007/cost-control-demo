
<li>
    <div class="tree--item">
        <label class="tree--item--label">
            <a href="#children-{{$type['id']}}" class="node-label" data-toggle="collapse">{{$type['name']}}</a>
        </label>
    </div>

    <ul class=""></ul>

    <div class="collapse" id="children-{{$type['id']}}">
    @if (count($type['resources']))
            <ul class="list-unstyled">
            @foreach($type['resources'] as $resource)
                        <li class="radio">
                            <label>
                                <input type="radio" value="{{$resource['id']}}" name="resource_id"
                                       v-model="resource_id" @change="setResource({{json_encode($resource['json'])}}
                                )">
                                <span class="resource-name">{{$resource['name']}}</span>
                            </label>


                @endforeach
            </ul>
        @endif

        @if (count($type['children']))
            <ul class="list-unstyled">
                @foreach($type['children'] as $child)
                    @include('resources._recursive_resource_input', ['type' => $child, 'input' => 'resource_id', 'value' => $value])
                @endforeach
            </ul>
        @endif
    </div>
</li>