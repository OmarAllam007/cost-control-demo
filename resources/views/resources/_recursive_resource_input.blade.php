<li>
    <div class="tree--item">
        <label class="tree--item--label">
            <a href="#children-{{$type->id}}" class="node-label" data-toggle="collapse">{{$type->name}}</a>
        </label>
    </div>


    <div class="collapse" id="children-{{$type['id']}}">
        @if (count($type->db_resources))
            <ul class="list-unstyled">


                @foreach($type->db_resources as $resource)
                        <li class="radio">
                            <label>
                                <input type="radio" value="{{$resource['id']}}" name="resource_id" v-model="resource_id" @change="setResource({{json_encode($resource)}})">
                                <span class="resource-name">{{$resource['name'] }}</span>
                            </label>
                        </li>
                @endforeach
            </ul>
        @endif

        @if (count($type->subtree))
            <ul class="list-unstyled">
                @foreach($type->subtree as $child)
                    @include('resources._recursive_resource_input', ['type' => $child, 'input' => 'resource_id', 'value' => $value])
                @endforeach
            </ul>
        @endif
    </div>
</li>
