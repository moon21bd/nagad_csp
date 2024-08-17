<template>
    <div></div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
    name: "PermissionsComponent",
    computed: {
        ...mapGetters("permissions", [
            "userPermissions",
            "hasPermission",
            "userRoles",
            "hasRole",
        ]),
    },
    methods: {
        ...mapActions("permissions", ["fetchPermissions"]),
        async updatePermissions() {
            await this.fetchPermissions(); // Fetch permissions and update the store
        },
    },
    created() {
        this.updatePermissions(); // Fetch permissions on component creation
    },
};
</script>
