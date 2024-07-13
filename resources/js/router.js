import Vue from "vue";
import Router from "vue-router";

import defaultRoutes from "./routers/default";
import rolesPermissions from "./routers/roles-permissions";
import callCategory from "./routers/call-category";
import callSubCategory from "./routers/call-sub-category";
import callSubSubCategory from "./routers/call-sub-sub-category";
import callTypes from "./routers/call-types";
import accessLists from "./routers/access-lists";
import groups from "./routers/groups";
import groupConfigs from "./routers/group-configs";
import store from "./store";
import requiredConfigs from "./routers/required-fields";

Vue.use(Router);

let router = new Router({
    mode: "history",
    routes: [
        ...defaultRoutes,
        ...callTypes,
        ...accessLists,
        ...groups,
        ...callCategory,
        ...callSubCategory,
        ...callSubSubCategory,
        ...groupConfigs,
        ...rolesPermissions,
        ...requiredConfigs

    ]
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (!store.getters['auth/authenticated']) {
            next({
                path: '/login',
                query: {redirect: to.fullPath}
            });
        } else {
            next();
        }
    } else {
        next();
    }
});

export default router;
