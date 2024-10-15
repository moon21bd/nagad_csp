const Create = () => import("../views/admin/DnDUsers/Create.vue");
const Edit = () => import("../views/admin/DnDUsers/Edit.vue");
const List = () => import("../views/admin/DnDUsers/List.vue");

export default [
    {
        name: "customer-profile-create",
        path: "/admin/customer-profile/create",
        component: Create,
        meta: {
            title: "Customer Profile Create",
            middleware: "auth",
            requiresAuth: true,
            requiresPermission: "customer-profile-create",
        },
    },
    {
        name: "customer-profile-edit",
        path: "/admin/customer-profile/:id/edit",
        component: Edit,
        meta: {
            title: "Customer Profile Edit",
            middleware: "auth",
            requiresAuth: true,
            requiresPermission: "customer-profile-edit",
        },
    },
    {
        name: "customer-profile-index",
        path: "/admin/customer-profiles",
        component: List,
        meta: {
            title: "Customer Profile List",
            middleware: "auth",
            requiresAuth: true,
            requiresPermission: "customer-profile-list",
        },
    },
];
