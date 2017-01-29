import Pagination from './pagination';

export default {
    template: document.getElementById('BreakdownTemplate').innerHTML,

    props: ['project'],

    data() {
        return {
            breakdowns: [],
            loading: false,
            wbs_id: 0, activity: '', resource_type: '', resource: '', cost_account: '',
            wiping: false,
            copied_wbs_id: 0,
            count: 0,
            first: 0,
            last: 99
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
                data: {_token: document.querySelector('meta[name=csrf-token]').content, _method: 'delete'},
                method: 'post'
            }).success(response => {
                if (response.ok) {
                    this.loadBreakdown();
                }
            }).error(() => {
            });
        },

        copy() {
            this.copied_wbs_id = this.wbs_id;
            alert('Copied');
        },

        paste() {
            if (this.copied_wbs_id && this.wbs_id) {
                this.loading = true;
                $.ajax({
                    url: '/breakdown/copy-wbs/' + this.copied_wbs_id + '/' + this.wbs_id
                }).success(response => {
                    this.loading = false;
                    this.$dispatch('request_alert', {
                        message: 'WBS data has been copied',
                        type: 'success'
                    });
                    this.breakdowns = response.breakdowns;
                }).error(response => {
                    this.loading = false;
                    this.$dispatch('request_alert', {
                        message: 'Failed to paste WBS data',
                        type: 'danger'
                    });
                });
            }
        },

        wipeAll() {
            this.wiping = true;

            $.ajax({
                url: '/breakdown/wipe/' + this.wbs_id,
                data: {
                    _token: $('meta[name=csrf-token]').attr('content'),
                    _method: 'delete', wipe: true,
                    wbs: this.wbs_id,
                },
                method: 'post', dataType: 'json'
            }).success(response => {
                this.wiping = false;
                this.$dispatch('request_alert', {
                    message: response.message,
                    type: response.ok ? 'info' : 'danger'
                });
                if (response.ok) {
                    this.breakdowns = [];
                    this.selected = 0;
                }
                $('#WipeBreakdownModal').modal('hide');
            }).error(response => {
                this.wiping = false;
                this.$dispatch('request_alert', {
                    message: response.message,
                    type: 'danger'
                });
                $('#WipeBreakdownModal').modal('hide');
            });
        }
    },

    computed: {
        filtered_breakdowns() {
            const resources = this.breakdowns.filter((item) => {
                if (this.activity) {
                    return (item.activity_id == this.activity);
                }
                return true;
            }).filter((item) => {
                if (this.resource_type) {
                    return (item.resource_type_id == this.resource_type);
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

            this.count = resources.length;
            return resources.slice(this.first, this.last);
        }
    },

    watch: {},

    events: {
        wbs_changed(params) {
            this.wbs_id = params.selection;
            this.loadBreakdown(this.wbs_id)
        },

        reload_breakdown() {
            this.loadBreakdown();
        },

        pageChanged(params) {
            this.first = params.first;
            this.last = params.last;
        }
    },

    components: { Pagination }
}
