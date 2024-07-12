import AdminLayout from "../views/admin/layout/index.vue";

const Index = () => import("../views/admin/NCGroupConfigs/List.vue");
const Create = () => import("../views/admin/NCGroupConfigs/Create.vue");
const Edit = () => import("../views/admin/NCGroupConfigs/Edit.vue");


export default [
    {
        path: "/admin/group-configs",
        name: "group-configs",
        component: Index,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/group-configs/create",
        name: "create-group-config",
        component: Create,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/group-configs/:id/edit",
        name: "edit-group-config",
        component: Edit,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    }
];
