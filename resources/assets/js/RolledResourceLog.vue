<template>
    <article class="card">
        <h4 class="card-title display-flex">
            <span class="flex">
                <span v-text="resource.name"></span> &mdash;
                <span class="text-muted text-capitalize" v-text="resource.code"></span>
            </span>

            <span class="text-danger" title="Driving Resource" v-if="important"><i class="fa fa-asterisk"></i></span>
        </h4>

        <div class="card-body highlight">
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
                    <td class="text-center" :class="qty_var < 0? 'text-danger' : 'text-success'"
                        v-text="qty_var|number_format"></td>
                    <td class="text-center" :class="cost_var < 0? 'text-danger' : 'text-success'"
                        v-text="cost_var|number_format"></td>
                </tr>
                </tbody>
            </table>
            <hr>
            <div class="row">
                <article class="col-sm-6">
                    <table class="table table-striped table-condensed table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="text-center table-caption" colspan="7">Budget</th>
                        </tr>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>U.O.M</th>
                            <th>Budget Unit</th>
                            <th>Unit Price</th>
                            <th>Amount</th>
                            <th>Cost Account</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="budget_resource in resource.budget_resources">
                            <td v-text="budget_resource.resource_code"></td>
                            <td v-text="budget_resource.resource_name"></td>
                            <td v-text="budget_resource.measure_unit"></td>
                            <td v-text="budget_resource.budget_unit|number_format"></td>
                            <td v-text="budget_resource.unit_price|number_format"></td>
                            <td v-text="budget_resource.budget_cost|number_format"></td>
                            <td v-text="budget_resource.cost_account"></td>
                        </tr>
                        </tbody>
                    </table>
                </article>

                <article class="col-sm-6 bl-1">
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
    export default {
        props: ['resource'],

        computed: {
            first() {
                return this.resource.rollup_resource;
            },

            important() {
                return this.resource.rollup_resource.important;
            },

            budget_unit() {
                return this.resource.rollup_resource.budget_unit;
            },

            budget_cost() {
                return this.resource.rollup_resource.budget_cost;
            },

            actual_unit_price() {
                if (!this.actual_qty) {
                    return 0;
                }

                return this.actual_cost / this.actual_qty;
            },

            actual_qty() {
                return this.resource.actual_resources.reduce((total, r) => total += r.qty, 0);
            },

            actual_cost() {
                return this.resource.actual_resources.reduce((total, r) => total += r.cost, 0);
            },

            qty_var() {
                return this.budget_unit - this.actual_qty;
            },

            cost_var() {
                return this.budget_cost - this.actual_cost;
            }
        }
    };
</script>