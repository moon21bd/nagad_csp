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
                                        <div class="btn-group">
                                            <button
                                                type="button"
                                                class="btn-action btn-edit dropdown-toggle"
                                                data-toggle="dropdown"
                                                aria-haspopup="true"
                                                aria-expanded="false"
                                            >
                                                <i class="icon-more"></i>
                                            </button>
                                            <div
                                                class="dropdown-menu dropdown-menu-right"
                                            >
                                                <router-link
                                                    v-if="
                                                        hasRole(
                                                            'admin|superadmin|owner'
                                                        )
                                                    "
                                                    class="dropdown-item text-gray-600"
                                                    :to="{
                                                        name: 'user-edit',
                                                        params: {
                                                            id: item?.id,
                                                        },
                                                    }"
                                                >
                                                    <i
                                                        class="icon-edit-pen mr-1"
                                                    ></i>
                                                    Edit User
                                                </router-link>

                                                <router-link
                                                    v-if="
                                                        hasRole(
                                                            'superadmin|admin'
                                                        )
                                                    "
                                                    class="dropdown-item text-gray-600"
                                                    title="Reset Password"
                                                    :to="{
                                                        name: 'user-reset-password',
                                                        params: {
                                                            id: item?.id,
                                                        },
                                                    }"
                                                >
                                                    <i
                                                        class="icon-unlock mr-1"
                                                    ></i>
                                                    Reset Password
                                                </router-link>

                                                <router-link
                                                    v-if="
                                                        hasRole(
                                                            'superadmin|admin'
                                                        )
                                                    "
                                                    class="dropdown-item text-gray-600"
                                                    title="Role Manage"
                                                    :to="{
                                                        name: 'user-roles-manage',
                                                        params: {
                                                            id: item?.id,
                                                        },
                                                    }"
                                                >
                                                    <i
                                                        class="icon-settings mr-1"
                                                    ></i>
                                                    Role Manage
                                                </router-link>

                                                <router-link
                                                    v-if="
                                                        hasRole(
                                                            'superadmin|admin'
                                                        )
                                                    "
                                                    class="dropdown-item text-gray-600"
                                                    title="Permissions Manage"
                                                    :to="{
                                                        name: 'user-permissions-manage',
                                                        params: {
                                                            id: item?.id,
                                                        },
                                                    }"
                                                >
                                                    <i
                                                        class="icon-shield mr-1"
                                                    ></i>
                                                    Permissions Manage
                                                </router-link>
                                                <a
                                                    v-if="
                                                        hasRole(
                                                            'admin|superadmin|owner'
                                                        )
                                                    "
                                                    class="dropdown-item text-danger"
                                                    @click.prevent="
                                                        deleteUser(item?.id)
                                                    "
                                                >
                                                    <i
                                                        class="icon-trash mr-1"
                                                    ></i>
                                                    Delete User
                                                </a>
                                            </div>
                                        </div>
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
    position: relative;
    overflow: visible;
}
.table > thead > tr > th:last-child {
    background: #fff9f9;
}
.dropdown-menu {
    color: #b1b5b9;
    font-size: 14px;
    box-shadow: 0 0 18px -10px rgba(0, 0, 0, 0.15) !important;
}
.dropdown-menu::before {
    content: "";
    height: 10px;
    width: 10px;
    background: white;
    position: absolute;
    right: 10px;
    top: -5px;
    border-radius: 3px 0px 0px 0px;
    transform: rotate(45deg);
}
.dropdown-toggle::after {
    display: none;
}
.dropdown-item {
    padding: 0.5rem 1rem;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    line-height: 1;
    gap: 3px;
}
.dropdown-item i {
    font-size: 16px;
}
</style>
