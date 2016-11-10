import WbsItem from './WbsItem';

export default {
    template: document.getElementById('WbsTemplate').innerHTML,

    props: ['wbs_levels'],

    data () {
        if (!this.wbs_levels) {
            this.wbs_levels = [];
        }

        return {};
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

        }
    },

    components: {
        WbsItem
    },

    events: {
        reload_wbs() {
            this.loadWbs();
        }
    }
}