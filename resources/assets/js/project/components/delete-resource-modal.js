export default {
    data : function() {
        return {resource: {}, loading: false};
    },

    created() {
        this.$parent.$on('delete_resource', resource => {
            this.resource = resource;
            $(this.$el).modal();
        });
    },

    methods: {
        delete_resource() {
            this.loading = true;

            $.ajax({
                url: '/api/cost/delete-resource/' + this.resource.breakdown_resource_id,
                data: {_token: document.querySelector('[name=csrf-token]').content},
                method: 'DELETE', dataType: 'json'
            }).success(() => {
                this.$emit('reload_breakdowns');
                $(this.$el).modal('hide');
                this.loading = false;
            }).error(() => {
                $(this.$el).modal('hide');
                this.loading = false;
            });
        }
    }
}