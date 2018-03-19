import Pagination from './pagination';

export default{
    // template: document.getElementById(''),
    props: ['project'],
    data(){
        return {
            templates: [],
            template: '',
            count: 0,
            first: 1,
            last: 100
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
            const templates = this.templates.filter((item)=> {
                if (this.template) {
                    return item.name.toLowerCase().indexOf(this.template.toLowerCase()) >= 0
                        || item.code.toLowerCase().indexOf(this.template.toLowerCase()) >= 0;
                }
                return true;
            });

            this.count = templates.length;
            return templates.slice(this.first, this.last);
        }
    },
    events: {
        pageChanged(params) {
            this.first = params.first;
            this.last = params.last;
        }
    },

    created(){
        this.loadTemplates();
    },

    components: {
        Pagination
    }
}