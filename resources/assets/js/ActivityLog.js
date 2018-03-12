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
        resource_search: '', resource_mode: '', logs: [], loading: false,
        wbs_id: window.wbs_id, code: window.code
    },

    computed: {
        filteredLogs() {
            return this.logs;
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