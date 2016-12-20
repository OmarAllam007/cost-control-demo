import WbsItem from './WbsItem';

export default {
    props: ['wbs_levels', 'project'],

    data () {
        if (!this.wbs_levels) {
            this.wbs_levels = [];
        }

        return { loading: false, wiping: false, filter: '' };
    },

    ready() {
        let wbsTree = $('#wbs-tree').on('click', '.wbs-icon', function (e) {
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
        },

        wipeAll () {
            this.wiping = true;
            $.ajax({
                url: '/wbs-level/wipe/' + this.project,
                data: {
                    _token: $('meta[name=csrf-token]').attr('content'),
                    _method: 'delete', wipe: true
                },
                method: 'post', dataType: 'json'
            }).success((response) => {
                this.wiping = false;
                this.$dispatch('request_alert', {
                    message: response.message,
                    type: response.ok ? 'info' : 'error'
                });
                if (response.ok) {
                    this.wbs_levels = [];
                    this.selected = 0;
                }
                $('#WipeWBSModal').modal('hide');
            }).error((response) => {
                this.wiping = false;
                this.$dispatch('request_alert', {
                    message: response.message,
                    type: 'error'
                });
                $('#WipeWBSModal').modal('hide');
            });
        },

        applyFilter(level) {
            const filter = this.filter.toLowerCase();
            let filtered = [];

            const valid = level.code.toLowerCase().indexOf(filter) >= 0 || level.name.toLowerCase().indexOf(filter) >= 0;
            if (valid) {
                filtered.push(level);
            }

            level.children.forEach((child) => {
                filtered = $.merge(filtered, this.applyFilter(child));
            });

            return filtered;
        }
    },

    computed: {
        filtered_wbs_levels() {
            if (!this.filter) {
                return this.wbs_levels;
            }

            let filtered = [];
            this.wbs_levels.forEach((level) => {
                filtered = $.merge(filtered, this.applyFilter(level));
            });

            return filtered;
        }
    },

    components: { WbsItem },

    events: {
        reload_wbs() {
            this.loadWbs();
        }
    }
}