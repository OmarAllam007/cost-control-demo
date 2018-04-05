import Vue from 'vue';

Vue.component('ResourceLog', {
    props: ['resource']
});

Vue.filter('number_format', function(val) {
    return parseFloat(val).toLocaleString();
});

const app = new Vue({
    el: 'body',

    data: {
        resource_search: '', resource_mode: 'all', logs: [], loading: false,
        wbs_id: window.wbs_id, code: window.code
    },

    computed: {
        filteredLogs() {
            const term = this.resource_search.toLowerCase();

            return this.logs.filter(log => {
                return term === '' ||
                    log.code.toLowerCase().indexOf(term) > -1 ||
                    log.name.toLowerCase().indexOf(term) > -1;
            }).filter(log => {
                return this.resource_mode === 'all' ||
                    log.budget_resources.filter(res => res.important).length;
            });
        }
    },

    created() {
        this.loadLogs()
    },

    methods: {
        loadLogs() {
            this.loading = true;

            $.ajax({
                dataType: 'json',
                url: `/api/activity-log/${this.wbs_id}/${this.code}`,
            }).then(data => {
                this.logs = data;
                this.loading = false;
            }, () => {
                this.loading = false;
            });
        }
    }
});