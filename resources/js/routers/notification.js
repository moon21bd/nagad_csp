export default [
    {
        path: "/admin/notifications",
        name: "notification-list",
        component: () => import("../views/admin/NCNotification/List.vue"),
        meta: {
            requiresAuth: true,
        },
    },
];
