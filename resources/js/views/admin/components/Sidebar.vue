<template>
    <aside class="bg-white sidebar sidebar-light">
        <!-- Sidebar - Brand -->
        <router-link
            class="sidebar-brand d-flex align-items-center"
            :to="{ name: 'admin' }"
        >
            <i class="icon-logo sidebar-brand-icon mx-auto"></i>
            <img class="sidebar-brand-logo" src="/images/logo.svg" alt="" />
        </router-link>
        <div class="sidebar-nav">
            <el-menu
                :default-active="activeIndex"
                class="el-menu-vertical-demo"
                :unique-opened="true"
            >
                <el-menu-item index="dashboard">
                    <router-link :to="{ name: 'admin' }">
                        <i class="icon-grid"></i>
                        <span>Dashboard</span>
                    </router-link>
                </el-menu-item>

                <el-menu-item index="tickets-index">
                    <router-link :to="{ name: 'ticket-index' }">
                        <i class="icon-tickets"></i>
                        <span>Tickets</span>
                    </router-link>
                </el-menu-item>

                <el-menu-item index="admin-user-index">
                    <router-link :to="{ name: 'user-index' }">
                        <i class="icon-users"></i>
                        <span>Users</span>
                    </router-link>
                </el-menu-item>

                <el-menu-item index="user-location">
                    <router-link
                        :to="{
                            name: 'user-location',
                        }"
                        ><i class="icon-map"></i>
                        <span>User Location</span>
                    </router-link>
                </el-menu-item>

                <div v-if="hasRole('admin|superadmin|owner')">
                    <el-menu-item index="service-types-index">
                        <router-link :to="{ name: 'service-types-index' }">
                            <i class="icon-phone"></i>
                            <span>Service Type</span>
                        </router-link>
                    </el-menu-item>

                    <el-menu-item index="service-categories-index">
                        <router-link :to="{ name: 'service-categories-index' }">
                            <i class="icon-pull"></i>
                            <span>Service Categories</span>
                        </router-link>
                    </el-menu-item>

                    <el-menu-item index="service-sub-categories-index">
                        <router-link
                            :to="{ name: 'service-sub-categories-index' }"
                        >
                            <i class="icon-branch"></i>
                            <span>Service Sub Categories</span>
                        </router-link>
                    </el-menu-item>
                    <el-menu-item index="service-type-config-index">
                        <router-link
                            :to="{ name: 'service-type-config-index' }"
                        >
                            <i class="icon-settings"></i>
                            <span>Service Type Configs</span>
                        </router-link>
                    </el-menu-item>
                    <el-menu-item index="required-fields-config-index">
                        <router-link
                            :to="{ name: 'required-fields-config-index' }"
                        >
                            <i class="icon-sliders"></i>
                            <span>Required Fields Configs</span>
                        </router-link>
                    </el-menu-item>
                    <el-menu-item index="groups-index">
                        <router-link :to="{ name: 'groups-index' }">
                            <i class="icon-users"></i>
                            <span>Group Configs</span>
                        </router-link>
                    </el-menu-item>

                    <el-menu-item index="email-config-index">
                        <router-link :to="{ name: 'email-config-index' }">
                            <i class="icon-mail"></i>
                            <span>Email Configs</span>
                        </router-link>
                    </el-menu-item>

                    <el-menu-item index="roles-index">
                        <router-link :to="{ name: 'roles-index' }">
                            <i class="icon-refer"></i>
                            <span>Roles</span>
                        </router-link>
                    </el-menu-item>

                    <el-menu-item index="permissions">
                        <router-link :to="{ name: 'permissions' }">
                            <i class="icon-shield"></i>
                            <span>Permissions</span>
                        </router-link>
                    </el-menu-item>

                    <el-menu-item index="dnd-user-index">
                        <router-link :to="{ name: 'dnd-user-index' }">
                            <i class="icon-user-x"></i>
                            <span>DnD Users</span>
                        </router-link>
                    </el-menu-item>
                </div>

                <div v-if="hasRole('admin|superadmin|owner')">
                    <el-submenu index="1" class="all-dashboard">
                        <template slot="title">
                            <i class="icon-user"></i>
                            <span>All Dashboard</span>
                        </template>
                        <el-menu-item-group>
                            <el-menu-item index="/admin/charts">
                                <router-link to="/admin/charts">
                                    <i class="icon-grid"></i>
                                    <span>Admin Dashboard</span>
                                </router-link>
                            </el-menu-item>
                            <el-menu-item index="/admin/components/buttons">
                                <router-link to="/admin/components/buttons">
                                    <i class="icon-grid"></i>
                                    <span>Agent Dashboard</span>
                                </router-link>
                            </el-menu-item>
                            <el-menu-item index="/admin/components/updated">
                                <router-link to="/admin/components/updated">
                                    <i class="icon-grid"></i>
                                    <span>Updated</span>
                                </router-link>
                            </el-menu-item>
                        </el-menu-item-group>
                    </el-submenu>
                </div>

                <!-- <el-submenu index="2">
                    <template slot="title">
                        <i class="icon-grid"></i>
                        <span>Navigator One</span>
                    </template>
                    <el-menu-item-group title="Group One">
                        <el-menu-item index="2-1">item one</el-menu-item>
                        <el-menu-item index="2-2">item one</el-menu-item>
                    </el-menu-item-group>
                    <el-menu-item-group title="Group Two">
                        <el-menu-item index="2-3">item three</el-menu-item>
                    </el-menu-item-group>
                    <el-submenu index="2-4">
                        <template slot="title">item four</template>
                        <el-menu-item index="2-4-1">item one</el-menu-item>
                    </el-submenu>
                </el-submenu> -->
            </el-menu>
        </div>
    </aside>

    <!-- End of Sidebar -->
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
    name: "Sidebar",

    computed: {
        activeIndex() {
            return this.$route.path;
        },
        ...mapGetters("permissions", ["hasPermission", "hasRole"]),
    },
    methods: {
        ...mapActions("permissions", ["fetchPermissions"]),
    },
    created() {
        this.fetchPermissions();
    },
};
</script>
