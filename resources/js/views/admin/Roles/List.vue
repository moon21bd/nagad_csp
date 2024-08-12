<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Roles</h1>
            <router-link
                class="btn btn-site ml-auto"
                :to="{ name: 'roles-create' }"
                ><i class="icon-plus"></i> New
            </router-link>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div v-if="roles.length && !isLoading">
                    <div class="table-responsive">
                        <table id="dataTable" class="table border rounded">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Guard</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="role in roles" :key="role.id">
                                    <td>{{ role.id }}</td>
                                    <td>{{ role.name }}</td>
                                    <td>{{ role.guard_name }}</td>
                                    <td class="text-right">
                                        <router-link
                                            class="btn-action btn-edit"
                                            title="Update permissions"
                                            :to="{
                                                name: 'roles-edit',
                                                params: { id: role.id },
                                            }"
                                            ><i class="icon-settings"></i
                                        ></router-link>
                                        <a
                                            title="Delete Role"
                                            class="btn-action btn-trash"
                                            @click.prevent="deleteRole(role.id)"
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
import noData from "../components/noData.vue";
import axios from "../../../axios";

export default {
    components: {
        noData,
    },
    data() {
        return {
            isLoading: false,
            roles: [],
            dataTable: null,
        };
    },
    methods: {
        async fetchRoles() {
            this.isLoading = true;
            try {
                const response = await axios.get("/roles");
                this.roles = response.data;
            } catch (error) {
                console.error("Error fetching roles:", error);
            } finally {
                this.isLoading = false;
            }
        },
        async deleteRole(roleId) {
            try {
                if (confirm("Are you sure you want to delete this role?")) {
                    await axios.delete(`/role/delete/${roleId}`);
                    this.fetchRoles();
                }
            } catch (error) {
                console.error("Error deleting role:", error);
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
    mounted() {
        this.fetchRoles();
    },
    watch: {
        roles(newValue) {
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
