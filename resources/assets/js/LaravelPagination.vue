<template>
    <ul class="pagination">
        <li v-if="hasPrevious">
            <a href="#" @click.prevent="page = 1"><i class="fa fa-backward"></i></a>
        </li>
        <li v-if="hasPrevious">
            <a href="#" @click.prevent="page--">&laquo;</a>
        </li>
        <li v-for="p in range" :class="{active: page == p}">
            <a href="#" @click.prevent="page = p" v-text="p"></a>
        </li>
        <li v-if="hasNext">
            <a href="#" @click.prevent="page++">&raquo;</a>
        </li>
        <li v-if="hasNext">
            <a href="#" @click.prevent="page = totalPages"><i class="fa fa-forward"></i></a>
        </li>
    </ul>
</template>
<script>
    export default {
        props: ['url', 'property'],

        name: 'LaravelPagination',

        data() {
            return {page: 1, totalPages: 1, cardinality: 8}
        },

        computed: {
            hasPrevious() { return this.page > 1; },
            hasNext() { return this.page < this.totalPages; },
            range() {
                if (this.totalPages <= this.cardinality) {
                    return this.createRange(1, this.totalPages);
                }

                const middle = this.cardinality / 2;
                if (this.page <= middle) {
                    return this.createRange(1, this.cardinality);
                }

                let end = Math.min(this.page + middle, this.totalPages);
                let start = 1;
                if (end > this.cardinality) {
                    start = end-this.cardinality + 1;
                }

                return this.createRange(start, end);
            }
        },

        created() {
            this.loadData();
        },

        watch: {
            url() {
                this.loadData();
            },

            page() {
                this.loadData(false);
            }
        },

        methods: {
            createRange(start, end) {
                if (start === end) {
                    return [];
                }

                const range = [];
                for (let i = start; i <= end; i++) {
                    range.push(i);
                }
                return range;
            },

            loadData(resetPage = true) {
                let url = this.url;
                let separator = '?';
                if (url.indexOf('?') >= 0) {
                    separator = '&';
                }

                if (resetPage) {
                    this.page = 1;
                }

                url += `${separator}page=${this.page}`;

                this.$dispatch('loadingStart');

                $.ajax({
                    url, dataType: 'json'
                }).done(response => {
                    this.$parent[this.property] = response.data;
                    this.totalPages = response.last_page;

                    this.$dispatch('loadingDone');
                });
            }
        }
    }
</script>

<style scoped>
    .pagination {
        margin-top: 0;
        margin-bottom: 0;
    }
</style>