<resources project="{{$project->id}}" inline-template>
    <table class="table table-striped table-condensed table-hover table-fixed">
        <thead>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Type</th>
            <th>Division</th>
            <th>Actions</th>
        </tr>
        </thead>

        <tbody>
        <tr v-for="resource in resources">
            <td>@{{ resource.code }}</td>
            <td>@{{ resource.name }}</td>
            <td>@{{ resource.type }}</td>
            <td>@{{ resource.division }}</td>
            <td>
                <a href="/cost-control/@{{ project }}/resource/@{{ resource.id }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-edit"></i> Edit
                </a>
            </td>
        </tr>
        </tbody>
    </table>
</resources>