import AdminLayout from "../views/admin/layout/index.vue";

const Index = () => import("../views/admin/NCCallSubSubCategory/List.vue");
const Create = () => import("../views/admin/NCCallSubSubCategory/Create.vue");
const Edit = () => import("../views/admin/NCCallSubSubCategory/Edit.vue");

export default [
    {
        path: "/admin/call-sub-sub-categories",
        name: "call-sub-sub-categories-index",
        component: Index,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/call-sub-sub-categories/create",
        name: "call-sub-sub-categories-create",
        component: Create,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/call-sub-sub-categories/edit/:id",
        name: "call-sub-sub-categories-edit",
        component: Edit,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
];
