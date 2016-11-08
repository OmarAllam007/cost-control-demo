export default {
    template: document.getElementById('BOQTemplate').innerHTML,

    data() {
        return {
            boq: {},
            loading: false
        };
    },

    computed: {
        empty_boq() {
            return Object.keys(this.boq).length == 0;
        }
    },

    methods: {

    },

    watch: {

    },

    events: {
        wbs_changed(params) {
            this.loading = true;
            $.ajax({
                url: '/api/wbs/boq/' + params.selection, dataType: 'json',
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
    }
}
