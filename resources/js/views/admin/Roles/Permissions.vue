<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Permissions</h1>
            <button
                type="button"
                @click="resetform"
                class="btn btn-site ml-auto"
            >
                <i class="icon-plus"></i> New
            </button>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-header" v-if="showForm">
                <form
                    ref="RoleForm"
                    action="javascript:void(0)"
                    @submit="savePermission"
                    method="post"
                >
                    <label for="name" class="control-label"
                        >Permission Name
                        <small class="text-muted">{{
                            permission.id
                                ? "(You are updating existing permission)"
                                : "(You are going to add new permission)"
                        }}</small>
                    </label>
                    <div class="form-row">
                        <div class="col-md-4">
                            <div>
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
                                <span v-if="errors.name" class="text-danger">{{
                                    errors.name[0]
                                }}</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button
                                type="submit"
                                :disabled="processing"
                                class="btn btn-site mt-3 mt-md-0"
                            >
                                {{
                                    processing
                                        ? "Please wait"
                                        : permission.id
                                        ? "Update"
                                        : "Save"
                                }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div v-if="permissions && permissions.length > 0">
                    <div class="table-responsive">
                        <table id="dataTable" class="table border rounded">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(permission, key) in permissions"
                                    :key="key"
                                >
                                    <td>{{ permission.id }}</td>
                                    <td>{{ permission.name }}</td>
                                    <td class="text-right">
                                        <!-- <router-link
                                            class="btn-action btn-edit"
                                            title="Update permissions"
                                            :to="{
                                                name: 'roles-edit',
                                                params: { id: role.id },
                                            }"
                                            ><i class="icon-settings"></i
                                        ></router-link> -->
                                        <button
                                            type="button"
                                            :disabled="
                                                protectedPermissions.indexOf(
                                                    permission.name
                                                ) !== -1
                                            "
                                            @click="
                                                editPermission(permission.id)
                                            "
                                            class="btn-action btn-edit"
                                        >
                                            <i class="icon-edit-pen"></i>
                                        </button>

                                        <button
                                            type="button"
                                            class="btn-action btn-trash"
                                            :disabled="
                                                protectedPermissions.indexOf(
                                                    permission.name
                                                ) !== -1
                                            "
                                            @click="
                                                deletePermission(permission.id)
                                            "
                                        >
                                            <i class="icon-trash"></i>
                                        </button>
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
            showForm: false,
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
            this.showForm = true;
            window.scrollTo({
                top: 0,
                behavior: "smooth",
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
                    "/permissions/save",
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
            this.showForm = !this.showForm;
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
