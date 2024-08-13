<template>
    <div class="container">
        <div class="row">
            <div class="col-12 mb-2">
                <div class="common-heading d-flex align-items-center mb-3">
                    <h3 class="title mr-auto p-3">Permissions</h3>

                    <!-- <router-link
                            class="btn btn-site ml-auto"
                            :to="{ name: 'permissions' }"
                            ><i class="icon-plus"></i> New
                        </router-link> -->
                    <div class="btn-group float-right" role="group">
                        <button
                            type="button"
                            @click="resetform"
                            class="btn btn-site"
                        >
                            <i class="icon-plus"></i> New
                        </button>
                    </div>
                </div>

                <div class="card mb-6">
                    <div class="overlay" v-if="isLoading">
                        <img src="/images/loader.gif" alt="" />
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <div v-if="permissions && permissions.length > 0">
                                <div class="col-mb-12">
                                    <div class="table-responsive">
                                        <table
                                            id="dataTable"
                                            class="table border rounded"
                                        >
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Title</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="(
                                                        permission, key
                                                    ) in permissions"
                                                    :key="key"
                                                >
                                                    <td>
                                                        {{ permission.id }}
                                                    </td>
                                                    <td>
                                                        {{ permission.name }}
                                                    </td>
                                                    <td>
                                                        <button
                                                            type="button"
                                                            :disabled="
                                                                protectedPermissions.indexOf(
                                                                    permission.name
                                                                ) !== -1
                                                            "
                                                            @click="
                                                                editPermission(
                                                                    permission.id
                                                                )
                                                            "
                                                            class="btn btn-success"
                                                        >
                                                            Edit
                                                        </button>
                                                        <button
                                                            type="button"
                                                            :disabled="
                                                                protectedPermissions.indexOf(
                                                                    permission.name
                                                                ) !== -1
                                                            "
                                                            @click="
                                                                deletePermission(
                                                                    permission.id
                                                                )
                                                            "
                                                            class="btn btn-danger"
                                                        >
                                                            Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <no-data v-else />
                            <div class="col-6">
                                <form
                                    ref="RoleForm"
                                    action="javascript:void(0)"
                                    @submit="savePermission"
                                    class="row"
                                    method="post"
                                >
                                    <div class="form-group col-12 mb-2">
                                        <label
                                            for="name"
                                            class="font-weight-bold col-12"
                                            >Permission Name
                                            <i
                                                class="bi bi-asterisk text-danger small-font"
                                            ></i>
                                            <span
                                                class="text-muted ml-auto float-right"
                                                >{{
                                                    permission.id
                                                        ? "You are updating existing permission"
                                                        : "You are going to add new permission"
                                                }}</span
                                            ></label
                                        >
                                        <input
                                            type="hidden"
                                            name="id"
                                            v-model="permission.id"
                                        />
                                        <input
                                            type="text"
                                            name="name"
                                            v-model="permission.name"
                                            id="name"
                                            placeholder="Enter permission name"
                                            class="form-control"
                                        />
                                        <span
                                            v-if="errors.name"
                                            class="text-danger"
                                            >{{ errors.name[0] }}</span
                                        >
                                    </div>
                                    <div class="col-12 mb-2 mb-2 mt-2">
                                        <button
                                            type="submit"
                                            :disabled="processing"
                                            class="btn btn-site"
                                        >
                                            {{
                                                processing
                                                    ? "Please wait"
                                                    : "Save"
                                            }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import "datatables.net-dt/css/jquery.dataTables.min.css";
import "datatables.net-dt/js/dataTables.dataTables";
import axios from "../../../axios";
import noData from "../components/noData.vue";
export default {
    name: "permissions",
    components: {
        noData,
    },
    data() {
        return {
            isLoading: false,
            dataTable: null,
            protectedPermissions: [
                // "role-list",
                // "role-create",
                // "role-edit",
                // "role-delete",
                // "user-list",
                // "user-create",
                // "user-edit",
                // "user-delete",
                // "permission-list",
                // "permission-create",
                // "permission-edit",
                // "permission-delete",
            ],
            permissions: [],
            permission: {
                id: "",
                name: "",
            },
            errors: {
                name: "",
            },
            processing: false,
            isError: false,
        };
    },
    mounted() {
        this.getPermissions();
    },
    methods: {
        initializeDataTable() {
            this.$nextTick(() => {
                if (this.dataTable) {
                    this.dataTable.destroy();
                }
                this.dataTable = $("#dataTable").DataTable({
                    // Initialize DataTable options
                    order: [[0, "desc"]],
                });
            });
        },

        async getPermissions() {
            await axios
                .get("/permissions")
                .then((response) => {
                    console.log("response.data", response.data.data);
                    this.permissions = Array.isArray(response.data.data)
                        ? response.data.data
                        : [];
                    this.initializeDataTable();
                })
                .catch((error) => {
                    console.log("Error fetching permissions:", error);
                    this.permissions = [];
                });
        },

        editPermission(id) {
            axios
                .post("/permissions/" + id)
                .then((response) => {
                    this.permission.id = response.data.data.id;
                    this.permission.name = response.data.data.name;
                })
                .catch((error) => {
                    console.error("Error editing permission:", error);
                });
        },
        async savePermission() {
            this.isError = false;

            if (!this.permission.name) {
                this.errors.name = ["Permission name is required"];
                this.isError = true;
            } else {
                this.errors.name = "";
            }

            if (this.isError) {
                return;
            }

            this.processing = true;
            try {
                const { data } = await axios.post(
                    "/permission/save",
                    this.permission
                );

                await this.getPermissions();

                // Reinitialize DataTable after data is reloaded
                this.$nextTick(() => {
                    if (this.dataTable) {
                        this.dataTable.destroy();
                    }
                    this.dataTable = $("#dataTable").DataTable({
                        order: [[0, "desc"]],
                    });
                });

                this.$toasted.show(data.message, {
                    theme: "toasted-primary",
                    position: "top-right",
                    duration: 5000,
                });

                this.resetform();
            } catch (error) {
                console.error("Error saving permission:", error);
                const errorData = error.response ? error.response.data : {};
                if (errorData.error) {
                    this.$toasted.show(errorData.error, {
                        theme: "toasted-primary",
                        position: "top-right",
                        duration: 5000,
                    });
                }
                this.errors = errorData.errors || {};
            } finally {
                this.processing = false;
            }
        },
        deletePermission(id) {
            if (confirm("Are you sure to delete this permission?")) {
                axios
                    .delete(`/permissions/${id}`)
                    .then(async (response) => {
                        await this.getPermissions();
                    })
                    .catch((error) => {
                        console.error("Error deleting permission:", error);
                    });
            }
        },
        resetform() {
            this.permission.id = "";
            this.permission.name = "";
        },
    },
    watch: {
        permissions(newValue) {
            if (newValue.length) {
                this.initializeDataTable();
            }
        },
    },
};
</script>

<style>
.table.dataTable > thead > tr > th {
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
