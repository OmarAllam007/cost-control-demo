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

    created() {
        this.loadResources();
    },

    computed: {
        filtered_resources() {
            return this.resources.filter(res => {
                if (this.filters.type) {
                    return res.type_id = this.filters.type;
                }

                return true;
            }).filter(res => {
                if (this.filters.name) {
                    return res.name.toLowerCase().indexOf(this.filters.name.toLowerCase()) >= 0
                        || res.code.toLowerCase().indexOf(this.filters.name.toLowerCase()) >=0;
                }

                return true;
            })
        }
    },

    methods: {
        loadResources() {
            $.ajax({
                url: `/api/cost/resources/${this.project}`,
                dataType: 'json'
            }).success(resources => this.resources = resources);
        }
    }
}