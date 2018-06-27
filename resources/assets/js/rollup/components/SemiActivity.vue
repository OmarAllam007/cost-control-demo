<template>
    <section class="card semi-activity" :class="`level-${depth}`">


        <div class="card-title display-flex">
            <h4 class="flex">
                <a href="#" @click.prevent="expanded = ! expanded">
                    {{activity.name}} &mdash;
                    <small>({{activity.code}})</small>
                </a>
            </h4>

            <button type="button" class="btn btn-primary btn-sm" v-show="selected > 1" @click="doRollup">
                <i class="fa fa-compress"></i> Rollup
            </button>
        </div>

        <div class="card-body" v-show="expanded">
            <div class="alert alert-danger" v-if="error_messages.length">
                <ul>
                    <li v-for="msg in error_messages">{{msg}}</li>
                </ul>
            </div>

            <table v-if="resources.length" class="table table-bordered table-condensed table-hover table-striped">
                <thead>
                <tr>
                    <td class="text-center"><input type="checkbox" title="Select All" v-model="check_all"></td>
                    <td>Resource Code</td>
                    <td>Resource Name</td>
                    <td>Remarks</td>
                    <td>Budget Unit</td>
                    <td>Unit of measure</td>
                    <td>Budget Cost</td>
                    <td>To Date Qty</td>
                    <td>To Date Cost</td>
                    <td>Progress</td>
                </tr>
                <tr class="info">
                    <th class="text-center">&nbsp;</th>
                    <th :class="{'has-error': errors.resource_code}">
                        <input type="text" class="form-control input-sm"
                               v-model="resource_code"
                               :name="`resource_code[${activity.code}]`"
                               placeholder="Resource Code"
                               :required="selected">
                    </th>
                    <th  :class="{'has-error': errors.resource_name}">
                        <input type="text" class="form-control input-sm"
                               v-model="resource_name"
                               :name="`resource_name[${activity.code}]`"
                               placeholder="Resource Name"
                               :required="selected">
                    </th>
                    <th><input type="text" class="form-control input-sm"
                               v-model="remarks"
                               :name="`remarks[${activity.code}]`"
                               placeholder="Remarks"
                               ></th>
                    <th :class="{'has-error': errors.budget_unit}">
                        <input type="text" class="form-control input-sm"
                               v-model="budget_unit"
                               :name="`budget_unit[${activity.code}]`"
                               placeholder="Budget Qty"
                               :value="budget_qty" :required="selected">
                    </th>

                    <th :class="{'has-error': errors.measure_unit}">
                        <select class="form-control input-sm"
                                v-model="measure_unit" :name="`measure_unit[${activity.code}]`"
                                :required="selected" title="Select unit of measure">

                            <option value="">Select Unit</option>
                            <option v-for="unit in units" :value="unit.id" v-text="unit.type" :key="unit.id"></option>
                        </select>
                    </th>
                    <th v-text="total_budget_cost"></th>
                    <th :class="{'has-error': errors.to_date_qty}">
                        <input type="text" class="form-control input-sm" :name="`to_date_qty[${activity.code}]`"
                               v-model="to_date_qty" placeholder="To date qty" :required="selected">
                    </th>
                    <th v-text="total_to_date_cost"></th>
                    <th :class="{'has-error': errors.progress}">
                        <input type="text" class="form-control input-sm" :name="`progress[${activity.code}]`"
                               v-model="progress" placeholder="Progress" :required="selected">
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="resource in resources" :class="resource.important? 'danger' : ''"
                    @click="resource.selected = !resource.selected">
                    <td class="text-center">
                        <input :title="`Select ${resource.name}`" type="checkbox" :name="get_input_name(resource)"
                               :value="resource.id"
                               v-model="resource.selected">
                    </td>
                    <td v-text="resource.code"></td>
                    <td v-text="resource.name"></td>
                    <td v-text="resource.remarks"></td>
                    <td>{{resource.budget_unit | number_format}}</td>
                    <td>{{resource.measure_unit}}</td>
                    <td>{{resource.budget_cost | number_format}}</td>
                    <td>{{resource.to_date_qty|number_format}}</td>
                    <td>{{resource.to_date_cost|number_format}}</td>
                    <td>{{resource.progress|number_format}}%</td>
                </tr>
                </tbody>
            </table>

            <div class="loading text-center" v-else>
                <i class="fa fa-2x fa-spinner fa-spin"></i>
            </div>
        </div>
    </section>
</template>

<style scoped>
    .semi-activity .table {
        margin-bottom: 0;
    }

    .card-title h4 {
        margin-top: 6px;
        margin-bottom: 6px;
    }

    .semi-activity {
        position: relative;
    }

    .loading {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        background: rgba(255, 255, 255, 0.7);
    }
</style>

