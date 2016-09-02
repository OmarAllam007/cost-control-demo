var Productivity = Vue.extend({
    template: document.getElementById('ProductivityTemplate').innerHTML,

    data: function () {
        return {
            productivities: [],
            loading: false,
            term: '',
            selected: productivity
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
        setProductivity: function (resource) {
            this.selected = resource;
            this.$dispatch('productivity-changed', resource);
        },

        load: function () {
            var self = this;
            if (!this.loading) {
                self.loading = true;
                $.ajax({
                    url: '/api/productivity',
                    type: 'get', data: {term: self.term}, dataType: 'json', cache: false
                }).success(function (response) {
                    self.productivities = response;
                    self.loading = false;
                });
            }
        }
    }
});
