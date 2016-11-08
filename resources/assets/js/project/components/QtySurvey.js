export default {
    template: document.getElementById('QtySurveyTemplate').innerHTML,

    data() {
        return {
            quantities: [],
            loading: false,
            wbs_id: 0
        };
    },

    methods: {
        loadQuantities() {
            this.loading = true;
            $.ajax({
                url: '/api/wbs/qty-survey/' + this.wbs_id, dataType: 'json',
                cache: true
            }).success(response => {
                this.loading = false;
                this.quantities = response;
            }).error(() => {
                this.loading = false;
                this.quantities = [];
            });
        },

        destroy(qty_id) {
            this.loading = true;
            $.ajax({
                url: '/survey/' + qty_id,
                data: {_token: document.querySelector('meta[name=csrf-token]').content,_method: 'delete'},
                method: 'post'
            }).success(response => {
                if (response.ok) {
                    this.loadQuantities();
                    this.$dispatch('request_alert', {
                        type: 'info',
                        message: response.message
                    });
                }
            }).error(() => {});
        }
    },

    watch: {

    },

    events: {
        wbs_changed(params) {
            this.wbs_id = params.selection;
            this.loadQuantities();
        },

        reload_quantities() {
            this.loadQuantities();
        }
    }
}