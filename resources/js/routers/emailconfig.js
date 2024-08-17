const Create = () => import("../views/admin/EmailConfig/Create.vue");
const Edit = () => import("../views/admin/EmailConfig/Edit.vue");
const List = () => import("../views/admin/EmailConfig/List.vue");

export default [
    {
        name: "email-config-create",
        path: "/admin/email-config/create",
        component: Create,
        meta: {
            title: "Email Config Create",
            middleware: "auth",
            requiresAuth: true,
            requiresPermission: "email-config-create",
        },
    },
    {
        name: "email-config-edit",
        path: "/admin/email-config/:id/edit",
        component: Edit,
        meta: {
            title: "Email Config Edit",
            middleware: "auth",
            requiresAuth: true,
            requiresPermission: "email-config-edit",
        },
    },
    {
        name: "email-config-index",
        path: "/admin/email-config",
        component: List,
        meta: {
            title: "Email Config List",
            middleware: "auth",
            requiresAuth: true,
            requiresPermission: "email-config-list",
        },
    },
];
