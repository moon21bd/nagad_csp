import Vue from "vue";
import Router from "vue-router";

import defaultRoutes from "./routers/default";
import rolesPermissions from "./routers/roles-permissions";
import userRoutes from "./routers/users";
import callTypes from "./routers/call-types";
import callCategory from "./routers/call-category";
import callSubCategory from "./routers/call-sub-category";
import groups from "./routers/groups";
import requiredConfigs from "./routers/required-fields";
import serviceTypeConfigs from "./routers/service-type-configs";
import notifications from "./routers/notification";
import store from "./store";

Vue.use(Router);

let router = new Router({
    mode: "history",
    routes: [
        ...defaultRoutes,
        ...userRoutes,
        ...callTypes,
        ...groups,
        ...callCategory,
        ...callSubCategory,
        ...rolesPermissions,
        ...requiredConfigs,
        ...serviceTypeConfigs,
        ...notifications,
    ],
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = store.getters["auth/authenticated"];

    // Checking if the user is trying to access the login page
    if (to.path === "/login" && isAuthenticated) {
        next({ path: "/" });
    } else if (to.matched.some((record) => record.meta.requiresAuth)) {
        // Check if the route requires authentication
        if (!isAuthenticated) {
            // Redirect to the login page with the intended route as a query parameter
            next({
                path: "/login",
                query: { redirect: to.fullPath },
            });
        } else {
            // Proceed to the route
            next();
        }
    } else {
        // Proceed to the route
        next();
    }
});

export default router;