<script>
    export default {
        name: 'SemiActivity',

        props: ['initial', 'depth'],


        data() {
            return {
                activity: this.initial,
                resources: [],
                check_all: false,
                units: window.units,
                expanded: false,
                budget_unit: 0, measure_unit: 0, to_date_qty: 0, progress: 0,
                resource_code: this.initial.code + '.' + this.initial.next_rollup_code, resource_name: this.initial.name, remarks: 'Semi Activity Rollup',
                token: document.querySelector('meta[name=csrf-token]').content, errors: {}, error_messages: []
            };
        },

        created() {
            this.loadResources();
        },

        methods: {
            validate() {
                this.errors = {};
                this.error_messages = [];

                if (!this.resource_code) {
                    this.errors.resource_code = true;
                    this.error_messages.push("Resource code is required");
                }

                this.resources.forEach(resource => {
                    if (resource.code === this.resource_code) {
                        this.errors.resource_code = true;
                        this.error_messages.push("Resource code already found");
                    }
                });

                if (!this.resource_name) {
                    this.errors.resource_name = true;
                    this.error_messages.push("Resource name is required");
                }

                if (!this.budget_unit || !parseFloat(this.budget_unit)) {
                    this.errors.budget_unit = true;
                    this.error_messages.push("Budget unit is required");
                }

                if (!this.measure_unit) {
                    this.errors.measure_unit = true;
                    this.error_messages.push("Unit of measure is required");
                }

                if (this.total_to_date_cost > 0 && parseFloat(this.to_date_qty) <= 0) {
                    this.errors.to_date_qty = true;
                    this.error_messages.push("To date qty is required");
                }

                if (this.total_to_date_cost > 0 && parseFloat(this.progress) <= 0) {
                    this.errors.progress = true;
                    this.error_messages.push("Progress is required");
                }
            },

            loadResources() {
                this.loading = true;

                $.ajax({
                    url: `/api/rollup/activity-resources/${this.activity.wbs_id}?code=${this.activity.code}`,
                    dataType: 'json'
                }).then((data) => {
                    if (!data.resources.length) {
                        this.$emit('delete-activity', this.activity);
                    }

                    this.resources = data.resources;
                    this.budget_unit = data.resources.length ? data.resources[0].budget_qty : 1;
                    this.measure_unit = data.resources.length ? data.resources[0].qs_unit : 1;

                    this.resource_code = this.initial.code + '.' + data.next_rollup_code;
                    this.resource_name = this.initial.name;
                    this.remarks = '';

                    this.loading = false;
                }, () => {
                    this.loading = false;
                });

            },

            get_input_name(resource, type = 'resources') {
                return `${type}[${this.activity.code}][${resource.id}]`;
            },

            doRollup() {
                if (!this.validate()) {
                    return false;
                }

                this.loading = true;

                const resources = {};
                resources[this.activity.code] = this.resources.filter(res => res.selected).map(res => res.id);

                const budget_units = {}, measure_units = {},
                    to_date_qtys = {}, progress = {},
                    resource_codes = {}, resource_names = {}, remarks={};

                budget_units[this.activity.code] = this.budget_unit;
                measure_units[this.activity.code] = this.measure_unit;
                to_date_qtys[this.activity.code] = this.to_date_qty;
                progress[this.activity.code] = this.progress;
                resource_codes[this.activity.code] = this.resource_code;
                resource_names[this.activity.code] = this.resource_name;
                remarks[this.activity.code] = this.remarks;


                // The API end point requires data to be in this shape 😩
                // const data = ;

                const url = `/project/${this.activity.project_id}/rollup-semi-activity`;

                $.ajax({
                    url,
                    method: 'post',
                    dataType: 'json',
                    data: {
                            _token: this.token, resources,
                            remarks,
                            budget_unit: budget_units,
                            measure_unit: measure_units,
                            to_date_qty: to_date_qtys,
                            progress: progress, resource_codes, resource_names
                    }
                }).then(
                    () => {
                        this.loading = false;
                        this.loadResources();
                        // this.$emit('delete-activity', this.activity);
                    }, () => {
                        this.loading = false
                    }
                );
            }
        },

        watch: {
            check_all(checked) {
                this.resources.filter(resource => !resource.important).forEach(resource => {
                    resource.selected = checked;
                });
            }
        },

        filters: {
            number_format(val) {
                if (!val) {
                    val = 0;
                }
                return parseFloat(val.toFixed(2)).toLocaleString();
            }
        },

        computed: {
            selected() {
                return this.resources.filter(res => res.selected).length;
            },

            total_budget_cost() {
                let resources = this.resources;
                if (this.selected) {
                    resources = resources.filter(res => res.selected);
                }

                const total = resources.reduce((total, resource) => total + resource.budget_cost, 0);

                return parseFloat(total.toFixed(2)).toLocaleString();
            },

            total_to_date_cost() {
                let resources = this.resources;
                if (this.selected) {
                    resources = resources.filter(res => res.selected);
                }

                const total = resources.reduce((total, resource) => total + resource.to_date_cost, 0);

                return parseFloat(total.toFixed(2)).toLocaleString();
            }
        }
    }
</script>