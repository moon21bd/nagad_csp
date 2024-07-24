import Vue from "vue";
import Router from "vue-router";

import defaultRoutes from "./routers/default";
import rolesPermissions from "./routers/roles-permissions";
import userRoutes from "./routers/users";
import callTypes from "./routers/call-types";
import callCategory from "./routers/call-category";
import callSubCategory from "./routers/call-sub-category";
import callSubSubCategory from "./routers/call-sub-sub-category";
import accessLists from "./routers/access-lists";
import groups from "./routers/groups";
import groupConfigs from "./routers/group-configs";
import store from "./store";
import requiredConfigs from "./routers/required-fields";
import serviceTypeConfigs from "./routers/service-type-configs";

Vue.use(Router);

let router = new Router({
    mode: "history",
    routes: [
        ...defaultRoutes,
        ...userRoutes,
        ...callTypes,
        // ...accessLists,
        ...groups,
        ...callCategory,
        ...callSubCategory,
        // ...callSubSubCategory,
        // ...groupConfigs,
        ...rolesPermissions,
        ...requiredConfigs,
        ...serviceTypeConfigs,
    ],
});

/* router.beforeEach((to, from, next) => {
    if (to.matched.some((record) => record.meta.requiresAuth)) {
        if (!store.getters["auth/authenticated"]) {
            next({
                path: "/login",
                query: { redirect: to.fullPath },
            });
        } else {
            next();
        }
    } else {
        next();
    }
}); */

router.beforeEach((to, from, next) => {
    const isAuthenticated = store.getters["auth/authenticated"];
    if (to.matched.some((record) => record.meta.requiresAuth)) {
        if (!isAuthenticated) {
            next({
                path: "/login",
                query: { redirect: to.fullPath },
            });
        } else {
            next();
        }
    } else {
        /* else if (to.path === "/login" && isAuthenticated) {
        next("/"); // Redirect to home page or dashboard
    } */
        next();
    }
});

export default router;
