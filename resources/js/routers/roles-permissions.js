import AdminLayout from "../views/admin/layout/index.vue";

const RolesCreate = () => import("../views/admin/Roles/Create.vue");
const RolesList = () => import("../views/admin/Roles/List.vue");
const RolesEdit = () => import("../views/admin/Roles/Edit.vue");
const Permissions = () => import("../views/admin/Roles/Permissions.vue");
const PermissionDenied = () => import("../views/admin/components/Denied.vue");

export default [
    {
        name: "roles-create",
        path: "/admin/roles/create",
        component: RolesCreate,
        meta: {
            title: "Roles",
            middleware: "auth",
            requiresAuth: true,
            requiresPermission: "role-create",
            layout: AdminLayout,
        },
    },
    {
        name: "roles-index",
        path: "/admin/roles/",
        component: RolesList,
        meta: {
            title: "Roles List",
            middleware: "auth",
            requiresAuth: true,
            requiresPermission: "role-list",
            layout: AdminLayout,
        },
    },
    {
        name: "roles-edit",
        path: "/admin/role/:id/edit",
        component: RolesEdit,
        meta: {
            title: "Roles Edit",
            middleware: "auth",
            requiresAuth: true,
            requiresPermission: "role-edit",
            layout: AdminLayout,
        },
    },
    {
        name: "permissions",
        path: "/admin/permissions",
        component: Permissions,
        meta: {
            title: "Permissions",
            middleware: "auth",
            requiresAuth: true,
            requiresPermission: "permission-list",
            layout: AdminLayout,
        },
    },
    {
        name: "permission-denied",
        path: "/forbidden",
        component: PermissionDenied,
        meta: {
            title: "Permission Denied",
            middleware: "auth",
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
];
