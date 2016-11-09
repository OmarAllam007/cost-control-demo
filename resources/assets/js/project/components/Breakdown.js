export default {
    template: document.getElementById('BreakdownTemplate').innerHTML,

    data() {
        return {
            breakdowns: [],
            loading: false,
            wbs_id: 0
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
