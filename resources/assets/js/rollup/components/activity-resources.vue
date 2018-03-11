<template>
    <li class="panel panel-primary" :class="`level-${depth}`">
        <div class="panel-heading">
            <h4 class="panel-title">{{activity.activity}} <small>({{activity.code}})</small></h4>
        </div>

        <table v-if="resources.length" class="table table-bordered table-condensed table-hover table-striped">
            <thead>
            <tr>
                <td class="text-center"><input type="checkbox" v-model="check_all"></td>
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
                <th></th>
                <th></th>
                <th></th>
                <th>
                    <input type="text" class="form-control input-sm" :name="`budget_unit[${activity.code}]`" placeholder="Budget Qty" :value="budget_qty" :required="selected">
                </th>
                <th>
                    <select class="form-control input-sm" :name="`measure_unit[${activity.code}]`" :required="selected">
                        <option value="">Select Unit</option>
                        <option v-for="unit in units" :selected="unit.id == qs_unit" :value="unit.id" v-text="unit.type"></option>
                    </select>
                </th>
                <th>{{total_budget_cost}}</th>
                <th>
                    <input type="text" class="form-control input-sm" :name="`to_date_qty[${activity.code}]`" placeholder="To date qty" :required="selected">
                </th>
                <th>{{total_to_date_cost}}</th>
                <th>
                    <input type="text" class="form-control input-sm" :name="`progress[${activity.code}]`" placeholder="Progress" :required="selected">
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="resource in resources" :class="resource.important? 'danger' : ''" @click="resource.selected = !resource.selected">
                <td class="text-center">
                    <input type="checkbox" :name="get_input_name(resource)"
                           :value="resource.id"
                           v-model="resource.selected"
                           v-if="!resource.important">
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

        <div class="panel-body text-center" v-else>
            <i class="fa fa-2x fa-spinner fa-spin"></i>
        </div>
    </li>
</template>

<script>
export default {
    props: ['initial', 'depth'],

    data() {
        return { activity: this.initial, resources: [], check_all: false, units: window.units };
    },

    created() {
        this.loadResources();
    },

    methods: {
        loadResources() {
            this.loading = true;

            $.ajax({
                url: `/api/rollup/activity-resources/${this.$parent.level.id}/${this.activity.code}`,
                dataType: 'json'
            }).then((data) => {
                this.resources = data;
                this.loading = false;
            }, () => {
                this.loading = false;
            });

        },

        get_input_name(resource, type = 'resources') {
            return `${type}[${this.activity.code}][${resource.id}]`;
        },

        setChecked(state) {
            this.check_all = state;
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
            return !! this.resources.filter(res => res.selected).length;
        },

        total_budget_cost() {
            let total = 0;

            this.resources.forEach(function(resource) {
                total += resource.budget_cost;
            });

            return parseFloat(total.toFixed(2)).toLocaleString();
        },

        total_to_date_cost() {
            let total = 0;

            this.resources.forEach(function(resource) {
                total += resource.to_date_cost;
            });

            return parseFloat(total.toFixed(2)).toLocaleString();
        },

        budget_qty() {
            if (this.resources.length) {
                return this.resources[0].budget_qty;
            }

            return 1;
        },

        qs_unit() {
            if (this.cost_account.resources.length > 0) {
                return this.resources[0].qs_unit;
            }

            return 0;
        }
    }
}
</script>