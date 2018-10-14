<change-request :project="{{$project}}" inline-template>
    <section id="ChangeRequestSection">
        <div class="loader" v-show="loading">
            <i class="fa fa-spinner fa-spin fa-3x"></i>
        </div>

        <div class="form-group clearfix">
            <div class="pull-right">
                <a href="{{route('project.change-request.create', $project)}}" class="btn btn-primary btn-sm in-iframe"
                   title="Add Change Request">
                    <i class="fa fa-plus"></i> Add Change Request
                </a>
            </div>
        </div>

        <table class="table table-striped table-condensed" v-if="requests.length">
            <thead>
            <tr>
                <th>ID</th>
                <th>Created By</th>
                <th>Assigned To</th>
                <th>Created At</th>
                <th>Closed At</th>
            </tr>
            </thead>
            <tbody>
            {{--<tr v-for="request in requests">--}}
                {{--<td><a :href="revision.url" target="_blank" v-text="revision.name"></a></td>--}}
                {{--<td v-text="revision.user"></td>--}}
                {{--<td v-text="revision.created_date"></td>--}}
                {{--<td>--}}
                    {{--<form :action="`${revision.url}/delete`" method="post">--}}
                        {{--<a :href="`${revision.url}/edit`" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>--}}
                        {{--<a :href="revision.url" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-eye"></i>--}}
                            {{--Show</a>--}}
                        {{--<a :href="`${revision.url}/export`" class="btn btn-sm btn-success"><i--}}
                                    {{--class="fa fa-cloud-download"></i> Export</a>--}}
                        {{--@can('budget_owner', $project)--}}
                            {{--{{csrf_field()}} {{method_field('delete')}}--}}
                            {{--<button v-if="!revision.is_automatic" class="btn btn-sm btn-danger"><i--}}
                                        {{--class="fa fa-trash"></i> Delete--}}
                            {{--</button>--}}
                        {{--@endcan--}}
                    {{--</form>--}}
                {{--</td>--}}
            {{--</tr>--}}
            </tbody>
        </table>
        <div class="alert alert-info" v-else>
            <i class="fa fa-info-circle"></i> No change requests found
        </div>
    </section>
</change-request>