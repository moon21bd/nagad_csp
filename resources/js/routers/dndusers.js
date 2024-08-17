const Create = () => import("../views/admin/DnDUsers/Create.vue");
const Edit = () => import("../views/admin/DnDUsers/Edit.vue");
const List = () => import("../views/admin/DnDUsers/List.vue");

export default [
    {
        name: "dnd-user-create",
        path: "/admin/dnd-user/create",
        component: Create,
        meta: {
            title: "DnD User Create",
            middleware: "auth",
            requiresAuth: true,
            requiresPermission: "dnd-user-create",
        },
    },
    {
        name: "dnd-user-edit",
        path: "/admin/dnd-user/:id/edit",
        component: Edit,
        meta: {
            title: "DnD User Edit",
            middleware: "auth",
            requiresAuth: true,
            requiresPermission: "dnd-user-edit",
        },
    },
    {
        name: "dnd-user-index",
        path: "/admin/dnd-users",
        component: List,
        meta: {
            title: "DnD User List",
            middleware: "auth",
            requiresAuth: true,
            requiresPermission: "dnd-user-list",
        },
    },
];
