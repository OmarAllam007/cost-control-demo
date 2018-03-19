<template>
    <div class="form-group col-sm-4">
        <select v-model="activity" class="form-control input-sm">
            <option value="">Select Activity</option>
            <option v-for="item in activities" :value="item.code" v-text="item.name"></option>
        </select>
    </div>
</template>

<script>
    export default {
        name: "activity-list",

        props: ["wbs"],

        data() {
            return {
                activities: [], activity: ''
            };
        },

        watch: {
            activity() {
                this.$dispatch('activityChanged', activity)
            }
        }
    }
</script>

<style scoped>

</style>