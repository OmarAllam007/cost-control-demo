<template>
    <div class="divisions">
        <div class="form-group">
            <input type="search" class="form-control" v-model="search" placeholder="Type to search">
        </div>

        <ul class="wbs-tree">
            <division v-for="division in divisions" :division="division" :search="search"></division>
        </ul>
    </div>
</template>

<script>
    export default {
        name: "divisions",

        props: ['divisions'],

        data() {
            return {search: ''};
        }
    }
</script>