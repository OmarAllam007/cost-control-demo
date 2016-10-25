<template id="ResourcesTemplate">
    <div id="ResourcesModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Select Resource</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group form-group-sm">
                        <input type="text" v-model="term" placeholder="Type here to search" class="form-control search"
                               debounce="500" autocomplete="off">
                    </div>

                    <section>
                            <ul class="list-unstyled tree">
                                @foreach(App\ResourceType::tree()->get() as $type)
                                    @include('resources._recursive_resource_input', ['type' => $type, 'value' => Form::getValueAttribute('resource_id')])
                                @endforeach
                            </ul>
                        {{--<div class="alert alert-warning" v-else><i class="fa fa-exclamation-triangle"></i> No resources
                            found
                        </div>--}}
                    </section>


                </div>
            </div>
        </div>
    </div>
</template>
<resources resource=""></resources>


<template id="ProductivityTemplate">
    <div id="ProductivityModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Select Productivity</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group form-group-sm">
                        <input type="text" v-model="term" placeholder="Type here to search" class="form-control search"
                               autocomplete="off">
                    </div>
                    <table class="table table-condensed table-hover table-striped" v-if="productivities.length">
                        <thead>
                        <tr>

                            <th>Code</th>
                            <th>Daily Output</th>
                            <th>Reduction factor</th>
                            <th>After Reduction</th>
                        </tr>
                        </thead>
                        <tbody v-for="productivity in productivities">
                        <tr>
                            <td>
                                <label>
                                    <input type="radio" name="productivity_id" :value="productivity.id"
                                           v-on:change="setProductivity(productivity)"
                                           :checked="productivity.id == selected.id" debounce="500">
                                    @{{ productivity.code }}
                                </label>
                            </td>
                            <td>@{{ productivity.daily_output }}</td>
                            <td>@{{ productivity.reduction }}</td>
                            <td>@{{ productivity.after_reduction }}</td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="alert alert-warning" v-else>
                        <i class="fa fa-exclamation-triangle"></i> No productivity found
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<productivity></productivity>

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

<variables
        :vars="{{old('variables', isset($std_activity_resource->vars)? $std_activity_resource->vars : '[]')}}"></variables>