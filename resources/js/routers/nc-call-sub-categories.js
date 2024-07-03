import Vue from "vue";
import Router from "vue-router";
import store from "../vuex";
import AdminLayout from "../views/admin/layout/index.vue";

const Index = () => import("../views/admin/NCCallSubCategory/List.vue");
const Create = () => import("../views/admin/NCCallSubCategory/Create.vue");
const Edit = () => import("../views/admin/NCCallSubCategory/Edit.vue");

Vue.use(Router);

const routes = [
    {
        path: "/admin/call-sub-categories",
        name: "call-sub-categories-index",
        component: Index,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/call-sub-categories/create",
        name: "call-sub-categories-create",
        component: Create,
        meta: {
            requiresAuth: true,
            layout: AdminLayout,
        },
    },
    {
        path: "/admin/call-sub-categories/edit/:id",
        name: "call-sub-categories-edit",
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
