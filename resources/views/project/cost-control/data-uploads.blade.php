<section id="data-uploads" class="project-tab">
<table class="table table-striped table-hover table-condensed">
    <thead>
    <tr>
        <th>Uploaded By</th>
        <th>Uploaded At</th>
        <th>Uploaded File</th>
        <th>Actions</th>
    </tr>
    </thead>

    <tbody>
    @foreach($project->open_period()->batches as $batch)
        <tr>
            <td>{{$user = $batch->user->name}}</td>
            <td>{{$date = $batch->created_at->format('d/m/Y H:i')}}</td>
            <td><i class="fa fa-download"></i> <a href="{{'/actual-batches/' . $batch->id . '/download'}}">Download</a></td>
            <td>
                <a href="{{'/actual-batches/' . $batch->id}}" class="btn btn-info btn-sm in-iframe" title="Data upload by {{$user}} at {{$date}}"><i class="fa fa-eye"></i> Show</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</section>