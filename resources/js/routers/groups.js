import AdminLayout from "../views/admin/layout/index.vue";

const Index = () => import("../views/admin/NCGroups/List.vue");
const Create = () => import("../views/admin/NCGroups/Create.vue");
const Edit = () => import("../views/admin/NCGroups/Edit.vue");

export default [
    {
        path: "/admin/groups",
        name: "groups",
        component: Index,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/groups/create",
        name: "createGroup",
        component: Create,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/groups/:id/edit",
        name: "editGroup",
        component: Edit,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
];
