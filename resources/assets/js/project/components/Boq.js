export default {
    template: document.getElementById('BOQTemplate').innerHTML,

    data() {
        return {
            boq: {},
            loading: false,
            wbs_id: 0
        };
    },

    computed: {
        empty_boq() {
            return Object.keys(this.boq).length == 0;
        }
    },

    methods: {
        loadBoq() {
            if (this.wbs_id) {
                this.loading = true;
                $.ajax({
                    url: '/api/wbs/boq/' + this.wbs_id, dataType: 'json',
                    cache: true
                }).success(response => {
                    this.loading = false;
                    if ($.isPlainObject(response)) {
                        this.boq = response;
                    } else {
                        this.boq = {};
                    }
                }).error(() => {
                    this.loading = false;
                    this.boq = {};
                });
            }
        },

        destroy (item_id) {
            this.loading = true;
            $.ajax({
                url: '/boq/' + item_id,
                data: {_token: document.querySelector('meta[name=csrf-token]').content,_method: 'delete'},
                method: 'post'
            }).success(response => {
                if (response.ok) {
                    this.loadBoq();
                }
            }).error(() => {});
        },

        wipeAll() {
            this.wiping = true;
            $.ajax({
                url: '/boq/wipe/' + this.project,
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
                    this.boq = [];
                    this.selected = 0;
                }
                $('#WipeBoqModal').modal('hide');
            }).error((response) => {
                this.wiping = false;
                this.$dispatch('request_alert', {
                    message: response.message,
                    type: 'error'
                });
                $('#WipeBoqModal').modal('hide');
            });
        }
    },

    events: {
        wbs_changed(params) {
            this.wbs_id = params.selection;
            this.loadBoq();
        },

        reload_boq() {
            this.loadBoq();
        }
    }
};
