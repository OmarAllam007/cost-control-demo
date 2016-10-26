<template id="VariablesTemplate">
    <div id="VariablesModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Resource Variables</h4>
                </div>

                <div class="modal-body">


                    <div class="form-horizontal" v-if="vars.length">
                        <div class="form-group" v-for="_var in vars">
                            <label for="variables_@{{_var.id}}" class="control-label col-sm-2">@{{ _var.name }}</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="text" name="variables[@{{_var.id}}]" id="variables_@{{_var.id}}"
                                           v-model="_var.label" class="form-control">
                                    <span class="input-group-btn">
                                        <button class="btn btn-warning" @click="removeVariable($index)"><i
                                                class="fa fa-trash"></i></button>
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div> {{-- /End variables container --}}

                    <div class="alert alert-info" v-else>
                        <i class="fa fa-info-circle"></i> No variables found
                    </div>
                </div> {{-- /End modal body --}}

                <div class="modal-footer">
                    <div class="pull-right">
                        <button class="btn btn-primary" type="button" @click="addVariable"><i class="fa fa-plus"></i>
                        Add variable</button>
                        <button class="btn btn-default" type="button" data-dismiss="modal"><i class="fa fa-close"></i>
                            Close
                        </button>
                    </div>
                </div> {{-- /End modal footer --}}

            </div> {{-- /End modal content --}}
        </div>
    </div>
</template>

<variables :vars="{{old('variables', isset($std_activity->vars)? $std_activity->vars : '[]')}}"></variables>