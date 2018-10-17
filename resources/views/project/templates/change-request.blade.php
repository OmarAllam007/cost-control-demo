<change-request :project="{{$project}}" inline-template>
    <section id="ChangeRequestSection">
        <div class="loader text-center" v-show="loading">
            <i class="fa fa-spinner fa-spin fa-3x"></i>
        </div>

        @can('budget',$project)
            <div class="form-group clearfix">
                <div class="pull-right">
                    <a href="{{route('project.change-request.create', $project)}}"
                       class="btn btn-primary btn-sm in-iframe"
                       title="Add Change Request">
                        <i class="fa fa-plus"></i> Add Change Request
                    </a>
                </div>
            </div>
        @endcan

        <table class="table table-striped table-condensed" v-if="requests.length">
            <thead>
            <tr>
                <th>ID</th>
                <th>Created By</th>
                <th>Assigned To</th>
                <th>Created At</th>
                <th>Status</th>
                <th>Closed At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="request in requests">
                {{--<td><a :href="revision.url" target="_blank" v-text="revision.name"></a></td>--}}
                <td v-text="request.id"></td>
                <td v-text="request.created_by"></td>
                <td v-text="request.assigned_to"></td>
                <td v-text="request.created_at"></td>
                <td>
                    <span v-if="!request.closed" class="label label-success">Open</span>
                    <span v-if="request.closed" class="label label-info">Closed</span>
                </td>
                <td v-text="request.closed_at"></td>
                <td>

                    <form :action="`/change-request/delete/${request.id}`" method="post">
                        <a :href="'/project/' +request.project_id+'/change-request/'+request.id" target="_blank"
                           class="btn btn-info">
                            <i class="fa fa-eye"></i>
                            Show
                        </a>
                        {{csrf_field()}} {{method_field('delete')}}
                        {{--                        @can('budget_owner', $project)--}}
                        <button class="btn btn-sm btn-danger"><i
                                    class="fa fa-trash"></i> Delete
                        </button>
                        {{--@endcan--}}
                    </form>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="alert alert-info" v-else>
            <i class="fa fa-info-circle"></i> No change requests found
        </div>
    </section>
</change-request>