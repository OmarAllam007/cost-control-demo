export default {
    props: ['project'],

    template: document.getElementById('BOQTemplate').innerHTML,

    data() {
        return {
            boq: {},
            loading: false,
            wbs_id: 0,
            wiping: false,
            filter: '',
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

        filtered_boq() {
            const boqs = this.boq.filter(boq => {
                if (!this.filter || this.filter == '') {
                    return true;
                }
                const term = this.filter.toLowerCase();
                return qty.description.toLowerCase().indexOf(term) >= 0 ||
                    qty.cost_account.toLowerCase().indexOf(term) >= 0;
            });


            return quantities.slice(this.first, this.last);
        },

        destroy (item_id) {
            this.loading = true;
            $.ajax({
                url: '/boq/' + item_id,
                data: {_token: document.querySelector('meta[name=csrf-token]').content, _method: 'delete'},
                method: 'post'
            }).success(response => {
                if (response.ok) {
                    this.loadBoq();
                }
            }).error(() => {
            });
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
                    type: response.ok ? 'info' : 'danger'
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
                    type: 'danger'
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
