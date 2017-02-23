export default {
    data : function() {
        return {breakdown: {}, loading: false};
    },

    methods: {
        delete_activity() {
            this.loading = true;
            console.log(this.breakdown);
            $.ajax({
                url: '/api/cost/delete-activity/' + this.breakdown.breakdown_id,
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
        show_delete_activity(breakdown) {
            this.breakdown = breakdown;
            $(this.$el).modal();
        }
    }
}