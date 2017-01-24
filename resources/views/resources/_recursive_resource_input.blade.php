<li>
    <div class="tree--item">
        <label class="tree--item--label">
            <a href="#children-{{$type['id']}}" class="node-label" data-toggle="collapse">{{$type['name']}}</a>
        </label>
    </div>


    <div class="collapse" id="children-{{$type['id']}}">
        @if (count($type['resources']))
            <ul class="list-unstyled">


                @foreach($type['resources'] as $resource)
                    @if(!$resource['project_id'] || $resource['project_id'] ==
                    ($std_activity_resource->template->project_id ?? 0)
                    || $resource['project_id'] ==  ($breakdown_resource->breakdown->project_id ?? 0)
                    || $resource['project_id'] == ( request('template') ? \App\BreakdownTemplate::find(request('template'))->project_id : 0))
                        <li class="radio">
                            <label>
                                <input type="radio" value="{{$resource['id']}}" name="resource_id"
                                       v-model="resource_id" @change="setResource({{json_encode($resource)}})">
                                <span class="resource-name">{{$resource['name'] }} @if($resource['project_id']) ({{$resource['project_id']}}) @endif</span>
                            </label>
                        </li>
                    @endif


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
