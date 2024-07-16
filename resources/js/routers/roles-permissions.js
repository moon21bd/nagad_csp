import AdminLayout from "../views/admin/layout/index.vue";

const Profile = () => import("../components/Profile.vue");
const Roles = () => import("../components/Roles.vue");
const Permissions = () => import("../components/Permissions.vue");
const PermissionsAssign = () => import("../components/PermissionsAssign.vue");
const Users = () => import("../components/Users.vue");

export default [
    {
        name: "profile",
        path: "/admin/profile",
        component: Profile,
        meta: {
            title: "Profile",
            middleware: "auth",
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        name: "roles",
        path: "/admin/roles",
        component: Roles,
        meta: {
            title: "Roles",
            middleware: "auth",
            requiresAuth: true,
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
            layout: AdminLayout,
        },
    },
    {
        name: "permissionsAssign",
        path: "/admin/permissions-assign",
        component: PermissionsAssign,
        meta: {
            title: "PermissionsAssign",
            middleware: "auth",
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        name: "users",
        path: "/admin/users",
        component: Users,
        meta: {
            title: "Users",
            middleware: "auth",
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
];
