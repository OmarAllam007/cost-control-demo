<template id="resourcesEmptyAlert"><div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Please select breakdown template</div></template>
<template id="resourcesLoading"><div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Loading...</div></template>
<template id="resourcesError"><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Error loading breakdown resources</div></template>
<template id="containerTemplate">
    @include('breakdown._resource_container', ['include' => false])
</template>

<template id="resourceRowTemplate">
    @include('breakdown._resource_template', ['index' => '##'])
</template>