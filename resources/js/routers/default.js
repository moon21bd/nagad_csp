export default [
    {
        path: "/admin",
        name: "admin",
        component: () => import("../views/admin/admin-blank.vue"),
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: "/",
        name: "home",
        component: () => import("../views/admin/admin-blank.vue"),
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: "/admin/dashboard",
        name: "dashboard",
        component: () => import("../views/admin/dashboard.vue"),
        meta: {
            requiresPermission: "dashboard",
            requiresAuth: true,
        },
    },
    {
        path: "/moon",
        name: "moon",
        component: () => import("../views/admin/Moon.vue"),
        meta: {
            requiresAuth: true,
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
    {
        path: "/change-password/:token",
        name: "change-password",
        component: () => import("../views/login/changePassword.vue"),
    },
];
