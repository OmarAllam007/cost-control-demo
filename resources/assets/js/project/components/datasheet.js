import DeleteActivityModal from './delete-activity-modal';
import DeleteResourceModal from './delete-resource-modal';
import Pagination from './pagination';

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
    //</editor-fold>

    methods: {
        loadBreakdowns(cache = true) {
            if (this.wbs_id) {
                this.loading = true;
                $.ajax({
                    url: '/api/cost/breakdowns/' + this.wbs_id + (this.perspective ? ('?perspective=' + this.perspective) : ''),
                    dataType: 'json', cache
                }).success(response => {
                    this.loading = false;
                    this.breakdowns = response;
                }).error(() => {
                    this.loading = false;
                    this.breakdowns = [];
                });
            }
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
                this.loadBreakdowns(false);
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
            this.wbs_id = params.selection;
            this.loadBreakdowns();
        },

        reload_breakdowns() {
            this.loadBreakdowns();
        },

        pageChanged(params) {
            this.first = params.first;
            this.last = params.last;
        }
    },

    watch: {
        perspective(view) {
            console.log(view);
            window.localStorage.perspective = view;
            this.loadBreakdowns();
        }
    },

    components: {
        DeleteActivityModal, DeleteResourceModal, Pagination
    }
}