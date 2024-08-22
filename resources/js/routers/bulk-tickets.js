const BulkTicketCreate = () =>
    import("../views/admin/BulkTicketCreate/Create.vue");
const BulkTicketStatusUpdate = () =>
    import("../views/admin/BulkTicketStatusUpdate/Edit.vue");

export default [
    {
        path: "/admin/bulk-ticket/create",
        name: "bulk-ticket-create",
        component: BulkTicketCreate,
        meta: {
            requiresAuth: true,
            requiresPermission: "bulk-ticket-create",
        },
    },
    {
        path: "/admin/bulk-ticket/status/update",
        name: "bulk-ticket-status-update",
        component: BulkTicketStatusUpdate,
        meta: {
            requiresAuth: true,
            requiresPermission: "bulk-ticket-status-update",
        },
    },
];
