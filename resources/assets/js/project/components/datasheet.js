import DeleteActivityModal from './delete-activity-modal';
import DeleteResourceModal from './delete-resource-modal';
import BreakdownResource from './breakdown-resource';
import Pagination from './server-pagination';
import _ from 'lodash';

export default {

    props: ['project'],

    data() {
        let perspective = window.localStorage.cost_perspective;

        return {
            breakdowns: [],
            loading: false,
            wbs_id: 0, activity: '', resource_type: '', resource: '', cost_account: '',
            perspective, count: 0, first: 0, last: 99,
            rollup: [], rollup_activity: false, rollup_wbs: false
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
            // if (this.wbs_id) {
            //     this.loading = true;
            //     $.ajax({
            //         dataType: 'json', cache
            //     }).success(response => {
            //         this.loading = false;
            //         this.breakdowns = response;
            //     }).error(() => {
            //         this.loading = false;
            //         this.breakdowns = [];
            //     });
            // }
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
                data: { _method: 'delete', _token: document.querySelector('[name=csrf-token]').content }
            }).success(response => {
                this.loading = false;
                $('#DeleteWbsDataModal').modal('hide');
                this.$broadcast('reloadPage');
                this.$dispatch('request_alert', {
                    type: response.ok? 'info' : 'error', message: response.message
                });
            }).error(() => {
                this.loading = false;
                $('#DeleteWbsDataModal').modal('hide');
                this.$dispatch('request_alert', {
                    type: 'error', message: 'Could not delete current data for this WBS'
                });

            });
        },

        rollup() {

        }
    },

    events: {
        wbs_changed(params) {
            if (this.wbs_id != params.selection) {
                this.loading = true;
                this.wbs_id = params.selection;
                this.rollup = [];
                this.rollup_wbs = false;
                this.rollup_activity = false;
            }
        },

        changingPage() {
            this.loading = true;
        },

        pageChanged(data) {
            console.log(data);
            this.breakdowns = _.groupBy(data, 'activity');
            // this.breakdowns = data;
            this.loading = false;
        },

        reload_breakdowns() {
            this.loadBreakdowns();
        },

        add_to_rollup(resource) {
            if (!this.rollup.length) {
                this.rollup_wbs = resource.wbs_id;
                this.rollup_activity = resource.activity_id;
            }

            if (this.rollup.indexOf(resource.breakdown_resource_id) < 0) {
                this.rollup.push(resource.breakdown_resource_id);
            }
        },

        remove_from_rollup(resource) {
            const index = this.rollup.indexOf(resource.breakdown_resource_id);
            this.rollup.splice(index, 1);

            if (this.rollup.length === 0) {
                this.rollup_wbs = false;
                this.rollup_activity = false;
            }
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