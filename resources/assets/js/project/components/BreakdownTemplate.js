export default{
    // template: document.getElementById(''),
    props: ['project'],
    data(){
        return {
            templates: [],
            template: '',
            // resource_type:'',
        }
    },
    methods: {
        loadTemplates(){
            $.ajax({
                url: '/api/breakdown-template/template/' + this.project, dataType: 'json'
            }).success(response=> {
                this.templates = response;
            }).error(response=> {
                console.log('error');
            });
        }
    },
    computed: {
        filterd_templates(){
            return this.templates.filter((item)=> {
                if (this.template) {
                    return item.name.toLowerCase().indexOf(this.template.toLowerCase()) >= 0
                        || item.code.toLowerCase().indexOf(this.template.toLowerCase()) >= 0;
                }
                return true;
            })
        }
    },
    watch: {},
    ready(){
        this.loadTemplates();
    }

}