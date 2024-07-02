import Vue from "vue";
import Router from "vue-router";
import store from "../vuex";
import AdminLayout from "../views/admin/layout/index.vue";

const GroupsIndex = () => import("../views/admin/Groups/List.vue");
const GroupsCreate = () => import("../views/admin/Groups/Create.vue");
const GroupsEdit = () => import("../views/admin/Groups/Edit.vue");

Vue.use(Router);

const routes = [
    {
        path: "/admin/groups",
        name: "groups",
        component: GroupsIndex,
        meta: {
            requiresAuth: true,
            layout: AdminLayout
        }
    },
    {
        path: "/admin/groups/create",
        name: "createGroup",
        component: GroupsCreate,
        meta: {
            requiresAuth: true,
            layout: AdminLayout
        }
    },
    {
        path: "/admin/groups/:id/edit",
        name: "editGroup",
        component: GroupsEdit,
        meta: {
            requiresAuth: true,
            layout: AdminLayout
        }
    }
];

const router = new Router({
    mode: "history",
    routes
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
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
