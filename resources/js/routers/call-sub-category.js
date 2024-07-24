import AdminLayout from "../views/admin/layout/index.vue";

const Index = () => import("../views/admin/NCCallSubCategory/List.vue");
const Create = () => import("../views/admin/NCCallSubCategory/Create.vue");
const Edit = () => import("../views/admin/NCCallSubCategory/Edit.vue");

export default [
    {
        path: "/admin/service-sub-categories",
        name: "service-sub-categories-index",
        component: Index,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/service-sub-categories/create",
        name: "service-sub-categories-create",
        component: Create,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/service-sub-categories/edit/:id",
        name: "service-sub-categories-edit",
        component: Edit,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
];
