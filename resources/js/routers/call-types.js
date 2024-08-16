import CallTypeList from "../views/admin/NCCallTypes/List.vue";
import CallTypeCreate from "../views/admin/NCCallTypes/Create.vue";
import CallTypeEdit from "../views/admin/NCCallTypes/Edit.vue";
import AdminLayout from "../views/admin/layout/index.vue";

export default [
    {
        path: "/admin/service-types",
        name: "service-types-index",
        component: CallTypeList,
        meta: {
            requiresAuth: true,
            requiresPermission: "service-types-index",
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/service-types/create",
        name: "service-types-create",
        component: CallTypeCreate,
        meta: {
            requiresAuth: true,
            requiresPermission: "service-types-create",
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/service-types/edit/:id",
        name: "service-types-edit",
        component: CallTypeEdit,
        meta: {
            requiresAuth: true,
            requiresPermission: "service-types-edit",
            layout: AdminLayout,
        },
    },
];
