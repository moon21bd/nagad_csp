import AdminLayout from "../views/admin/layout/index.vue";

const Profile = () => import("../components/Profile.vue");
const UserCreate = () => import("../views/admin/Users/Create.vue");
const UserEdit = () => import("../views/admin/Users/Edit.vue");
const UserList = () => import("../views/admin/Users/List.vue");
const UserLogs = () => import("../components/Logs.vue");

export default [
    {
        name: "user-profile",
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
        name: "user-create",
        path: "/admin/user/create",
        component: UserCreate,
        meta: {
            title: "User Create",
            middleware: "auth",
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        name: "user-edit",
        path: "/admin/user/:id/edit",
        component: UserEdit,
        meta: {
            title: "User Edit",
            middleware: "auth",
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        name: "user-index",
        path: "/admin/users",
        component: UserList,
        meta: {
            title: "User List",
            middleware: "auth",
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        name: "user-logs",
        path: "/admin/user-logs",
        component: UserLogs,
        meta: {
            title: "User Logs",
            middleware: "auth",
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
];
