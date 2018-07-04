export default {
    data : function() {
        return {breakdown: {}, loading: false};
    },

    created () {
        this.$parent.$on('delete_activity', breakdown => {
            this.breakdown = breakdown;
            $(this.$el).modal();
        });
    },

    methods: {
        delete_activity() {
            this.loading = true;
            $.ajax({
                url: '/api/cost/delete-activity/' + this.breakdown.wbs_id + '?code=' + this.breakdown.code,
                data: {_token: document.querySelector('[name=csrf-token]').content},
                method: 'DELETE', dataType: 'json'
            }).success(() => {
                this.$emit('reload_breakdowns');
                $(this.$el).modal('hide');
                this.loading = false;
            }).error(() => {
                this.loading = false;
                $(this.$el).modal('hide');
            });
        }
    }
}