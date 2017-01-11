export default {
    props: ['total', 'perPage'],

    template: `
<div class="text-center">
    <ul v-if="numPages > 1" class="pagination pagination-sm">
        <li v-if="showPrev"><a href="#" @click.prevent="currentPage = 1"><i class="fa fa-chevron-left"></i></a></li>
        <li v-if="showPrev"><a href="#" @click.prevent="currentPage -= 1">&laquo;</a></li>
        <li v-for="page in pages" :class="{active: currentPage == page}"><a href="#" v-text="page" @click.prevent="currentPage = page"></a></li>
        <li v-if="showNext"><a href="#" @click.prevent="currentPage += 1">&raquo;</a></li>
        <li  v-if="showNext"><a href="#" @click.prevent="currentPage = numPages"><i class="fa fa-chevron-right"></i></a></li>
    </ul>
</div>
    `,

    data() {
        if (!this.perPage) {
            this.perPage = 100;
        }

        const numPages = Math.ceil(this.total/this.perPage);

        return {currentPage: 1, cardinal: 10, numPages}
    },

    computed: {
        showNext() {
            return this.currentPage < this.lastPage;
        },

        showPrev() {
            return this.currentPage > 1;
        },

        firstItem() {
            return this.perPage * (this.currentPage - 1);
        },

        lastItem() {
            let lastIndex = (this.perPage * this.currentPage);
            if (lastIndex > this.total) {
                return this.total;
            }
            return lastIndex
        },

        firstPage() {
            if (this.numPages <= this.cardinal) {
                return 1;
            }

            let middle = Math.floor(this.cardinal / 2);

            let page = this.currentPage - middle;
            if (page <= 0) {
                return 1;
            }

            return page;
        },

        lastPage() {
            if (this.numPages <= this.cardinal) {
                return this.numPages;
            }

            let middle = Math.floor(this.cardinal / 2);

            let page = this.currentPage + middle;
            if (page > this.numPages) {
                return this.numPages;
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
        }
    },

    methods: {
        changePage(page) {
            this.$dispatch('pageChanged', {
                page,
                first: this.firstItem,
                last: this.lastItem
            });
        }
    },

    watch: {
        currentPage(page) {
            this.changePage(page);
        },

        total() {
            this.numPages = Math.ceil(this.total/this.perPage);
            this.currentPage = 1;
            this.changePage(1);
        }
    }
};