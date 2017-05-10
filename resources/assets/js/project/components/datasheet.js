import DeleteActivityModal from './delete-activity-modal';
import DeleteResourceModal from './delete-resource-modal';
import Pagination from './server-pagination';

export default {

    props: ['project'],

    data() {
        let perspective = window.localStorage.cost_perspective;

        return {
            breakdowns: [],
            loading: false,
            wbs_id: 0, activity: '', resource_type: '', resource: '', cost_account: '',
            perspective, count: 0, first: 0, last: 99
        };
    },

    //<editor-fold defaultstate="collapsed" desc="Computed properties">
    computed: {
        url() {
            let url = '/api/cost/breakdowns/' + this.wbs_id + (this.perspective ? ('?perspective=' + this.perspective) : '')
            const filters = ['activity', 'resource_type', 'resource', 'cost_account'];
            filters.forEach(filter => {
                if (this[filter]) {
                    url += '&' + filter + '=' + this[filter];
                }
            });
            return url;
        }
    },
    //</editor-fold>

    methods: {
        loadBreakdowns(cache = true) {
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
            this.loading = false;
        }
    },

    watch: {
        perspective(view) {
            this.loading = true;
            window.localStorage.perspective = view;
        }
    },

    components: {
        DeleteActivityModal, DeleteResourceModal, Pagination
    }
}