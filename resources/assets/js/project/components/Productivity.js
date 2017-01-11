export default{
    // template: document.getElementById(''),
    props: ['project'],
    data(){
        return {
            productivityArray: [],
            code: '',
        }
    },
    methods: {
        loadProductivity(){
            $.ajax({
                url: '/api/productivities/productivity/' + this.project, dataType: 'json'
            }).success(response=> {
                this.productivityArray = $.map(response, function (value, index) {
                    return [value]
                });
            }).error(response=> {
                console.log('error')
            })
        }
    },
    computed: {
        filterd_productivity(){
            return this.productivityArray.filter((item)=> {
                if (this.code) {
                    if (item.code != null) {
                        return item.code.toLowerCase().indexOf(this.code.toLowerCase()) >= 0;
                    }
                }
                return true;
            })
        }
    },
    filters: {
        nl2br: function(value){
            return value.replace(/\r\n|\n|\r/g, '<br>')
        }
    },

    watch: {},
    ready(){
        this.loadProductivity();
    }

}