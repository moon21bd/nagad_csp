<template>
    <aside class="bg-white sidebar sidebar-light">
        <!-- Sidebar - Brand -->
        <router-link
            class="sidebar-brand d-flex align-items-center justify-content-center"
            to="/"
        >
            <i class="icon-logo sidebar-brand-icon"></i>
            <img class="sidebar-brand-logo" src="/images/logo.svg" alt="" />
        </router-link>
        <div class="sidebar-nav">
            <el-menu
                :default-active="activeIndex"
                class="el-menu-vertical-demo"
                :unique-opened="true"
            >
                <el-menu-item index="/admin">
                    <router-link to="/admin">
                        <i class="icon-grid"></i>
                        <span>Dashboard</span>
                    </router-link>
                </el-menu-item>
                <el-menu-item index="/admin/tickets">
                    <router-link to="/admin/tickets">
                        <i class="icon-tickets"></i>
                        <span>Tickets</span>
                    </router-link>
                </el-menu-item>
                <el-menu-item index="/admin/download">
                    <router-link to="/admin/download">
                        <i class="icon-download"></i>
                        <span>Ticket Download</span>
                    </router-link>
                </el-menu-item>
                <el-menu-item index="/admin/back-office">
                    <router-link to="/admin/back-office">
                        <i class="icon-briefcase"></i>
                        <span>Back Office</span>
                    </router-link>
                </el-menu-item>
                <el-menu-item index="/admin/call-types">
                    <router-link to="/admin/call-types">
                        <i class="icon-phone"></i>
                        <span>Call Type</span>
                    </router-link>
                </el-menu-item>
                <el-menu-item index="/admin/users">
                    <router-link to="/admin/users">
                        <i class="icon-users"></i>
                        <span>Users</span>
                    </router-link>
                </el-menu-item>
                <el-menu-item index="/admin/roles">
                    <router-link to="/admin/roles">
                        <i class="icon-phone"></i>
                        <span>Roles</span>
                    </router-link>
                </el-menu-item>

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
                        <el-menu-item index="/admin/components/multipurpose">
                            <router-link to="/admin/components/multipurpose">
                                <i class="icon-grid"></i>
                                <span>Multipurpose</span>
                            </router-link>
                        </el-menu-item>
                    </el-menu-item-group>
                </el-submenu>
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
export default {
    name: "Sidebar",
    computed: {
        activeIndex() {
            return this.$route.path; // Reactively return the current route path
        },
    },
};
</script>
