<template>
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
        </tr>

        <tr class="info">
            <th class="text-center">
                <input type="checkbox" :value="cost_account.id" :name="`cost_account[${cost_account.id}]`" v-model="selected">
            </th>
            <th v-text="cost_account.code"></th>
            <th v-text="cost_account.description"></th>
            <th :class="invalid_budget_qty? 'has-error' : ''">
                <input class="form-control input-sm" type="text" :value="budget_qty"
                       :name="`budget_unit[${cost_account.id}]`"
                       :required="selected"
                       placeholder="Budget Qty">
            </th>
            <th :class="invalid_measure_unit? 'has-error' : ''">
                <select class="form-control input-sm" :name="`measure_unit[${cost_account.id}]`" :required="selected">
                    <option value="">Select Unit</option>
                    <option v-for="unit in units" :selected="unit.id == qs_unit" :value="unit.id" :key="unit.invalid_measure_unit" v-text="unit.type"></option>
                </select>
            </th>
            <th v-text="total_budget_cost"></th>
            <th :class="invalid_to_date_qty">
                <input class="form-control input-sm"
                       type="text"
                       :name="`to_date_qty[${cost_account.id}]`"
                       :required="selected"
                       placeholder="To date Qty">
            </th>
            <th v-text="total_to_date_cost"></th>
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
        </tr>
        </tbody>
    </table>
</template>

<script>
    export default {
        props: ['initial'],

        data() {
            return {
                cost_account: this.initial, units: window.units,
                selected: false
            };
        },

        methods: {
            setChecked(state = true) {
                this.selected = state;
            }
        },

        watch: {
            selected(state) {
                this.$emit('state-changed', state);
            }
        },

        computed: {
            total_budget_cost() {
                let total = 0;

                this.cost_account.resources.forEach(function (resource) {
                    total += resource.budget_cost;
                });

                return parseFloat(total.toFixed(2)).toLocaleString();
            },

            total_to_date_cost() {
                let total = 0;
                this.cost_account.resources.forEach(function (resource) {
                    total += resource.to_date_cost;
                });

                return parseFloat(total.toFixed(2)).toLocaleString();
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
                return parseFloat(str.toFixed(2)).toLocaleString();
            }
        }
    }
</script>