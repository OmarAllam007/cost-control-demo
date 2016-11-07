export default {
    template: document.getElementById('BreakdownTemplate').innerHTML,

    data() {
        return {
            breakdowns: [],
            loading: false
        }
    },

    methods: {

    },

    watch: {

    },

    events: {
        wbs_changed(params) {
            console.log(params);
            this.loading = true;
            $.ajax({
                url: '/api/wbs/breakdowns/' + params.selection, dataType: 'json',
                cache: true
            }).success(response => {
                this.loading = false;
                this.breakdowns = response;
            }).error(() => {
                this.loading = false;
                this.breakdowns = [];
            });
        }
    }
}
