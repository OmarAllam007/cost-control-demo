import _ from 'lodash';

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
        term: _.debounce(function (term) {
            const root = $('#ResourcesModal');
            root.find('.radio').removeClass('hidden');
            root.find('.collapse').removeClass('in');
            root.find('li').removeClass('hidden');
            if (term !== '') {
                const lower = term.toLowerCase();
                root.find('.radio').filter((index, el) => {
                    return $(el).find('.resource-name').text().toLowerCase().indexOf(lower) < 0;
                }).addClass('hidden');
            }

            // console.log(root.find('.tree--item').parent('li'));

            root.find('li').not('.radio').filter((idx, element) => {
                return $(element).find('.radio.hidden').length === $(element).find('.radio').length;
            }).addClass('hidden');
            // root.find('.tree--item').filter(element => {
            //     let $parent = $(element).parent('li');
            //     return $parent.find('.radio').not('.hidden').length
            // }).parent('li').show();
        }, 500)
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
