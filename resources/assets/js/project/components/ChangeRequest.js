export default {
    props: ['project'],

    data() {
        return {requests: [], loading: false};
    },

    computed: {},

    created() {
        this.loadRequests();
    },

    methods: {
        loadRequests() {
            this.loading = true;
            $.ajax({
                url: `/project/${this.project.id}/change-request`, dataType: 'json'
            }).success(response => {
                this.loading = false;
                this.requests = response;
            }).error(response => {
                this.loading = false;
            });
        }
    },
    events: {
        reload_change_request() {
            this.loadRequests();
        }
    }
}