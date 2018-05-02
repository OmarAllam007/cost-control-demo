<template>
    <section>
        <section class="loading" v-show="loading"><i class="fa fa-spinner fa-spin fa-3x"></i></section>
        <section class="card-group" v-if="activities.length">
            <activity v-for="activity in activities" :initial="activity"></activity>
        </section>
        <div class="alert alert-info" v-else>
            <i class="fa fa-info-circle"></i>
            No Activities Found
        </div>

        <laravel-pagination property="activities" :url="url"></laravel-pagination>
    </section>
</template>

<script>
    import LaravelPagination from "../../LaravelPagination.vue";

    export default {
        name: "ActivityList",
        components: {LaravelPagination},
        data() {
            return {wbs_id: 0, activities: [], loading: false}
        },

        created() {
            window.EventBus.$watch('wbs', level => {
                this.wbs_id = level.id;
            });
        },

        computed: {
            url() {
                return `/api/rollup/wbs/${this.wbs_id}`;
            }
        },

        events: {
            loadingStart() {
                this.loading = true;
            },

            loadingDone() {
                this.loading = false;
            },
        }


    }
</script>

<style scoped>
    .loading {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        z-index: 999;
        background: rgba(255, 255, 255, 0.6);
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>