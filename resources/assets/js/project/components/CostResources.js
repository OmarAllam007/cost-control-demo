export default {
    props: ['project'],

    data() {
        return {
            resources: [],
            filters: {
                type: '', name: ''
            }
        }
    },

    mounted() {
        loadResources();
    },

    computed: {
        filtered_resources() {
            return this.resources.filter(res => {
                if (this.filters.type) {
                    return res.type_id = this.filters.type;
                }

                return true;
            }).filter(res => {
                return item.name.toLowerCase().indexOf(this.resource.toLowerCase()) >= 0
                    || item.resource_code.toLowerCase().indexOf(this.resource.toLowerCase()) >=0;
            })
        }
    },

    methods: {
        loadResources() {
            $.ajax({
                url: '/api/cost/resources' + this.project,
                dataType: 'json'
            }).success(resources => this.resources = resources);
        }
    }
}