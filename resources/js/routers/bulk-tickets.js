const BulkTicketsCreate = () =>
    import("../views/admin/BulkTicketsCreate/Create.vue");
const BulkTicketsCreateList = () =>
    import("../views/admin/BulkTicketsCreate/List.vue");

const BulkTicketsStatusUpdateCreate = () =>
    import("../views/admin/BulkTicketsStatusUpdate/Create.vue");

const BulkTicketsStatusUpdateList = () =>
    import("../views/admin/BulkTicketsStatusUpdate/List.vue");

export default [
    {
        path: "/admin/bulk-tickets/create",
        name: "bulk-tickets-create",
        component: BulkTicketsCreate,
        meta: {
            requiresAuth: true,
            requiresPermission: "bulk-tickets-create",
        },
    },
    {
        path: "/admin/bulk-tickets",
        name: "bulk-tickets-create-list",
        component: BulkTicketsCreateList,
        meta: {
            requiresAuth: true,
            requiresPermission: "bulk-tickets-create-list",
        },
    },
    {
        path: "/admin/bulk-tickets/status-update",
        name: "bulk-tickets-status-update-create",
        component: BulkTicketsStatusUpdateCreate,
        meta: {
            requiresAuth: true,
            requiresPermission: "bulk-tickets-status-update",
        },
    },
    {
        path: "/admin/bulk-tickets/status-list",
        name: "bulk-tickets-status-update-list",
        component: BulkTicketsStatusUpdateList,
        meta: {
            requiresAuth: true,
            requiresPermission: "bulk-tickets-status-update-list",
        },
    },
];
