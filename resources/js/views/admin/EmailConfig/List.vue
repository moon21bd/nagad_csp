<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Email Configurations</h1>
            <router-link
                class="btn btn-site ml-auto"
                :to="{ name: 'email-config-create' }"
                ><i class="icon-plus"></i> New
            </router-link>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div v-if="emailConfigs.length && !isLoading">
                    <div class="table-responsive">
                        <table id="dataTable" class="table border rounded">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Mailer</th>
                                    <th>Host</th>
                                    <th>Port</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Encryption</th>
                                    <th>From Address</th>
                                    <th>From Name</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in emailConfigs" :key="item.id">
                                    <td>{{ item.id }}</td>
                                    <td>{{ item.mailer }}</td>
                                    <td>{{ item.host }}</td>
                                    <td>{{ item.port }}</td>
                                    <td>{{ item.username }}</td>
                                    <td>{{ item.password }}</td>
                                    <td>{{ item.encryption }}</td>
                                    <td>{{ item.from_address }}</td>
                                    <td>{{ item.from_name }}</td>
                                    <td>
                                        <span
                                            :class="
                                                item.status === 'active'
                                                    ? 'active'
                                                    : 'inactive'
                                            "
                                            class="badge"
                                            >{{ item.status }}</span
                                        >
                                    </td>
                                    <td class="text-right">
                                        <router-link
                                            class="btn-action btn-edit"
                                            :to="{
                                                name: 'email-config-edit',
                                                params: { id: item.id },
                                            }"
                                        >
                                            <i class="icon-edit-pen"></i>
                                        </router-link>

                                        <a
                                            class="btn-action btn-trash"
                                            @click.prevent="
                                                deleteEmailConfig(item.id)
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
import noData from "../components/noData.vue";
import axios from "../../../axios";

export default {
    components: {
        noData,
    },
    data() {
        return {
            isLoading: false,
            emailConfigs: [],
            dataTable: null, // Add reference for DataTable instance
        };
    },
    methods: {
        async fetchEmailConfig() {
            try {
                this.isLoading = true;
                const response = await axios.get("/email-config");
                this.emailConfigs = response.data;
            } catch (error) {
                console.error("Error fetching email configs:", error);
            } finally {
                this.isLoading = false;
                this.$nextTick(() => {
                    this.initializeDataTable();
                });
            }
        },
        async deleteEmailConfig(id) {
            try {
                if (
                    confirm(
                        "Are you sure you want to delete this configuration?"
                    )
                ) {
                    await axios.delete(`/email-config/${id}`);
                    this.fetchEmailConfig();
                }
            } catch (error) {
                console.error("Error deleting email config:", error);
            }
        },
        initializeDataTable() {
            if (this.dataTable) {
                this.dataTable.destroy(); // Destroy existing DataTable instance if any
            }
            this.dataTable = $("#dataTable").DataTable({
                order: [[0, "desc"]],
            });
        },
    },
    mounted() {
        this.fetchEmailConfig();
    },
    beforeDestroy() {
        if (this.dataTable) {
            this.dataTable.destroy(); // Destroy DataTable instance when the component is destroyed
        }
    },
};
</script>
