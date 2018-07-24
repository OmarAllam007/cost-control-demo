<template>
    <article>
        <div class="alert alert-danger" v-if="error_messages.length">
            <ul>
                <li v-for="msg in error_messages">{{msg}}</li>
            </ul>
        </div>

        <table class="table table-condensed table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th class="text-center"><i class="fa fa-asterisk"></i></th>
                <th class="col-sm-2">Code</th>
                <th class="col-sm-4">Name</th>
                <th class="col-sm-1">Budget Qty</th>
                <th class="col-sm-1">Unit of Measure</th>
                <th class="col-sm-1">Budget Cost</th>
                <th class="col-sm-1">To date Qty</th>
                <th class="col-sm-1">To date Cost</th>
                <th class="col-sm-1">Progress</th>
            </tr>

            <tr class="info">
                <th class="text-center">
                    <input type="checkbox" :value="cost_account.id" :name="`cost_account[${cost_account.id}]`" v-model="selected">
                </th>
                <th v-text="cost_account.code"></th>
                <th v-text="cost_account.description"></th>
                <th :class="invalid_budget_qty? 'has-error' : ''">
                    <input class="form-control input-sm" type="text"
                           :name="`budget_unit[${cost_account.id}]`"
                           v-model="budget_unit"
                           :required="selected"
                           placeholder="Budget Qty">
                </th>
                <th :class="invalid_measure_unit? 'has-error' : ''">
                    <select class="form-control input-sm" v-model="measure_unit" :name="`measure_unit[${cost_account.id}]`" :required="selected">
                        <option value="">Select Unit</option>
                        <option v-for="unit in units" :value="unit.id" :key="unit.id" v-text="unit.type"></option>
                    </select>
                </th>
                <th v-text="total_budget_cost|number_format"></th>
                <th :class="invalid_to_date_qty">
                    <input class="form-control input-sm"
                           type="text"
                           v-model="to_date_qty"
                           :name="`to_date_qty[${cost_account.id}]`"
                           :required="selected"
                           placeholder="To date Qty">
                </th>
                <th v-text="total_to_date_cost|number_format"></th>
                <th>
                    <input class="form-control input-sm"
                           type="text"
                           v-model="progress"
                           :name="`progress[${cost_account.id}]`"
                           :required="selected"
                           placeholder="Progress">
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="resource in cost_account.resources" :key="resource.code" :class="resource.important? 'highlight' : ''">
                <td class="text-center"><i class="fa fa-asterisk" v-if="resource.important"></i></td>
                <td v-text="resource.code"></td>
                <td v-text="resource.name"></td>
                <td>{{resource.budget_qty|number_format}}</td>
                <td>{{resource.measure_unit}}</td>
                <td>{{resource.budget_cost|number_format}}</td>
                <td>{{resource.to_date_qty|number_format}}</td>
                <td>{{resource.to_date_cost|number_format}}</td>
                <td>{{resource.progress|number_format}}</td>
            </tr>
            </tbody>
        </table>
    </article>
</template>

<script>
    export default {
        props: ['initial'],

        data() {
            return {
                cost_account: this.initial, units: window.units, errors: {}, error_messages: {},
                selected: false, budget_unit: 1, to_date_qty: 1, measure_unit: 15, progress: 0
            };
        },

        created() {
            this.budget_unit = this.budget_qty;
            this.to_date_qty = 0;
            this.measure_unit = this.qs_unit;
        },

        methods: {
            validate(cost_account) {
                this.errors = {};
                this.error_messages = [];

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

                if (this.total_to_date_cost == 0 && parseFloat(this.to_date_qty) > 0) {
                    this.errors.to_date_qty = true;
                    this.error_messages.push("No actual cost to update to date qty");
                }

                if (this.total_to_date_cost > 0 && parseFloat(this.progress) <= 0) {
                    this.errors.progress = true;
                    this.error_messages.push("Progress is required");
                }

                if (this.total_to_date_cost == 0 && parseFloat(this.progress) > 0) {
                    this.errors.progress = true;
                    this.error_messages.push("No actual cost to update progress");
                }

                return this.error_messages.length === 0;
            },

            setChecked(state = true) {
                this.selected = state;
            },

            collectData() {
                return id;
            }
        },

        watch: {
            selected(state) {
                this.$emit('state-changed', state);
            }
        },

        computed: {
            total_budget_cost() {
                return this.cost_account.resources.reduce((total, resource) => total += resource.budget_cost, 0);
            },

            total_to_date_cost() {
                return this.cost_account.resources.reduce((total, resource) => total += resource.to_date_cost, 0);
            },

            budget_qty() {
                if (this.cost_account.resources.length) {
                    return this.cost_account.resources[0].budget_qty;
                }

                return 1;
            },

            qs_unit() {
                if (this.cost_account.resources.length > 0) {
                    return this.cost_account.resources[0].qs_unit;
                }

                return 0;
            }
        },

        filters: {
            number_format(str) {
                return parseFloat(str).toLocaleString();
            }
        }
    }
</script>