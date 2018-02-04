<template>
    <li>
        <div class="wbs-item">
            <a href="#" class="open-level" @click.prevent="toggleChildren">
                <span class="wbs-icon"><i class="fa" :class="show_children? 'fa-minus-square-o' : 'fa-plus-square-o'"></i></span>

                {{cost_account.code}} &mdash;
                <small>{{cost_account.description}}</small>
            </a>
        </div>

        <div class="row" v-if="show_children && hasResources()">
            <div class="col-sm-10">
                <table class="table table-condensed table-hover table-striped">
                    <thead>
                    <tr @click="checkAll">
                        <td class="col-sm-1"><input type="checkbox" v-model="check_all"></td>
                        <td class="col-sm-2">Resource Code</td>
                        <td class="col-sm-5">Resource Name</td>
                        <td class="col-sm-3">Remarks</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="resource in resources" :class="resource.important? 'danger' : ''" @click="resource.selected = !resource.selected">
                        <td class="col-sm-1">
                            <input type="checkbox" :name="get_input_name(resource)"
                                   :value="resource.id"
                                   v-model="resource.selected">
                        </td>
                        <td class="col-sm-2" v-text="resource.code"></td>
                        <td class="col-sm-5" v-text="resource.name"></td>
                        <td class="col-sm-3" v-text="resource.remarks"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </li>
</template>

<script>
    export default {
        props: ['initial'],

        data() {
            return {cost_account: this.initial, resources: [], show_children: false, loading: false, check_all: false}
        },

        methods: {
            toggleChildren() {
                this.show_children = !this.show_children;

                if (!this.resources.length) {
                    this.loading = true;

                    $.ajax({
                        url: `/api/rollup/cost-account/${this.cost_account.wbs_id}/${this.cost_account.id}`,
                        dataType: 'json'
                    }).then(data => {
                        this.loading = false;
                        this.resources = data;
                    }, () => {
                        this.loading = false;
                    })
                }
            },

            hasResources() {
                return Object.keys(this.resources).length;
            },

            checkAll() {
                this.check_all = !this.check_all;

                this.resources.filter(resource => !resource.important).forEach(resource => {
                    resource.selected = this.check_all;
                });
            },

            get_input_name(resource) {
                return `resources[${this.cost_account.id}][${resource.id}]`;
            }
        }
    };
</script>