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
                                    <th>Name</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="{ name, id } in roles" :key="id">
                                    <td>{{ name }}</td>
                                    <td class="text-right">
                                        <router-link
                                            class="btn-action btn-edit"
                                            :to="{
                                                name: 'roles-assign-permissions',
                                                params: { id },
                                            }"
                                            ><i class="icon-settings"></i
                                        ></router-link>
                                        <router-link
                                            class="btn-action btn-edit"
                                            :to="{
                                                name: 'roles-edit',
                                                params: { id },
                                            }"
                                            ><i class="icon-edit-pen"></i
                                        ></router-link>
                                        <a
                                            class="btn-action btn-trash"
                                            @click.prevent="deleteRole(id)"
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
        };
    },
    methods: {
        async fetchRoles() {
            try {
                const response = await axios.get("/roles");
                // console.log("response", response);
                this.roles = response.data;
            } catch (error) {
                console.error("Error fetching roles:", error);
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
                $("#dataTable").DataTable();
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
