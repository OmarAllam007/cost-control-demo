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
                console.log(response)
                this.productivityArray = $.map(response, function (value, index) {
                    return [value]

                })
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
        breakline: function(value){
            console.log(value);
            // return value ;
        }
    },

    watch: {},
    ready(){
        this.loadProductivity();
    }

}