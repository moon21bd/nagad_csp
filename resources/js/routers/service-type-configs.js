export default [
    {
        path: "/admin/service-type-config/add",
        name: "service-type-config-add",
        component: () =>
            import("../views/admin/NCServiceTypeConfig/Create.vue"),
        meta: {
            requiresAuth: true,
            requiresPermission: "service-type-config-create",
        },
    },
    {
        path: "/admin/service-type-config",
        name: "service-type-config-index",
        component: () => import("../views/admin/NCServiceTypeConfig/List.vue"),
        meta: {
            requiresAuth: true,
            requiresPermission: "service-type-config-list",
        },
    },
    {
        path: "/admin/service-type-config/edit/:id",
        name: "service-type-config-edit",
        component: () => import("../views/admin/NCServiceTypeConfig/Edit.vue"),
        meta: {
            requiresAuth: true,
            requiresPermission: "service-type-config-edit",
        },
    },
];
