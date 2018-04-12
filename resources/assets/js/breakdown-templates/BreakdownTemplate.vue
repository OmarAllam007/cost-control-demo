<template>
    <div class="row">
        <div class="col-sm-4 col-md-3 br-1">
            <divisions :divisions="divisions"></divisions>
        </div>

        <div class="col-sm-8 col-md-9">
            <templates :url="url"
                       :can_edit="can_edit"
            ></templates>
        </div>
    </div>
</template>
<script>
    export default {
        props: ['project_id', 'divisions', 'can_edit'],

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
                }
                return url;
            }
        }
    }
</script>