import AdminLayout from "../views/admin/layout/index.vue";

const Index = () => import("../views/admin/NCCallCategory/List.vue");
const Create = () => import("../views/admin/NCCallCategory/Create.vue");
const Edit = () => import("../views/admin/NCCallCategory/Edit.vue");

export default [
    {
        path: "/admin/call-categories",
        name: "call-categories-index",
        component: Index,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/call-categories/create",
        name: "call-categories-create",
        component: Create,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/call-categories/edit/:id",
        name: "call-categories-edit",
        component: Edit,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
];
