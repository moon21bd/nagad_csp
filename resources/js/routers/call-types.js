import CallTypeList from '../views/admin/NCCallTypes/List.vue';
import CallTypeCreate from '../views/admin/NCCallTypes/Create.vue';
import CallTypeEdit from '../views/admin/NCCallTypes/Edit.vue';
import AdminLayout from "../views/admin/layout/index.vue";

export default [
    {
        path: '/admin/call-types',
        name: 'call-types-index',
        component: CallTypeList,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: '/admin/call-types/create',
        name: 'call-types-create',
        component: CallTypeCreate,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: '/admin/call-types/edit/:id',
        name: 'call-types-edit',
        component: CallTypeEdit, meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    }];
