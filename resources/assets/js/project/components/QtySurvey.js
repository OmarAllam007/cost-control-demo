import Pagination from './pagination';

export default {
    props: ['project'],

    data() {
        return {
            quantities: [],
            loading: false,
            wbs_id: 0,
            wiping: false,
            filter: '',
            count: 0,
            first: 0,
            last: 100
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
                    type: response.ok ? 'info' : 'danger'
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
                    type: 'danger'
                });
                $('#WipeQSModal').modal('hide');
            });
        }
    },

    computed: {
        filtered_qty() {
            const quantities = this.quantities.filter(qty => {
                if (!this.filter || this.filter == '') {
                    return true;
                }

                const term = this.filter.toLowerCase();
                return qty.description.toLowerCase().indexOf(term) >= 0 ||
                    qty.cost_account.toLowerCase().indexOf(term) >= 0;
            });

            this.count = quantities.length;

            return quantities.slice(this.first, this.last);
        }
    },

    events: {
        wbs_changed(params) {
            this.wbs_id = params.selection;
            this.loadQuantities();
        },

        reload_quantities() {
            this.loadQuantities();
        },

        pageChanged(params) {
            this.first = params.first;
            this.last = params.last;
        }
    },

    components: { Pagination }
}