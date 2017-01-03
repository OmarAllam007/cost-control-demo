export default{
    // template: document.getElementById(''),
    props: ['project'],
    data(){
        return {
            resources: [],
            resource: '',
            resource_type: '',
        }
    },
    methods: {
        loadResources(){
            $.ajax({
                url: '/api/resources/resources/' + this.project, dataType: 'json',
            }).success(response=> {
                this.resources = response;
            }).error(response=> {
                console.log('error');
            });
        }
    },
    computed: {
        filtered_resources(){
            return this.resources.filter((item)=> {
                if (this.resource) {
                    return item.name.toLowerCase().indexOf(this.resource.toLowerCase()) >= 0
                        || item.resource_code.toLowerCase().indexOf(this.resource.toLowerCase()) >= 0;
                }
                return true;
            }).filter((item)=> {
                if (this.resource_type) {
                    return (item.resource_type_id == this.resource_type);
                }
                return true;
            });
        }
    },
    watch: {},
    ready(){
        this.loadResources();
    }

}