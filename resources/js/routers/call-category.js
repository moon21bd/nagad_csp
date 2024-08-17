const Index = () => import("../views/admin/NCCallCategory/List.vue");
const Create = () => import("../views/admin/NCCallCategory/Create.vue");
const Edit = () => import("../views/admin/NCCallCategory/Edit.vue");

export default [
    {
        path: "/admin/service-categories",
        name: "service-categories-index",
        component: Index,
        meta: {
            requiresAuth: true,
            requiresPermission: "service-categories-list",
        },
    },
    {
        path: "/admin/service-categories/create",
        name: "service-categories-create",
        component: Create,
        meta: {
            requiresAuth: true,
            requiresPermission: "service-categories-create",
        },
    },
    {
        path: "/admin/service-categories/edit/:id",
        name: "service-categories-edit",
        component: Edit,
        meta: {
            requiresAuth: true,
            requiresPermission: "service-categories-edit",
        },
    },
];
