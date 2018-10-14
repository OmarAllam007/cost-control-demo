export default {
    props: ['project'],

    data() {
        return {requests: [], loading: false};
    },

    computed: {},

    created() {
        this.loadRequests();
        console.log('asdad')
    },

    methods: {
        loadRequests() {
            $.ajax({url: `/project/${this.project.id}/change-request`, dataType: 'json'})
                .success(response => {
                    this.loading = false;
                    this.requests = response.requests;
                })
                .error(response => {
                    this.loading = false;
                });
        }
    },

    events: {}
}