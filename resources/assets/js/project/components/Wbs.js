import WbsItem from './WbsItem';

export default {
    template: document.getElementById('WbsTemplate').innerHTML,

    props: ['wbs_levels', 'project'],

    data () {
        if (!this.wbs_levels) {
            this.wbs_levels = [];
        }

        return { loading: false };
    },

    ready() {
        var wbsTree = $('#wbs-tree').on('click', '.wbs-icon', function (e) {
            e.preventDefault();
            $(this).find('.fa').toggleClass('fa-plus-square-o fa-minus-square-o');
        }).on('click', '.wbs-link', function (e) {
            e.preventDefault();
            wbsTree.find('.wbs-item').removeClass('active');
            $(this).parent('.wbs-item').addClass('active');
        });
    },

    methods: {
        loadWbs() {
            this.loading = true;

            $.ajax({
                url: '/api/wbs/' + this.project,
                dataType: 'json', cache: false
            }).success(response => {
                this.loading = false;
                this.wbs_levels = response;
            }).error(() => {
                this.loading = false;
            })
        }
    },

    components: { WbsItem },

    events: {
        reload_wbs() {
            this.loadWbs();
        }
    }
}