export default {
    template: document.getElementById('#BOQTemplate').innerHTML,

    data() {
        return {
            items: {},
            loading: false
        };
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
                this.boq = response;
            }).error(() => {
                this.loading = false;
                this.boq = {};
            });
        }
    }
}
