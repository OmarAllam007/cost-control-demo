<li>
    <div class="tree--item">
        <a href="#children-{{$wbs_level->id}}" class="tree--item--label" data-toggle="collapse"><i class="fa fa-chevron-circle-right"></i> {{$wbs_level->name}} <small class="text-muted">({{$wbs_level->code}})</small></a>
        <span class="tree--item--actions">
            {{--<a href="{{route('wbs-level.show', $wbs_level)}}" class="label label-info"><i class="fa fa-eye"></i> Show</a>--}}
        </span>
    </div>
    @if ($wbs_level->children && $wbs_level->children->count())
        <ul class="list-unstyled collapse" id="children-{{$wbs_level->id}}">
            @foreach($wbs_level->children as $child)
                @include('wbs-level._recursive_report', ['wbs_level' => $child])
            @endforeach
        </ul>
    @endif
</li>