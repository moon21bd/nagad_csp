import AdminLayout from "../views/admin/layout/index.vue";

const Create = () => import("../views/admin/DnDUsers/Create.vue");
const Edit = () => import("../views/admin/DnDUsers/Edit.vue");
const List = () => import("../views/admin/DnDUsers/List.vue");

export default [
    {
        name: "dnduser-create",
        path: "/admin/dnduser/create",
        component: Create,
        meta: {
            title: "DnD User Create",
            middleware: "auth",
            requiresAuth: true,
            requiresPermission: "dnduser-create",
            layout: AdminLayout,
        },
    },
    {
        name: "dnduser-edit",
        path: "/admin/dnduser/:id/edit",
        component: Edit,
        meta: {
            title: "DnD User Edit",
            middleware: "auth",
            requiresAuth: true,
            requiresPermission: "dnduser-edit",
            layout: AdminLayout,
        },
    },
    {
        name: "dnduser-index",
        path: "/admin/dndusers",
        component: List,
        meta: {
            title: "DnD User List",
            middleware: "auth",
            requiresAuth: true,
            requiresPermission: "dnduser-list",
            layout: AdminLayout,
        },
    },
];
