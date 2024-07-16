<template>
    <div>
        <component :is="this.$route.meta.layout || 'div'">
            <nav></nav>
            <router-view/>
        </component>
    </div>
</template>

<script>
// import Nav from "./components/Nav.vue";
// import axios from "axios";

export default {
    async created() {
        //
    },
};
</script>

<style>
</style>
