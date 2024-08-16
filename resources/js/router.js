import Vue from "vue";
import Router from "vue-router";

import defaultRoutes from "./routers/default";
import sbadminRoutes from "./routers/sbadmin";
import rolesPermissions from "./routers/roles-permissions";
import userRoutes from "./routers/users";
import callTypes from "./routers/call-types";
import callCategory from "./routers/call-category";
import callSubCategory from "./routers/call-sub-category";
import groups from "./routers/groups";
import requiredConfigs from "./routers/required-fields";
import serviceTypeConfigs from "./routers/service-type-configs";
import notifications from "./routers/notification";
import tickets from "./routers/tickets";
import store from "./store";

Vue.use(Router);

let router = new Router({
    mode: "history",
    routes: [
        ...defaultRoutes,
        ...sbadminRoutes,
        ...userRoutes,
        ...callTypes,
        ...groups,
        ...callCategory,
        ...callSubCategory,
        ...rolesPermissions,
        ...requiredConfigs,
        ...serviceTypeConfigs,
        ...notifications,
        ...tickets,
    ],
});

router.beforeEach(async (to, from, next) => {
    const isAuthenticated = store.getters["auth/authenticated"];

    // If the user is authenticated and tries to access the login page, redirect to home
    if (to.path === "/login" && isAuthenticated) {
        return next({ path: "/" });
    }

    // Check if the route requires authentication
    if (to.matched.some((record) => record.meta.requiresAuth)) {
        if (!isAuthenticated) {
            // If the user is not authenticated, redirect to login
            return next({
                path: "/login",
                query: { redirect: to.fullPath },
            });
        }
    }

    // If the route requires a specific permission
    if (to.meta.requiresPermission) {
        await store.dispatch("permissions/fetchPermissions"); // Fetch permissions before checking

        if (
            store.getters["permissions/hasPermission"](
                to.meta.requiresPermission
            )
        ) {
            // User has the required permission, proceed to the route
            return next();
        } else {
            // User doesn't have the required permission, redirect to forbidden page
            return next("/forbidden");
        }
    }

    // If no authentication or permission is required, or all checks passed
    next();
});

export default router;
