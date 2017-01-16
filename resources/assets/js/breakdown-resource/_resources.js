export default {
    template: document.getElementById('ResourcesTemplate').innerHTML,

    props: ['resource'],

    data: function () {
        let data = {
            resources: [],
            resource_id: '',
            loading: false,
            term: ''
        };

        if (this.resource) {
            data.resource_id = this.resource.id;
            this.$dispatch('resource-changed', this.resource);
        }

        return data;
    },

    mounted() {
        this.$dispatch('resource-changed', this.resource);
    },

    watch: {
        term: function (term) {
            const root = $('#ResourcesModal');
            if (term == '') {
                root.find('.radio').removeClass('hidden');
                root.find('.collapse').removeClass('in');
            } else {
                const lower = term.toLowerCase();
                root.find('.resource-name').each((index, element) => {
                    let $el = $(element);
                    if ($el.html().toLowerCase().indexOf(lower) != -1) {
                        $el.parents('.radio').removeClass('hidden');
                    } else {
                        $el.parents('.radio').addClass('hidden');
                    }
                });

            }
            root.find('.tree--item').each((index, element) => {
                let $parent = $(element).parent('li');
                if ($parent.find('.radio').not('.hidden').length) {
                    $parent.show();
                } else {
                    $parent.hide();
                }
            });
        }
    },

    methods: {
        setResource: function (resource) {
            this.resource = resource;
            this.$dispatch('resource-changed', resource);
        }
    },

    events: {
        resetResource() {
            this.resource_id = '';
            this.setResource({});
        }
    }
};
