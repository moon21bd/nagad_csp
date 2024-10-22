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
                <el-menu-item
                    index="dashboard"
                    v-if="
                        hasRole('admin|superadmin') ||
                        hasPermission('dashboard')
                    "
                >
                    <router-link :to="{ name: 'dashboard' }">
                        <i class="icon-grid"></i>
                        <span>Dashboard</span>
                    </router-link>
                </el-menu-item>

                <el-menu-item
                    index="tickets-index"
                    v-if="
                        hasRole('admin|superadmin') ||
                        hasPermission('ticket-list')
                    "
                >
                    <router-link :to="{ name: 'ticket-index' }">
                        <i class="icon-tickets"></i>
                        <span>Tickets</span>
                    </router-link>
                </el-menu-item>

                <el-menu-item
                    index="admin-user-index"
                    v-if="
                        hasRole('admin|superadmin') ||
                        hasPermission('user-list')
                    "
                >
                    <router-link :to="{ name: 'user-index' }">
                        <i class="icon-users"></i>
                        <span>Users</span>
                    </router-link>
                </el-menu-item>

                <el-menu-item
                    v-if="
                        hasRole('admin|superadmin') ||
                        hasPermission('user-location-view')
                    "
                    index="user-location"
                >
                    <router-link
                        :to="{
                            name: 'user-location',
                        }"
                        ><i class="icon-map"></i>
                        <span>User Location</span>
                    </router-link>
                </el-menu-item>

                <el-menu-item
                    index="bulk-tickets-create-list"
                    v-if="
                        hasRole('admin|superadmin') ||
                        hasPermission('bulk-tickets-create-list')
                    "
                >
                    <router-link :to="{ name: 'bulk-tickets-create-list' }">
                        <i class="icon-tickets"></i>
                        <span>Bulk Ticket Create</span>
                    </router-link>
                </el-menu-item>

                <el-menu-item
                    index="bulk-tickets-status-update-list"
                    v-if="
                        hasRole('admin|superadmin') ||
                        hasPermission('bulk-tickets-status-update-list')
                    "
                >
                    <router-link
                        :to="{ name: 'bulk-tickets-status-update-list' }"
                    >
                        <i class="icon-ticket-fill"></i>
                        <span>Bulk Ticket Status Update</span>
                    </router-link>
                </el-menu-item>

                <div v-if="hasRole('admin|superadmin')">
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

                    <el-menu-item index="customer-profile-index">
                        <router-link :to="{ name: 'customer-profile-index' }">
                            <i class="icon-user"></i>
                            <span>Customer Profiles</span>
                        </router-link>
                    </el-menu-item>
                </div>

                <!-- <div
                    v-if="hasRole('admin|superadmin')"
                    class="sticky-dashboard"
                >
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
                </div> -->
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
