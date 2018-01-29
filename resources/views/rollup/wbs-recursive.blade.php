<li class="level-{{$depth}}">
    <div class="wbs-item">
        <a href="#children-{{$level->id}}" class="open-level">
            <i class="fa fa-plus-circle"></i> {{$level->name}}
            <small>({{$level->code}})</small>
        </a>


    </div>
    <ul id="children-{{$level->id}}" class="tree list-unstyled collapse">
        @foreach($level->children as $sublevel)
            @include('rollup.wbs-recursive', ['level' => $sublevel, 'depth' => $depth + 1])
        @endforeach
    </ul>
</li>