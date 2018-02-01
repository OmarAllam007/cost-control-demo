<template>
    <li>
        <div class="wbs-item">
            <a href="#" class="open-level" @click.prevent="toggleChildren">
                <span class="wbs-icon"><i class="fa" :class="show_children? 'fa-minus-square-o' : 'fa-plus-square-o'"></i></span>

                {{cost_account.code}} &mdash;
                <small>{{cost_account.description}}</small>
            </a>
        </div>

        <table class="table table-condensed table-hover table-striped" v-if="show_children && hasResources()">
            <tbody>
            <tr v-for="resource in resources" :class="resource.important? 'danger' : ''" @click="resource.selected = !resource.selected">
                <td>
                    <input type="checkbox" :name="`resources[${resource.id}]`"
                           :value="resource.id"
                           v-model="resource.selected">
                </td>
                <td v-text="resource.code"></td>
                <td v-text="resource.name"></td>
                <td v-text="resource.budget_unit"></td>
                <td v-text="resource.remarks"></td>
            </tr>
            </tbody>
        </table>
    </li>
</template>

<script>
    export default {
        props: ['initial'],

        data() {
            return {cost_account: this.initial, resources: [], show_children: false, loading: false}
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
            }
        }
    };
</script>