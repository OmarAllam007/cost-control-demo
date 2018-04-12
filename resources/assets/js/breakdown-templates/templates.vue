<template>
    <section>
        <section v-if="templates.length">
        <div class="form-group">
            <input type="search" class="form-control" v-model="search" placeholder="Search by code or name">
        </div>

        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th class="col-xs-3">Code</th>
                <th class="col-xs-6">Template</th>
                <th class="col-xs-3">Actions</th>
            </tr>
            </thead>

            <tbody>
            <tr v-for="tpl in templates">
                <td>
                    <a :href="`/breakdown-template/${tpl.id}`" v-text="tpl.code"></a>
                </td>
                <td>
                    <a :href="`/breakdown-template/${tpl.id}`" v-text="tpl.name"></a>
                </td>
                <td>
                    <a class="btn btn-sm btn-info" :href="`/breakdown-template/${tpl.id}`">
                        <i class="fa fa-eye"></i> Show
                    </a>

                    <a v-if="can_edit" class="btn btn-sm btn-primary" :href="`/breakdown-template/${tpl.id}/edit`">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                </td>
            </tr>
            </tbody>
        </table>
        </section>
        <div v-else class="alert alert-info">
            <i class="fa fa-arrow-left"></i>
            Please select a division or activity
        </div>
    </section>
</template>
<script>
    import _ from 'lodash';

    export default {
        name: 'Templates',

        props: ['url', 'can_edit'],

        data() {
            return {templates: [], search: ''};
        },

        created() {
            this.loadTemplates();
        },

        watch: {
            url() {
                this.loadTemplates();
            },

            search() {
                this.updateSearch();
            }
        },

        methods: {
            loadTemplates() {
                if (this.url) {
                    $.ajax({
                        url: this.url, dataType: 'json'
                    }).done(response => {
                        this.templates = response.data
                    });
                } else {
                    this.templates = [];
                }
            },

            updateSearch: _.debounce(function() {
                this.$root.term = this.search;
            }, 500)
        }
    }
</script>