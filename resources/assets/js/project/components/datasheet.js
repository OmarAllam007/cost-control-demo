import DeleteActivityModal from './delete-activity-modal';
import DeleteResourceModal from './delete-resource-modal';
import BreakdownResource from './breakdown-resource';
import Pagination from './server-pagination';

export default {

    props: ['project'],

    data() {
        let perspective = window.localStorage.cost_perspective;

        return {
            breakdowns: [], loading: false,
            wbs_id: 0, activity: '', resource_type: '', resource: '', cost_account: '',
            perspective, count: 0, first: 0, last: 99
        };
    },

    //<editor-fold defaultstate="collapsed" desc="Computed properties">
    computed: {
        url() {
            let url = '/api/cost/breakdowns/' + this.wbs_id + '?' + (this.perspective ? `perspective=${this.perspective}&` : '')
            const urlTokens = [];
            const filters = ['activity', 'resource_type', 'resource', 'cost_account'];
            filters.forEach(filter => {
                if (this[filter]) {
                    urlTokens.push(`${filter}=${this[filter]}`);
                }
            });
            return url + urlTokens.join('&');
        },

        show_breakdowns() {
            return Object.keys(this.breakdowns).length > 0;
        }
    },
    //</editor-fold>

    methods: {
        loadBreakdowns(cache = true) {
            this.$broadcast('reloadPage');

        },

        deleteResource(resource) {
            this.$broadcast('show_delete_resource', resource);
        },

        deleteActivity(resource) {
            this.$broadcast('show_delete_activity', resource);
        },

        deleteWbsCurrent() {
            this.loading = true;
            $.ajax({
                url: '/api/cost/delete-wbs/' + this.wbs_id, method: 'delete', dataType: 'json',
                data: {_method: 'delete', _token: document.querySelector('[name=csrf-token]').content}
            }).success(response => {
                this.loading = false;
                $('#DeleteWbsDataModal').modal('hide');
                this.$broadcast('reloadPage');
                this.$dispatch('request_alert', {
                    type: response.ok ? 'info' : 'error', message: response.message
                });
            }).error(() => {
                this.loading = false;
                $('#DeleteWbsDataModal').modal('hide');
                this.$dispatch('request_alert', {
                    type: 'error', message: 'Could not delete current data for this WBS'
                });

            });
        },

        sumResourcesOnCostAccount() {
            const _token = document.querySelector('meta[name=csrf-token]').content;
            const url = `/api/rollup/summarize/${this.wbs_id}/cost-account`

            this.loading = true;
            $.ajax({
                url, method: 'post', data: {_token}, dataType: 'json',
            }).then(() => {
                this.loadBreakdowns();
                this.loading = false;
            }, () => {
                this.loading = false;
            });
        },

        sumResourcesOnActivity() {
            const _token = document.querySelector('meta[name=csrf-token]').content;
            const url = `/api/rollup/summarize/${this.wbs_id}/activity`

            this.loading = true;
            $.ajax({
                url, method: 'post', data: {_token}, dataType: 'json',
            }).then(() => {
                this.loadBreakdowns();
                this.loading = false;
            }, () => {
                this.loading = false;
            });
        },

        doRollup(activity_code) {
            const _token = document.querySelector('meta[name=csrf-token]').content;

            this.loading = true;
            $.ajax({
                url: `/project/${this.project}/rollup-activity`,
                data: { codes: [activity_code], _token },
                dataType: 'json',
                method: 'put'
            }).then(response => {
                if (response.ok) {
                    this.loadBreakdowns();
                }

                this.$dispatch('request_alert', {
                    type: response.ok ? 'info' : 'error', message: response.message
                });

                this.loading = false;
            }, () => {
                this.loading = false;
            })
        },

        slug(str) {
            return str.trim().replace(/[\s\W]+/g, '-').toLowerCase();
        }
    },

    events: {
        wbs_changed(params) {
            if (this.wbs_id != params.selection) {
                this.loading = true;
                this.wbs_id = params.selection;
            }
        },

        changingPage() {
            this.loading = true;
        },

        pageChanged(data) {
            this.breakdowns = data;

            // this.breakdowns = data;
            this.loading = false;
        },

        reload_breakdowns() {
            this.loadBreakdowns();
        }
    },

    watch: {
        perspective(view) {
            this.loading = true;
            window.localStorage.perspective = view;
        }
    },

    components: {
        BreakdownResource, DeleteActivityModal, DeleteResourceModal, Pagination
    },

    filters: {
        number_format(val) {
            return parseFloat(val.toFixed(2)).toLocaleString();
        }
    }
}