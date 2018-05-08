<template>
    <section>
        <section class="loading" v-show="loading"><i class="fa fa-spinner fa-spin fa-3x"></i></section>

        <section class="card" v-if="wbs_id">
            <div class="card-body">
                <input type="search" class="form-control input-sm" name="search" placeholder="Search for activities" v-model="search">
            </div>
        </section>

        <section class="card-group" v-if="activities.length">
            <activity 
                @delete-activity="deleteActivity(activity)"  
                v-for="activity in activities" 
                :initial="activity" 
                :key="activity.code"></activity>
        </section>

        <div class="alert alert-info" v-else>
            <i class="fa fa-info-circle"></i> No Activities Found
        </div>

        <laravel-pagination property="activities" :url="url"></laravel-pagination>
    </section>
</template>

<script>
    import LaravelPagination from "../../LaravelPagination.vue";
    import _ from 'lodash';

    export default {
        name: "ActivityList",
        components: {LaravelPagination},
        data() {
            return {wbs_id: 0, activities: [], loading: false, search: '', term: ''}
        },

        created() {
            window.EventBus.$watch('wbs', level => {
                this.wbs_id = level.id;
            });
        },

        computed: {
            url() {
                if (!this.wbs_id) {
                    return '';
                }
                
                return `/api/rollup/wbs/${this.wbs_id}?term=${this.term}`;
            }
        },

        watch: {
            search: _.debounce(function() {
                this.term = this.search.toLowerCase();
            }, 500)
        },

        methods: {
            deleteActivity(deleted) {
                this.activities = this.activities.filter(activity => 
                    activity.code !== deleted.code
                );
            }
        },

        events: {
            loadingStart() {
                this.loading = true;
            },

            loadingDone() {
                this.loading = false;
            },

            loadingError() {
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