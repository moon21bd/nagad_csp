import AdminLayout from "../views/admin/layout/index.vue";

export default [
    {
        path: "/admin/service-type-config/add",
        name: "service-type-config-add",
        component: () =>
            import("../views/admin/NCServiceTypeConfig/Create.vue"),
        meta: {
            requiresAuth: true,
            requiresPermission: "service-type-config-create",
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/service-type-config",
        name: "service-type-config-index",
        component: () => import("../views/admin/NCServiceTypeConfig/List.vue"),
        meta: {
            requiresAuth: true,
            requiresPermission: "service-type-config-list",
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/service-type-config/edit/:id",
        name: "service-type-config-edit",
        component: () => import("../views/admin/NCServiceTypeConfig/Edit.vue"),
        meta: {
            requiresAuth: true,
            requiresPermission: "service-type-config-edit",
            layout: AdminLayout,
        },
    },
];
