import AdminLayout from "../views/admin/layout/index.vue";

const Profile = () => import("../views/admin/Profile/Index.vue");
const ProfileSetting = () => import("../views/admin/Profile/Setting.vue");
const UserCreate = () => import("../views/admin/Users/Create.vue");
const UserEdit = () => import("../views/admin/Users/Edit.vue");
const UserList = () => import("../views/admin/Users/List.vue");
const UserLocation = () => import("../views/admin/Users/Location.vue");
const ManageUserRoles = () =>
    import("../views/admin/Users/ManageUserRoles.vue");

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
        name: "user-profile-setting",
        path: "/admin/profile/setting",
        component: ProfileSetting,
        meta: {
            title: "Profile Setting",
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
        name: "user-location",
        path: "/admin/user/location",
        component: UserLocation,
        meta: {
            title: "User Location",
            middleware: "auth",
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        name: "user-roles-manage",
        path: "/admin/user/:id/roles/",
        component: ManageUserRoles,
        meta: {
            title: "User Role Manage",
            middleware: "auth",
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
];
