<template>
    <div class="row">
        <div class="col-sm-4 col-md-3 br-1">
            <divisions :divisions="divisions"></divisions>
        </div>

        <div class="col-sm-8 col-md-9">
            <templates :url="url"
                       :can_edit="can_edit"
                       :can_delete="can_delete"
                       :enable-select="enableSelect"
            ></templates>
        </div>
    </div>
</template>
<script>
    import divisions from './divisions.vue';
    import templates from './templates.vue';

    export default {
        name: 'BreakdownTemplate',

        props: ['project_id', 'divisions', 'can_edit', 'can_delete', 'reject', 'enableSelect'],

        data() {
            return {division: 0, activity: 0, term: ''};
        },

        computed: {
            url() {
                let url = '';
                if (this.activity || this.division) {
                    url = `/api/breakdown-template?division=${this.division}&activity=${this.activity}`;

                    if (this.project_id) {
                        url += `&project_id=${this.project_id}`;
                    }

                    if (this.term) {
                        url += `&term=${this.term}`;
                    }

                    if (this.reject) {
                        url += `&reject=${this.reject}`;
                    }
                }
                return url;
            }
        },

        components: { divisions, templates }
    }
</script>