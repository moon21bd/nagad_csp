import AdminLayout from "../views/admin/layout/index.vue";

export default [
    {
        path: "/admin",
        name: "admin",
        component: () => import("../views/admin/dashboard.vue"),
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/",
        name: "home",
        component: () => import("../views/admin/dashboard.vue"),
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/moon",
        name: "moon",
        component: () => import("../views/admin/Moon.vue"),
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/login/:user_id?",
        name: "login",
        component: () => import("../views/login/login.vue"),
    },
    {
        path: "/register",
        name: "register",
        component: () => import("../views/register/register.vue"),
    },
    {
        path: "/verify/user/:id",
        name: "verify",
        props: true,
        component: () => import("../views/verify/verify.vue"),
    },
    {
        path: "/forgot-password",
        name: "forgot",
        component: () => import("../views/forgot/forgot.vue"),
    },
    {
        path: "/reset/:token",
        name: "reset",
        component: () => import("../views/reset/index.vue"),
    },
];
