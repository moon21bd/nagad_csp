import Vue from 'vue';
import Router from 'vue-router';
import CallTypeList from '../views/admin/NCCallTypes/List.vue';
import CallTypeCreate from '../views/admin/NCCallTypes/Create.vue';
import CallTypeEdit from '../views/admin/NCCallTypes/Edit.vue';
import AdminLayout from "../views/admin/layout/index.vue";
import store from "../vuex";

Vue.use(Router);

const routes = [
    {
        path: '/admin/call-types',
        name: 'call-types',
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
        component: CallTypeEdit,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    }
];

const router = new Router({
    mode: "history",
    routes,
});

router.beforeEach((to, from, next) => {
    if (to.matched.some((record) => record.meta.requiresAuth)) {
        if (store.getters.user) {
            next();
            return;
        }
        next("/login");
    } else {
        next();
    }
});

export default router;
