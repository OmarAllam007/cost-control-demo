<template>
    <article class="card">
        <h4 class="card-title">
            <span v-text="resource.name"></span> &mdash;
            <span class="text-muted text-capitalize" v-text="resource.code"></span>
        </h4>

        <div class="card-body" :class="{highlight: important}">
            <table class="table table-bordered table-striped table-condensed">
                <thead>
                <tr class="info">
                    <th class="text-center" colspan="4">Budget</th>
                    <th class="text-center" colspan="5">Actual</th>
                </tr>
                <tr>
                    <th width="11%" class="text-center">Unit Price</th>
                    <th width="11%" class="text-center">Budget Unit</th>
                    <th width="11%" class="text-center">Amount</th>
                    <th width="11%" class="text-center">U.O.M</th>

                    <th width="11%" class="text-center">Equiv. Unit Price</th>
                    <th width="11%" class="text-center">Qty</th>
                    <th width="11%" class="text-center">Amount</th>
                    <th width="11%" class="text-center">Qty Var.</th>
                    <th width="11%" class="text-center">Cost Var.</th>
                </tr>
                </thead>

                <tbody>
                <tr>
                    <td class="text-center" v-text="first.unit_price|number_format"></td>
                    <td class="text-center" v-text="budget_unit|number_format"></td>
                    <td class="text-center" v-text="budget_cost|number_format"></td>
                    <td class="text-center" v-text="first.measure_unit"></td>

                    <td class="text-center" v-text="actual_unit_price|number_format"></td>
                    <td class="text-center" v-text="actual_qty|number_format"></td>
                    <td class="text-center" v-text="actual_cost|number_format"></td>
                    <td class="text-center" :class="qty_var < 0? 'text-danger' : 'text-success'" v-text="qty_var|number_format"></td>
                    <td class="text-center" :class="cost_var < 0? 'text-danger' : 'text-success'" v-text="cost_var|number_format"></td>
                </tr>
                </tbody>
            </table>
            <hr>
            <div class="row">
                <article class="col-sm-3">
                    <table class="table table-striped table-condensed table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="text-center table-caption" colspan="5">Budget</th>
                        </tr>
                        <tr>
                            <th>Budget Unit</th>
                            <th>Unit Price</th>
                            <th>Amount</th>
                            <th>Cost Account</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="budget_resource in resource.budget_resources">
                            <td v-text="budget_resource.budget_unit|number_format"></td>
                            <td v-text="budget_resource.unit_price|number_format"></td>
                            <td v-text="budget_resource.budget_cost|number_format"></td>
                            <td v-text="budget_resource.cost_account"></td>
                        </tr>
                        </tbody>
                    </table>
                </article>

                <article class="col-sm-9 bl-1">
                    <table class="table table-striped table-condensed table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="text-center table-caption" colspan="10">Actual</th>
                        </tr>
                        <tr>
                            <th>Resource ID</th>
                            <th>Resource Name</th>
                            <th>UOM</th>
                            <th>Unit Price</th>
                            <th>Qty</th>
                            <th>Amount</th>
                            <th>Date from store</th>
                            <th>Date uploaded</th>
                            <th>Reference</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr v-for="actual_resource in resource.store_resources">
                            <td v-text="actual_resource.item_code"></td>
                            <td v-text="actual_resource.item_desc"></td>
                            <td v-text="actual_resource.measure_unit"></td>
                            <td v-text="actual_resource.unit_price|number_format"></td>
                            <td v-text="actual_resource.qty|number_format"></td>
                            <td v-text="actual_resource.cost|number_format"></td>
                            <td v-text="actual_resource.store_date"></td>
                            <td>
                                <a class="in-iframe"
                                   :href="`/actual-batches/${actual_resource.batch_id}`"
                                   v-text="actual_resource.created_at">
                                </a>
                            </td>
                            <td>
                                <a :href="`/actual-batches/${actual_resource.batch_id}/download`"
                                   v-text="actual_resource.doc_no">
                                </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </article>
            </div>
        </div>
    </article>
</template>

<script>
    import _ from 'lodash';

    export default {
        props: ['resource'],

        computed: {
            first() {
                return this.resource.budget_resources[0];
            },

            budget_unit() {
                let total = 0;
                this.resource.budget_resources.forEach(res => { total += res.budget_unit });
                return total;
            },

            budget_cost() {
                let total = 0;
                this.resource.budget_resources.forEach(res => { total += res.budget_cost });
                return total;
            },

            actual_unit_price() {
                if (!this.actual_qty) {
                    return 0;
                }

                return this.actual_cost / this.actual_qty;
            },

            actual_qty() {
                return this.actual_resources.reduce((total, r) => total += r.qty, 0) +
                    this.important_actual_resources.reduce((total, r) => total += r.qty, 0);
            },

            actual_cost() {
                return this.actual_resources.reduce((total, r) => total += r.cost, 0) +
                    this.important_actual_resources.reduce((total, r) => total += r.cost, 0);
            },

            actual_resources() {
                return _.flatMap(this.resource.budget_resources, r => r.actual_resources);
            },

            important_actual_resources() {
                return _.flatMap(this.resource.budget_resources, r => r.important_actual_resources);
            },

            qty_var() {
                return this.budget_unit - this.actual_qty;
            },

            cost_var() {
                return this.budget_cost - this.actual_cost;
            },

            important() {
                return this.resource.budget_resources.filter(res => res.important).length;
            }
        }
    };
</script>