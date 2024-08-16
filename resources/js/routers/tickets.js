import AdminLayout from "../views/admin/layout/index.vue";

export default [
    {
        path: "/admin/ticket/create",
        name: "ticket-create",
        component: () => import("../views/admin/Tickets/Create.vue"),
        meta: {
            requiresAuth: true,
            requiresPermission: "ticket-create",
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/ticket/edit/:id",
        name: "ticket-edit",
        component: () => import("../views/admin/Tickets/Edit.vue"),
        meta: {
            requiresAuth: true,
            requiresPermission: "ticket-edit",
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/tickets",
        name: "ticket-index",
        component: () => import("../views/admin/Tickets/List.vue"),
        meta: {
            requiresAuth: true,
            requiresPermission: "ticket-list",
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/tickets/required-fields/add",
        name: "tickets-required-fields-add",
        component: () =>
            import(
                "../views/admin/Tickets/category-wise-required-fields-add.vue"
            ),
        meta: {
            requiresAuth: true,
            requiresPermission: "tickets-required-fields-create",
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/tickets/timeline",
        name: "ticket-timeline",
        component: () => import("../views/admin/Tickets/Timeline.vue"),
        meta: {
            requiresAuth: true,
            requiresPermission: "ticket-timeline",
            layout: AdminLayout,
        },
    },
];
