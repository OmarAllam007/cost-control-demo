import Vue from 'vue';
import ResourceLog from './ResourceLog.vue';
import RolledResourceLog from './RolledResourceLog.vue';

Vue.filter('number_format', function(val) {
    return parseFloat(val).toLocaleString();
});

const app = new Vue({
    el: 'body',

    components: { ResourceLog, RolledResourceLog },

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
                if (window.is_activity_rollup) {
                    return (this.resource_mode === 'important' &&  !log.rollup) || (this.resource_mode !== 'important' &&  log.rollup);
                } else {
                    return this.resource_mode === 'all' ||
                        log.budget_resources.filter(res => res.important).length;
                }
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
                url: `/api/activity-log/${this.wbs_id}?code=${this.code}`,
            }).then(data => {
                this.logs = data;
                this.loading = false;
            }, () => {
                this.loading = false;
            });
        }
    }
});