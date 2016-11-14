<li>
    <div class="wbs-item">
    @if (count($item['children']))
    <a href="#wbsChildren{{$item['id']}}" class="wbs-icon" data-toggle="collapse"><i class="fa fa-plus-square-o toggle-icon"></i></a>
        @else
        <span class="wbs-icon"><i class="fa fa-angle-right wbs-icon"></i></span>
    @endif

    <a href="#" class="wbs-link" @click="selected = {{$item['id']}}" title="{{$item['name']}}">{{$item['code']}}</a>
    </div>

    @if (count($item['children']))
        <ul class="collapse" id="wbsChildren{{$item['id']}}">
            @foreach($item['children'] as $child)
                @include('project.wbs-item', ['item' => $child])
            @endforeach
        </ul>
    @endif
</li>