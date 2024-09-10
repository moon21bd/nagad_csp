const Profile = () => import("../views/admin/Profile/Index.vue");
const ChangePassword = () =>
    import("../views/admin/Profile/changePassword.vue");
const UserCreate = () => import("../views/admin/Users/Create.vue");
const UserEdit = () => import("../views/admin/Users/Edit.vue");
const UserList = () => import("../views/admin/Users/List.vue");
const ResetPassword = () => import("../views/admin/Users/resetPassword.vue");
const ManageUserPermissions = () =>
    import("../views/admin/Users/managePermissions.vue");
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
            // requiresPermission: "user-profile",
        },
    },
    {
        name: "user-change-password",
        path: "/admin/change-password",
        component: ChangePassword,
        meta: {
            title: "Change Password",
            middleware: "auth",
            requiresAuth: true,
            // requiresPermission: "user-change-password",
        },
    },
    {
        name: "user-reset-password",
        path: "/admin/user/:id/reset-password",
        component: ResetPassword,
        meta: {
            title: "Reset Password",
            middleware: "auth",
            requiresAuth: true,
            requiresPermission: "user-reset-password",
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
            requiresPermission: "user-create",
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
            requiresPermission: "user-edit",
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
            requiresPermission: "user-list",
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
            requiresPermission: "user-location-view",
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
            requiresPermission: "user-role-manage",
        },
    },

    {
        name: "user-permissions-manage",
        path: "/admin/user/:id/permissions/",
        component: ManageUserPermissions,
        meta: {
            title: "User Permissions Manage",
            middleware: "auth",
            requiresAuth: true,
            requiresPermission: "user-permissions-manage",
        },
    },
];
