export default {
    props: ['project'],

    data() {
        return {revisions: [], loading: false};
    },

    computed: {

    },

    created() {
        this.loadRevisions();
    },

    methods: {
        loadRevisions() {
            this.loading = true;

            $.ajax({url: `/project/${this.project.id}/revisions`, dataType: 'json'})
                .success(response => {
                    this.loading = false;
                    this.revisions = response.revisions;
                })
                .error(response => {
                    this.loading = false;
                });
        }
    },

    events: {
        reload_revisions() {
            this.loadRevisions();
        }
    }
}