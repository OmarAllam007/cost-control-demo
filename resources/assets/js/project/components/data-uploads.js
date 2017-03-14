const DataUploads = {
    props: ['project_id'],

    data() {
        return {batches: []};
    },

    created() {
        this.loadBreakdowns();
    },

    methods: {
        loadBreakdowns() {
            $.ajax({
                url: '/api/cost/batches/' + this.project_id, dataType: 'json'
            }).success(response => {
                if (response.ok) {
                    this.batches = response.batches;
                }
            }).error(() => {
                this.batches = [];
            });
        }
    },

    events: {
        reload_data_uploads() {
            this.loadBreakdowns();
        }
    }
};

export { DataUploads };