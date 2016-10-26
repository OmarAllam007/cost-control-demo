<template id="resourcesEmptyAlert"><div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Please select cost account and breakdown template</div></template>
<template id="resourcesLoading"><div class="alert alert-info" id="resourcesLoading"><i class="fa fa-spinner fa-spin"></i> Loading...</div></template>
<template id="resourcesError"><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Error loading breakdown resources</div></template>
<template id="containerTemplate">
    @include('breakdown._resource_container', ['include' => false])
</template>

<template id="resourceRowTemplate">
    @include('breakdown._resource_template', ['index' => '##'])
</template>

<template id="variableTemplate">
    <div class="form-group">
        <label for="var_{display_order}" class="control-label col-sm-3 var-name">{label}</label>
        <div class="col-sm-9">
            <input id="var_{display_order}" type="text" class="form-control" name="variables[{display_order}]">
        </div>
    </div>
</template>

<div class="modal fade" id="VariablesModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Variables</h4>
            </div>
            <div class="modal-body form-horizontal" id="VariablesPane">

            </div>
        </div>
    </div>
</div>