<template>
    <section>
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
            window.EventBus.$on('wbsChanged', wbs => {
                this.wbs_id = wbs.id;
            });
        },

        computed: {
            url() {
                return `/api/rollup/wbs/${this.wbs_id}`;
            }
        }


    }
</script>

<style scoped>

</style>