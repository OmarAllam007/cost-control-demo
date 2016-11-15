export default {
    template: document.getElementById('BreakdownTemplate').innerHTML,

    data() {
        return {
            breakdowns: [],
            loading: false,
            wbs_id: 0, activity: '', resource_type: '', resource: '', cost_account: ''
        }
    },

    methods: {
        loadBreakdown() {
            if (this.wbs_id) {
                this.loading = true;
                $.ajax({
                    url: '/api/wbs/breakdowns/' + this.wbs_id, dataType: 'json',
                    cache: true
                }).success(response => {
                    this.loading = false;
                    this.breakdowns = response;
                }).error(() => {
                    this.loading = false;
                    this.breakdowns = [];
                });
            }
        },

        destroy(breakdown_id) {
            this.loading = true;
            $.ajax({
                url: '/breakdown-resource/' + breakdown_id,
                data: {_token: document.querySelector('meta[name=csrf-token]').content,_method: 'delete'},
                method: 'post'
            }).success(response => {
                if (response.ok) {
                    this.loadBreakdown();
                }
            }).error(() => {});
        }
    },

    computed: {
        filtered_breakdowns() {
            return this.breakdowns.filter((item) => {
                if (this.activity) {
                    return  (item.activity_id == this.activity);
                }
                return true;
            }).filter((item) => {
                if (this.resource_type) {
                    return  (item.resource_type_id == this.resource_type);
                }
                return true;
            }).filter((item) => {
                if (this.cost_account) {
                    return item.cost_account.toLowerCase().indexOf(this.cost_account.toLowerCase()) >= 0;
                }
                return true;
            }).filter((item) => {
                if (this.resource) {
                    return item.resource_name.toLowerCase().indexOf(this.resource.toLowerCase()) >= 0 ||
                        item.resource_code.toLowerCase().indexOf(this.resource.toLowerCase()) >= 0;
                }
                return true;
            });
        }
    },

    watch: {

    },

    events: {
        wbs_changed(params) {
            this.wbs_id = params.selection;
            this.loadBreakdown(this.wbs_id)
        },

        reload_breakdown() {
            this.loadBreakdown();
        }
    }
}
