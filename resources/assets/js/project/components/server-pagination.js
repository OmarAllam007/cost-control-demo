export default {
    props: ['url'],

    template: `
<div class="text-center">
    <ul v-if="pages.length > 1" class="pagination pagination-sm">
        <li v-if="showPrev"><a href="#" @click.prevent="changePage(1)"><i class="fa fa-chevron-left"></i></a></li>
        <li v-if="showPrev"><a href="#" @click.prevent="previous()">&laquo;</a></li>
        <li v-for="page in pages" :class="{active: this.pager.current_page == page}"><a href="#" v-text="page" @click.prevent="changePage(page)"></a></li>
        <li v-if="showNext"><a href="#" @click.prevent="next()">&raquo;</a></li>
        <li  v-if="showNext"><a href="#" @click.prevent="last()"><i class="fa fa-chevron-right"></i></a></li>
    </ul>
</div>
    `,

    data() {
        let pager = {};
        return {cardinal: 10, pager}
    },

    computed: {
        showNext() {
            return this.pager.current_page < this.pager.last_page;
        },

        showPrev() {
            return this.pager.current_page > 1;
        },

        firstItem() {
            return this.pager.per_page * (this.pager.current_page - 1);
        },

        lastItem() {
            let lastIndex = (this.pager.per_page * this.pager.current_page);
            if (lastIndex > this.pager.total) {
                return this.pager.total;
            }
            return lastIndex
        },

        firstPage() {
            if (this.pager.last_page <= this.cardinal) {
                return 1;
            }

            let middle = Math.floor(this.cardinal / 2);

            let page = this.pager.current_page - middle;
            if (page <= 0) {
                return 1;
            }

            return page;
        },

        lastPage() {
            if (this.pager.last_page <= this.cardinal) {
                return this.pager.last_page;
            }

            let middle = Math.floor(this.cardinal / 2);

            let page = this.pager.current_page + middle;
            if (page > this.pager.last_page) {
                return this.pager.last_page;
            } else if (page < this.cardinal) {
                return this.cardinal;
            }

            return page;
        },

        pages() {
            const pages = [];
            for (let i = this.firstPage; i <= this.lastPage; ++i) {
                pages.push(i);
            }

            return pages;
        },
    },

    created() {
        this.changePage(1);
    },

    methods: {
        changePage(page) {
            this.$dispatch('changingPage');

            $.ajax({
                url: this.url, method: 'get', dataType: 'json', data: {page}
            }).success(data => {
                this.pager = data;
                this.$dispatch('pageChanged', this.pager.data);
            });
        },

        previous() {
            this.changePage(this.pager.current_page - 1);
        },

        next() {
            this.changePage(this.pager.current_page + 1);
        },

        last() {
            this.changePage(this.pager.last_page);
        }
    },

    watch: {
        paginator(paginator) {
            this.pager = pagaintor;
        },

        pager() {
            this.pager.last_page = Math.ceil(this.pager.total/this.pager.per_page);
        },

        url(url) {
            this.changePage(1);
        }
    },

    events: {
        reloadPage() {
            this.changingPage(this.pager.current_page);
        }
    }
};