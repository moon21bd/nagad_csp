const Index = () => import("../views/admin/Groups/List.vue");
const Create = () => import("../views/admin/Groups/Create.vue");
const Edit = () => import("../views/admin/Groups/Edit.vue");
const ManageGroupRoles = () =>
    import("../views/admin/Groups/ManageGroupRoles.vue");

export default [
    {
        path: "/admin/groups",
        name: "groups-index",
        component: Index,
        meta: {
            requiresAuth: true,
            requiresPermission: "groups-list",
        },
    },
    {
        path: "/admin/groups/create",
        name: "createGroup",
        component: Create,
        meta: {
            requiresAuth: true,
            requiresPermission: "groups-create",
        },
    },
    {
        path: "/admin/groups/:id/edit",
        name: "editGroup",
        component: Edit,
        meta: {
            requiresAuth: true,
            requiresPermission: "groups-edit",
        },
    },
    {
        path: "/admin/groups/:id/roles",
        name: "manageGroupRoles",
        component: ManageGroupRoles,
        meta: {
            requiresAuth: true,
            requiresPermission: "manage-group-roles",
        },
    },
];
