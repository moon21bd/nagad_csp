import Vue from "vue";
import Router from "vue-router";
import store from "../vuex";
import AdminLayout from "../views/admin/layout/index.vue";

const Index = () => import("../views/admin/NCAccessLists/List.vue");
const Create = () => import("../views/admin/NCAccessLists/Create.vue");
const Edit = () => import("../views/admin/NCAccessLists/Edit.vue");

Vue.use(Router);

const routes = [
    {
        path: "/admin/access-lists",
        name: "access-lists",
        component: Index,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/access-lists/create",
        name: "create-access",
        component: Create,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/access-lists/edit/:id",
        name: "edit-access",
        component: Edit,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
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