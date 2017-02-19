export default {
    data() {
        return { loading: false, activities: {}, wbs_id: 0 };
    },

    methods: {
        loadActivities() {
            if (this.wbs_id) {
                this.loading = true;
                $.ajax({
                    url: '/api/cost/activity-log/' + this.wbs_id, dataType: 'json', cache: true
                }).success(response => {
                    this.loading = false;
                    this.activities = response;
                }).error(() => {
                    this.loading = false;
                    this.activities = {};
                });
            }
        }
    },

    events: {
        wbs_changed(params) {
            this.wbs_id = params.selection;
            this.loadActivities();
        },
    },

    filters: {
        isEmptyObject(obj) {
            return Object.keys(obj).length !== 0;
        }
    }
};