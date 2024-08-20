export default [
    {
        path: "/admin/ticket/create",
        name: "ticket-create",
        component: () => import("../views/admin/Tickets/Create.vue"),
        meta: {
            requiresAuth: true,
            requiresPermission: "ticket-create",
        },
    },
    {
        path: "/admin/ticket/edit/:id",
        name: "ticket-edit",
        component: () => import("../views/admin/Tickets/Edit.vue"),
        meta: {
            requiresAuth: true,
            requiresPermission: "ticket-edit",
        },
    },
    {
        path: "/admin/tickets",
        name: "ticket-index",
        component: () => import("../views/admin/Tickets/List.vue"),
        meta: {
            requiresAuth: true,
            requiresPermission: "ticket-list",
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
        },
    },
    {
        path: "/admin/ticket/:id/timeline",
        name: "ticket-timeline",
        component: () => import("../views/admin/Tickets/Timeline.vue"),
        meta: {
            requiresAuth: true,
            requiresPermission: "ticket-timeline",
        },
    },
];
