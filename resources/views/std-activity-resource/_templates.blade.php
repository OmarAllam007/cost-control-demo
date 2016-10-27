@include('std-activity-resource._resources_modal')


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