var Resources = Vue.extend({
    template: document.getElementById('ResourcesTemplate').innerHTML,

    data: function () {
        return {
            resources: [],
            loading: false,
            term: '',
            selected: resource
        };
    },

    ready: function () {
        this.load();
    },

    watch: {
        term: function () {
            this.load();
        }
    },

    methods: {
        setResource: function(resource) {
            this.selected = resource;
            this.$dispatch('resource-changed', resource);
        },

        load: function () {
            var self = this;
            if (!this.loading) {
                self.loading = true;
                $.ajax({
                    url: '/api/resources',
                    type: 'get', data: {term: self.term}, dataType: 'json', cache: false
                }).success(function (response) {
                    self.resources = response;
                    self.loading = false;
                });
            }
        }
    }
});