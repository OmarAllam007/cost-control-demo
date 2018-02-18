<template>
    <li>
        <label>
            <input type="checkbox" :value="cost_account.id" :name="`cost_account[${cost_account.id}]`">
            {{cost_account.code}} &mdash; <small>{{cost_account.description}}</small>
        </label>

        <table class="table table-condensed table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th>&nbsp;</th>
                <th>Code</th>
                <th>Name</th>
                <th>Budget Unit</th>
                <th>Unit of Measure</th>
                <th>Budget Cost</th>
                <th>To date Qty</th>
                <th>To date Cost</th>
            </tr>

            <tr>
                <th>&nbsp;</th>
                <th v-text="cost_account.code"></th>
                <th v-text="cost_account.description"></th>
                <th><input class="form-control input-sm" type="number" :value="1" :name="`budget_unit[${cost_account.id}]`" placeholder="Budget Qty"></th>
                <th>
                    <select class="form-control input-sm" :name="`measure_unit[${cost_account.id}]`">
                        <option value="">Select Unit</option>
                    </select>
                </th>
                <th v-text="total_budget_cost"></th>
                <th><input class="form-control input-sm" type="number" value="1" :name="`to_date_qty[${cost_account.id}]`" placeholder="To date Qty"></th>
                <th v-text="total_to_date_cost"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="resource in cost_account.resources">
                <td><i class="fa fa-asterisk" v-if="resource.important"></i></td>
                <td v-text="resource.code"></td>
                <td v-text="resource.name"></td>
                <td>{{resource.budget_unit|number_format}}</td>
                <td>{{resource.measure_unit}}</td>
                <td>{{resource.budget_cost|number_format}}</td>
                <td>{{resource.to_date_qty|number_format}}</td>
                <td>{{resource.to_date_cost|number_format}}</td>
            </tr>
            </tbody>
        </table>
    </li>
</template>

<script>
    export default {
        props: ['initial'],

        data() {
            return { cost_account: this.initial };
        },

        computed: {
            total_budget_cost() {
                let total = 0;

                this.cost_account.resources.forEach(function(resource) {
                    total += resource.budget_cost;
                });

                return parseFloat(total.toFixed(2)).toLocaleString();
            },

            total_to_date_cost() {
                let total = 0;
                this.cost_account.resources.forEach(function(resource) {
                    total += resource.to_date_cost;
                });

                return parseFloat(total.toFixed(2)).toLocaleString();
            }
        },

        filters: {
            number_format(str) {
                return parseFloat(str.toFixed(2)).toLocaleString();
            }
        }
    }
</script>