<template>
    <div>
        <div>
            <h1 class="h3 mb-4 text-gray-800">Moon Test</h1>
        </div>

        <div v-if="hasPermission('user-create')">
            <h1>Contact Page Dashboard</h1>
        </div>
        <div v-else>
            <h1>Access Denied</h1>
        </div>

        <div @click="handleClick">Hi, click me!</div>
    </div>
</template>

<script>
import PermissionsComponent from "../../components/PermissionsComponent.vue";
import CommonComponent from "../../components/CommonComponent.vue";

export default {
    name: "Moon",
    mixins: [PermissionsComponent, CommonComponent], // This should not be mixed, just components
    methods: {
        async handleClick() {
            console.log(this.userPermissions);
            console.log(this.userRoles);
            if (this.hasRole("admin")) {
                console.log("role found");
            } else {
                console.log("no role found");
            }
        },
    },
};
</script>
