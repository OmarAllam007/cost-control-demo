<template>
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
</template>
<script>
    export default {
        name: 'Templates',

        props: ['url', 'can_edit'],

        data() {
            return { templates: []};
        },

        created() {
            this.loadTemplates();
        },

        watch: {
            url() {
                this.loadTemplates();
            }
        },

        methods: {
            loadTemplates() {
                if (this.url) {
                    $.ajax({
                        url: this.url, dataType: 'json'
                    }).done(response => {
                        console.log(response);
                        this.templates = response.data
                    });
                } else {
                    this.templates = [];
                }
            }
        }
    }
</script>