<div id="vueRoot">

    <div class="panel panel-default panel-form">
        <div class="panel-heading clearfix">
            <h4 class="panel-title pull-left">User Permissions</h4>
            <button class="btn btn-primary btn-sm pull-right" type="button" @click="addUser"><i
                    class="fa fa-plus"></i>
            </button>
        </div>
        @if(isset($project->permissions))
            <user-list :users="{{json_encode($project->permissions)}}"></user-list>
        @endif
    </div>

    <user-form :users="{{App\User::options()}}"></user-form>
</div>


<template id="user-form">
    <div class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@{{action}} User</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">

                        <select id="user_id" v-model="user.user_id" class="form-control" v-if="!edit">
                            <option value="">Select User</option>
                            <option v-for="(id, name) in users" :value="id" v-text="name"></option>
                        </select>
                        <div v-else>
                            <input type="text"  v-model="user.name" class="form-control" readonly>
                            <input type="hidden" v-model="user.user_id">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" :checked="user.budget" v-model="user.budget" value="1"> Display budget
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" :checked="user.cost_control" v-model="user.cost_control" value="1"> Display cost control
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" :checked="user.reports" v-model="user.reports" value="1"> Display reports
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" :checked="user.wbs" v-model="user.wbs" value="1"> Manage WBS
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" :checked="user.breakdown" v-model="user.breakdown" value="1"> Manage breakdowns
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" :checked="user.breakdown_templates" v-model="user.breakdown_templates" value="1"> Manage breakdown templates
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" :checked="user.resources" v-model="user.resources" value="1"> Manage resources
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" :checked="user.productivity" v-model="user.productivity" value="1"> Manage productivity
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" :checked="user.boq" v-model="user.boq" value="1"> Manage BOQ
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" :checked="user.qty_survey" v-model="user.qty_survey" value="1"> Manage Quantity Survey
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" :checked="user.actual_resources" v-model="user.actual_resources"> Manage actual resources
                            </label>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click="save"><i class="fa fa-check"></i> Save
                    changes</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i>
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="user-list">
    <div class="list-group" v-show="users.length">
        <div class="list-group-item clearfix" v-for="user in users">
            <div class="user-permission-name pull-left">
                @{{ user.name }}
                <input type="hidden" name="users[@{{$index}}][user_id]" :value="user.user_id">
                <input type="hidden" name="users[@{{$index}}][budget]" :value="user.budget? 1: 0">
                <input type="hidden" name="users[@{{$index}}][cost_control]" :value="user.cost_control? 1: 0">
                <input type="hidden" name="users[@{{$index}}][reports]" :value="user.reports? 1: 0">
                <input type="hidden" name="users[@{{$index}}][wbs]" :value="user.wbs? 1: 0">
                <input type="hidden" name="users[@{{$index}}][breakdown]" :value="user.breakdown? 1 : 0">
                <input type="hidden" name="users[@{{$index}}][breakdown_templates]" :value="user.breakdown_templates? 1: 0">
                <input type="hidden" name="users[@{{$index}}][resources]" :value="user.resources? 1: 0">
                <input type="hidden" name="users[@{{$index}}][productivity]" :value="user.productivity? 1: 0">
                <input type="hidden" name="users[@{{$index}}][actual_resources]" :value="user.actual_resources? 1: 0">
            </div>

            <div class="btn-toolbar pull-right">
                <button class="btn btn-primary btn-sm" type="button" @click="editUser(user)"><i class="fa fa-edit"></i></button>
                <button class="btn btn-danger btn-sm" type="button" @click="removeUser(user)"><i class="fa fa-remove"></i></button>
            </div>
        </div>
    </div>
</template>