export default{
    // template: document.getElementById(''),
    props: ['project'],
    data(){
        return {
            resources: [],
            code: '',
            resource:'',
            resource_type:'',
        }
    },
    methods: {
        loadResources(){
            $.ajax({
                url: '/api/resources/resources/' + this.project, dataType: 'json'
            }).success(response=> {
                this.resources = response;
            }).error(response=> {
                console.log('error');
            });
        }
    },
    computed: {
        filtered_resources(){
            return this.resources.filter((item)=>{
                if(this.resource){
                    return item.name.toLowerCase().indexOf(this.resource.toLowerCase()) >= 0
                        || item.resource_code.toLowerCase().indexOf(this.code.toLowerCase()) >=0;
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