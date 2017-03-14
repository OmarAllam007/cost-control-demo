<data-uploads project_id="{{$project->id}}" inline-template>
    <section id="data-uploads" class="project-tab">
        @if ($project->open_period())
            @verbatim
                <table class="table table-striped table-hover table-condensed" v-if="batches.length">
                    <thead>
                    <tr>
                        <th>Uploaded By</th>
                        <th>Uploaded At</th>
                        <th>Period</th>
                        <th>Uploaded File</th>
                        <th>Actions</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr v-for="batch in batches">
                        <td>{{batch.uploaded_by}}</td>
                        <td>{{batch.uploaded_at}}</td>
                        <td>{{batch.period_name}}</td>
                        <td><i class="fa fa-download"></i> <a :href="'/actual-batches/' + batch.id + '/download'">Download</a>
                        </td>
                        <td>
                            <a :href="'/actual-batches/' + batch.id" class="btn btn-info btn-sm in-iframe"
                               title="Data upload by {{batch.uploaded_by}} at {{batch.uploaded_at}}"><i
                                        class="fa fa-eye"></i> Show</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div v-else class="alert alert-info"><i class="fa fa-info-circle"></i> No data uploads found</div>
            @endverbatim
        @endif
    </section>
</data-uploads>