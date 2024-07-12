import Vue from "vue";

import Router from "vue-router";
import AdminLayout from "./views/admin/layout/index";
import groupRouters from "./routers/nc-groups";
import callCategoriesRouters from "./routers/nc-call-categories";
import callSubCategoriesRouters from "./routers/nc-call-sub-categories";
import callSubSubCategoriesRouters from "./routers/nc-call-sub-sub-categories";
import callTypesRouters from "./routers/nc-call-types";
import ncAccessLists from "./routers/nc-access-lists";
import ncGroupConfigs from "./routers/nc-group-configs";
import backOffice from "./routers/backoffice";
import store from "./vuex";

Vue.use(Router);

let router = new Router({
    mode: "history",
    routes: [
        {
            path: "/",
            name: "home",
            component: () => import("./views/login/login.vue")
        },
        {
            path: "/login/:user_id?",
            name: "login",
            component: () => import("./views/login/login.vue")
        },
        {
            path: "/register",
            name: "register",
            component: () => import("./views/register/register.vue")
        },
        {
            path: "/verify/user/:id",
            name: "verify",
            props: true,
            component: () => import("./views/verify/verify.vue")
        },
        {
            path: "/forgot-password",
            name: "forgot",
            component: () => import("./views/forgot/forgot.vue")
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
            path: "/admin/tickets",
            name: "tickets",
            component: () => import("./views/admin/tickets/tickets.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout,
            },
        },
        ...groupRouters.options.routes,
        ...callCategoriesRouters.options.routes,
        ...callSubCategoriesRouters.options.routes,
        ...callSubSubCategoriesRouters.options.routes,
        ...callTypesRouters.options.routes,
        ...ncAccessLists.options.routes,
        ...ncGroupConfigs.options.routes,
        ...backOffice.options.routes,

    ]
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (store.getters.user) {
            next();
            return;
        }
        next("/login");
    } else {
        next();
    }
});


/*router.beforeEach((to, from, next) => {
    console.log("Navigating to:", to.fullPath);
    console.log("Authentication status:", store.state.auth.authenticated);

    const isAuthenticated = store.state.auth.authenticated;

    if (to.meta.middleware === "guest") {
        if (isAuthenticated) {
            next({ name: "home" });
        } else {
            next();
        }
    } else {
        if (!isAuthenticated) {
            next({ name: "login" }); // Redirect to login if not authenticated
        } else {
            next();
        }
    }
});*/


/*router.beforeEach((to, from, next) => {
    document.title = `${to.meta.title} - ${process.env.MIX_APP_NAME}`
    console.log('to.meta.middleware', to.meta)
    if (to.meta.middleware == "guest") {
        if (store.state.auth.authenticated) {
            next({name: "home"})
        }
        next()
    } else {
        if (store.state.auth.authenticated) {
            next()
        } else {
            next({name: "home"})
            next()
        }
    }
})*/

export default router;
