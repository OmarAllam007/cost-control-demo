<template>
    <ul class="wbs-tree">
        <division v-for="division in divisions" :division="division"></division>
    </ul>
</template>

<script>
    export default {
        name: "divisions",

        props: ['divisions']
    }
</script>