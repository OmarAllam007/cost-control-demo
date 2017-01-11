import DeleteActivityModal from './delete-activity-modal';
import DeleteResourceModal from './delete-resource-modal';

export default {
    template: document.getElementById('BreakdownTemplate').innerHTML,

    props: ['project'],

    data() {
        return {
            breakdowns: [],
            loading: false,
            wbs_id: 0, activity: '', resource_type: '', resource: '', cost_account: ''
        };
    },

    //<editor-fold defaultstate="collapsed" desc="Computed properties">
    computed: {
        filtered_breakdowns() {
            return this.breakdowns.filter((item) => {
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
        }
    },
    //</editor-fold>

    methods: {
        loadBreakdowns() {
            if (this.wbs_id) {
                this.loading = true;
                $.ajax({
                    url: '/api/cost/breakdowns/' + this.wbs_id,
                    dataType: 'json',
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

        deleteResource(resource) {
            this.$broadcast('show_delete_resource', resource);
        },

        deleteActivity(resource) {
            this.$broadcast('show_delete_activity', resource);
        }
    },

    events: {
        wbs_changed(params) {
            this.wbs_id = params.selection;
            this.loadBreakdowns();
        },

        reload_breakdown() {
            this.loadBreakdowns();
        }
    },

    components: {
        DeleteActivityModal, DeleteResourceModal
    }
}