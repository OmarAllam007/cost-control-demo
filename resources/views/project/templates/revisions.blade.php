<revisions :project="{{$project}}" inline-template>
    <section id="RevisionsSection">
        <div class="loader" v-show="loading">
            <i class="fa fa-spinner fa-spin fa-3x"></i>
        </div>

        @can('owner', $project)
            <div class="form-group clearfix">
                <a href="{{route('revisions.create', $project)}}" class="btn btn-primary btn-sm in-iframe pull-right" title="Add Revision">
                    <i class="fa fa-plus"></i> Add Revision
                </a>
            </div>
        @endcan
        
        <table class="table table-striped table-condensed" v-if="revisions.length">
            <thead>
            <tr>
                <th>Name</th>
                <th>Created By</th>
                <th>Created Date</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="revision in revisions">
                <td><a :href="revision.url" target="_blank" v-text="revision.name"></a></td>
                <td v-text="revision.user"></td>
                <td v-text="revision.created_date"></td>
                <td>
                    <a :href="revision.url" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> Show</a>
                    <a :href="`${revision.url}/export`" class="btn btn-sm btn-success"><i class="fa fa-cloud-download"></i> Export</a>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="alert alert-info" v-else>
            <i class="fa fa-info-circle"></i> No revisions found
        </div>
    </section>
</revisions>



