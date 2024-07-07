import Vue from "vue";

import Router from "vue-router";
import store from "./vuex";
import AdminLayout from "./views/admin/layout/index";
import groupRouters from "./routers/nc-groups";
import callCategoriesRouters from "./routers/nc-call-categories";
import callSubCategoriesRouters from "./routers/nc-call-sub-categories";
import callSubSubCategoriesRouters from "./routers/nc-call-sub-sub-categories";
import callTypesRouters from "./routers/nc-call-types";
import ncAccessLists from "./routers/nc-access-lists";
import ncGroupConfigs from "./routers/nc-group-configs";

Vue.use(Router);

let router = new Router({
    mode: "history",
    routes: [
        {
            path: "/",
            name: "home",
            component: () => import("./views/home/index.vue")
        },
        {
            path: "/login/:user_id?",
            name: "login",
            component: () => import("./views/login/index.vue")
        },
        {
            path: "/register",
            name: "register",
            component: () => import("./views/register/index.vue")
        },
        {
            path: "/verify/user/:id",
            name: "verify",
            props: true,
            component: () => import("./views/verify/index.vue")
        },
        {
            path: "/forgot-password",
            name: "forgot",
            component: () => import("./views/forgot/index.vue")
        },
        {
            path: "/reset/:token",
            name: "reset",
            component: () => import("./views/reset/index.vue")
        },
        /**
         * Admin routes
         */
        {
            path: "/admin",
            name: "admin",
            component: () => import("./views/admin/dashboard.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/components/buttons",
            name: "buttons",
            component: () => import("./views/admin/buttons.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/components/cards",
            name: "cards",
            component: () => import("./views/admin/cards.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/utilities/colors",
            name: "colors",
            component: () => import("./views/admin/colors.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/utilities/borders",
            name: "borders",
            component: () => import("./views/admin/borders.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/utilities/animations",
            name: "animations",
            component: () => import("./views/admin/animations.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/utilities/other",
            name: "other",
            component: () => import("./views/admin/other.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/pages/page-not-found",
            name: "page-not-found",
            component: () => import("./views/admin/page-not-found.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/pages/blank",
            name: "blank",
            component: () => import("./views/admin/blank.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/charts",
            name: "charts",
            component: () => import("./views/admin/charts.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/tables",
            name: "tables",
            component: () => import("./views/admin/tables.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: '/forbidden',
            component: import("./views/permissions/forbidden.vue"),
            name: 'Forbidden',
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        ...groupRouters.options.routes,
        ...callCategoriesRouters.options.routes,
        ...callSubCategoriesRouters.options.routes,
        ...callSubSubCategoriesRouters.options.routes,
        ...callTypesRouters.options.routes,
        ...ncAccessLists.options.routes,
        ...ncGroupConfigs.options.routes,

    ]
});

router.beforeEach((to, from, next) => {
    console.log('to', to, 'from', from)
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (store.getters.user) {
            /*const userPermissions = store.state.userPermissions;
            console.log('userPermissions', userPermissions)
            if (userPermissions.some(permission => permission.path === to.path)) {
                next();
                return;
            } else {
                next({name: 'Forbidden'});
                return;
            }*/
            next();
            return;
        }
        next("/login");
    } else {
        next();
    }
});

export default router;
