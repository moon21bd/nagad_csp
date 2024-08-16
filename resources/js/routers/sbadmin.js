import AdminLayout from "../views/admin/layout/index.vue";

export default [
    /**
     * Default templates routes
     */
    {
        path: "/admin/components/buttons",
        name: "buttons",
        component: () => import("../views/admin/buttons.vue"),
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/components/cards",
        name: "cards",
        component: () => import("../views/admin/cards.vue"),
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/utilities/colors",
        name: "colors",
        component: () => import("../views/admin/colors.vue"),
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/utilities/borders",
        name: "borders",
        component: () => import("../views/admin/borders.vue"),
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/utilities/animations",
        name: "animations",
        component: () => import("../views/admin/animations.vue"),
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/utilities/other",
        name: "other",
        component: () => import("../views/admin/other.vue"),
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/pages/page-not-found",
        name: "page-not-found",
        component: () => import("../views/admin/page-not-found.vue"),
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/pages/blank",
        name: "blank",
        component: () => import("../views/admin/blank.vue"),
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/charts",
        name: "charts",
        component: () => import("../views/admin/charts.vue"),
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/tables",
        name: "tables",
        component: () => import("../views/admin/tables.vue"),
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
];
