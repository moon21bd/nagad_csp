export default [
    {
        path: "/admin/required-fields-config/add/:cti?/:cci?/:csci?",
        name: "required-fields-config-add",
        component: () => import("../views/admin/NCRequiredFields/Create.vue"),
        meta: {
            requiresAuth: true,
            requiresPermission: "required-fields-config-create",
        },
    },
    {
        path: "/admin/required-fields-config",
        name: "required-fields-config-index",
        component: () => import("../views/admin/NCRequiredFields/List.vue"),
        meta: {
            requiresAuth: true,
            requiresPermission: "required-fields-config-list",
        },
    },
    {
        path: "/admin/required-fields-config/edit/:id",
        name: "required-fields-config-edit",
        component: () => import("../views/admin/NCRequiredFields/Edit.vue"),
        meta: {
            requiresAuth: true,
            requiresPermission: "required-fields-config-edit",
        },
    },
];
