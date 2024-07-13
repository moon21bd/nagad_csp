import AdminLayout from "../views/admin/layout/index.vue";

export default [
    {
        path: "/admin/required-fields-config/add",
        name: "required-fields-config-add",
        component: () => import("../views/admin/NCRequiredFields/Create.vue"),
        meta: {
            requiresAuth: true,
            layout: AdminLayout
        }
    },
    {
        path: "/admin/required-fields-config",
        name: "required-fields-config-index",
        component: () => import("../views/admin/NCRequiredFields/List.vue"),
        meta: {
            requiresAuth: true,
            layout: AdminLayout
        }
    },
    {
        path: "/admin/required-fields-config/edit/:id",
        name: "required-fields-config-edit",
        component: () => import("../views/admin/NCRequiredFields/Edit.vue"),
        meta: {
            requiresAuth: true,
            layout: AdminLayout
        }
    },
    {
        path: "/admin/tickets/required-fields/add",
        name: "tickets-required-fields-add",
        component: () => import("../views/admin/tickets/showRequiredFields.vue"),
        meta: {
            requiresAuth: true,
            layout: AdminLayout
        }
    },


];
