export default {
    template: document.getElementById('QtySurveyTemplate').innerHTML,

    data() {
        return {
            quantities: [],
            loading: false,
            wbs_id: 0,
            wiping: false
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
        },

        wipeAll() {
            this.wiping = true;
            $.ajax({
                url: '/survey/wipe/' + this.project,
                data: {
                    _token: $('meta[name=csrf-token]').attr('content'),
                    _method: 'delete', wipe: true
                },
                method: 'post', dataType: 'json'
            }).success((response) => {
                this.wiping = false;
                this.$dispatch('request_alert', {
                    message: response.message,
                    type: response.ok ? 'info' : 'error'
                });
                if (response.ok) {
                    this.quantities = [];
                    this.selected = 0;
                }
                $('#WipeQSModal').modal('hide');
            }).error((response) => {
                this.wiping = false;
                this.$dispatch('request_alert', {
                    message: response.message,
                    type: 'error'
                });
                $('#WipeQSModal').modal('hide');
            });
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