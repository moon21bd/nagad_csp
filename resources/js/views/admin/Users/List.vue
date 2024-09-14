<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Users</h1>
            <router-link
                v-if="hasRole('admin|superadmin|owner')"
                class="btn btn-site ml-auto"
                :to="{ name: 'user-create' }"
                ><i class="icon-plus"></i> New
            </router-link>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div v-if="users.length && !isLoading">
                    <div class="table-responsive">
                        <table id="dataTable" class="table border rounded">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>User</th>
                                    <th>UserID</th>
                                    <th>Role Level</th>
                                    <th>Email</th>
                                    <th>User Group</th>
                                    <th>Last Login</th>
                                    <th>Active Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, key) in users" :key="key">
                                    <td>{{ item?.id || "" }}</td>

                                    <td :title="item?.avatar || ''">
                                        <img :src="item?.avatar || ''" alt="" />
                                        {{ item?.name || "" }}
                                    </td>

                                    <td>
                                        {{
                                            item?.user_details
                                                ?.employee_user_id || ""
                                        }}
                                    </td>
                                    <td>
                                        {{ item.level_label || "Unknown" }}
                                    </td>

                                    <td>
                                        {{ item?.email || "" }}
                                    </td>

                                    <td>
                                        {{ item?.group?.name || "" }}
                                    </td>

                                    <td>
                                        <!-- {{
                                            item.latest_login_activity
                                                .last_online || ""
                                        }}
 -->
                                        {{
                                            item?.latest_login_activity
                                                ?.last_online
                                                ? new Date(
                                                      item.latest_login_activity.last_online
                                                  ).toDateString()
                                                : ""
                                        }}
                                    </td>

                                    <td>
                                        <span
                                            :class="
                                                item?.status === 'active'
                                                    ? 'active'
                                                    : 'inactive'
                                            "
                                            class="badge"
                                        >
                                            {{ item?.status || "" }}
                                        </span>
                                    </td>

                                    <td class="text-right">
                                        <router-link
                                            v-if="
                                                hasRole(
                                                    'admin|superadmin|owner'
                                                )
                                            "
                                            class="btn-action btn-edit"
                                            :to="{
                                                name: 'user-edit',
                                                params: { id: item?.id },
                                            }"
                                        >
                                            <i class="icon-edit-pen"></i>
                                        </router-link>

                                        <router-link
                                            v-if="hasRole('superadmin|admin')"
                                            class="btn-action btn-edit"
                                            title="Reset Password"
                                            :to="{
                                                name: 'user-reset-password',
                                                params: { id: item?.id },
                                            }"
                                        >
                                            <i class="icon-unlock"></i>
                                        </router-link>

                                        <router-link
                                            v-if="hasRole('superadmin|admin')"
                                            class="btn btn-action"
                                            title="Role Manage"
                                            :to="{
                                                name: 'user-roles-manage',
                                                params: { id: item?.id },
                                            }"
                                        >
                                            <i class="icon-settings"></i>
                                        </router-link>

                                        <router-link
                                            v-if="hasRole('superadmin|admin')"
                                            class="btn btn-action"
                                            title="Permissions Manage"
                                            :to="{
                                                name: 'user-permissions-manage',
                                                params: { id: item?.id },
                                            }"
                                        >
                                            <i class="icon-shield"></i>
                                        </router-link>

                                        <a
                                            v-if="
                                                hasRole(
                                                    'admin|superadmin|owner'
                                                )
                                            "
                                            class="btn-action btn-trash"
                                            @click.prevent="
                                                deleteUser(item?.id)
                                            "
                                        >
                                            <i class="icon-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <no-data v-else />
            </div>
        </div>
    </div>
</template>

<script>
import "datatables.net-dt/css/jquery.dataTables.min.css";
import "datatables.net-dt/js/dataTables.dataTables";
import { mapActions, mapGetters } from "vuex";
import noData from "../components/noData.vue";

export default {
    components: {
        noData,
    },
    data() {
        return {
            isLoading: false,
            status: "active",
            users: [],
        };
    },
    computed: {
        ...mapGetters("permissions", [
            "permissions",
            "hasPermission",
            "hasRole",
            "userRoles",
            "userPermissions",
        ]),
    },
    mounted() {
        this.fetchUsers();
    },
    created() {
        this.fetchPermissions();
    },
    methods: {
        ...mapActions("permissions", ["fetchPermissions"]),

        async deleteUser(id) {
            try {
                const loggedInUserId = this.$store.state.auth.user.id;
                const user = this.users.find((user) => user.id === id);

                if (user) {
                    if (user.id === loggedInUserId) {
                        alert("You cannot delete your own account.");
                        return;
                    }

                    if (user.level === 1) {
                        alert("Users with level 1 cannot be deleted.");
                        return;
                    }

                    if (confirm("Are you sure you want to delete this User?")) {
                        await axios
                            .delete(`/user/${id}/delete`)
                            .then((response) => {
                                this.getUsers();
                            })
                            .catch((error) => {
                                console.log(error);
                            });
                        this.fetchUsers();
                    }
                } else {
                    alert("User not found.");
                }
            } catch (error) {
                console.error("Error deleting user:", error);
            }
        },
        async fetchUsers() {
            try {
                this.isLoading = true;
                const response = await axios.get("/users-index");
                console.log("response", response.data.data);
                this.users = response.data.data;
            } catch (error) {
                console.error("Error fetching users:", error);
            } finally {
                this.isLoading = false;
            }
        },
        initializeDataTable() {
            this.$nextTick(() => {
                $("#dataTable").DataTable({
                    order: [[0, "desc"]],
                });
            });
        },
    },

    watch: {
        users(newValue) {
            if (newValue.length) {
                this.initializeDataTable();
            }
        },
    },
};
</script>
<style>
.table.dataTable > thead > tr > th,
.table.dataTable > tbody > tr > td {
    white-space: nowrap;
}
.table > thead > tr > th:last-child,
.table > tbody > tr > td:last-child {
    white-space: nowrap;
    position: sticky;
    right: 0;
    background: #fff;
}
.table > thead > tr > th:last-child {
    background: #fff9f9;
}
</style>
