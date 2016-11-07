export default {
    template: document.getElementById('QtySurveyTemplate').innerHTML,

    data() {
        return {
            quantities: [],
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
                url: '/api/wbs/qty-survey/' + params.selection, dataType: 'json',
                cache: true
            }).success(response => {
                this.loading = false;
                this.quantities = response;
            }).error(() => {
                this.loading = false;
                this.quantities = [];
            });
        }
    }
}