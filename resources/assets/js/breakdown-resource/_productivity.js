export default {
    template: document.getElementById('ProductivityTemplate').innerHTML,

    data: function () {
        return {
            productivities: [],
            loading: false,
            term: '',
            selected: productivity,
            labors_count: 0,
            labors_cache: {}
        };
    },

    ready: function () {
        this.load();
    },

    watch: {
        term: function () {
            this.load();
        },

        selected: function() {
            $.ajax({
                url: '/api/productivity/labours-count/' + this.selected.id,
                dataType: 'json'
            }).success(response => {
                this.$dispatch('set_labor_count', response.count);
            });
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
};
