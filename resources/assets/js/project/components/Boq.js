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
}
