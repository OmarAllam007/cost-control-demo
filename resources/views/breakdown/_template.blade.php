<template id="resourcesEmptyAlert"><div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Please select breakdown template</div></template>
<template id="resourcesLoading"><div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Loading...</div></template>
<template id="resourcesError"><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Error loading breakdown resources</div></template>
<template id="containerTemplate">
    <div class="container-row">
        <table class="table" id="resourcesTable">
            <thead>
            <tr>
                <th>Resource Type</th>
                <th>Resource Name</th>
                <th>Budget Qty</th>
                <th>Eng Qty</th>
                <th>Resource Waste</th>
                <th>Labors Count</th>
                <th>Productivity Ref</th>
                <th>Remarks</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</template>

<template id="resourceRowTemplate">
    <tr>
        <td>
            <input class="form-control input-sm" type="hidden" name="resources[##][std_activity_resource_id]" id="resourceId##" j-model="std_activity_resource_id" readonly>
            <input class="form-control input-sm" type="text" name="resources[##][resource_type]" id="resourceType##" j-model="resource_type" readonly>
        </td>

        <td>
            <input class="form-control input-sm" type="text" name="resources[##][resource_name]" id="resourceType##" j-model="resource_name" readonly>
        </td>

        <td>
            <input class="form-control input-sm" type="text" name="resources[##][budget_qty]" id="budgetQuantity##" j-model="budget_qty">
        </td>

        <td>
            <input class="form-control input-sm" type="text" name="resources[##][eng_qty]" id="engQuantity##" j-model="eng_qty">
        </td>

        <td>
            <input class="form-control input-sm" type="text" name="resources[##][resource_waste]" id="resourceWastete##" j-model="resource_waste">
        </td>

        <td>
            <input class="form-control input-sm" type="text" name="resources[##][labor_count]" id="laborsCount##" j-model="labor_count">
        </td>

        <td>
            <input class="form-control input-sm" type="text" name="resources[##][productivity_id]" id="laborsCount##" j-model="productivity_id">
        </td>

        <td>
            <input class="form-control input-sm" type="text" name="resources[##][remarks]" id="laborsCount##" j-model="remarks">
        </td>
    </tr>
</template>
