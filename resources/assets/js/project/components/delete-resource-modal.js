export default {
    data : function() {
        return {breakdown: {}, loading: false};
    },

    methods: {
        delete_resource() {
            this.loading = true;

            $.ajax({
                url: '/api/cost/delete-resource/' + this.breakdown.breakdown_resource_id,
                data: {_token: document.querySelector('[name=csrf-token]').content},
                method: 'DELETE', dataType: 'json'
            }).success(response => {
                this.$dispatch('reload', {
                    component: 'breakdowns',
                    alert : {
                        type: response.ok? 'info' : 'danger',
                        message: response.message
                    }
                });

                this.loading = false;
                $(this.$el).modal('hide');
            }).error(response => {
                this.loading = false;
                $(this.$el).modal('hide');
            });


        }
    },

    events: {
        show_delete_resource(breakdown) {
            this.breakdown = breakdown;
            $(this.$el).modal();
        }
    }
}