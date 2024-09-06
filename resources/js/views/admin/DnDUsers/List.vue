<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Customer Profiles</h1>
            <router-link
                class="btn btn-site ml-auto"
                :to="{ name: 'dnd-user-create' }"
                ><i class="icon-plus"></i> New
            </router-link>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div v-if="dndUsers.length && !isLoading">
                    <div class="table-responsive">
                        <table id="dataTable" class="table border rounded">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Mobile No.</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in dndUsers" :key="item.id">
                                    <td>{{ item.id }}</td>
                                    <td>{{ item.name }}</td>
                                    <td>{{ item.mobile_no }}</td>
                                    <td>{{ item.message }}</td>
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
                                                name: 'dnd-user-edit',
                                                params: { id: item.id },
                                            }"
                                            ><i class="icon-edit-pen"></i
                                        ></router-link>
                                        <a
                                            class="btn-action btn-trash"
                                            @click.prevent="
                                                deleteDnDUser(item.id)
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

export default {
    components: {
        noData,
    },
    data() {
        return {
            isLoading: false,
            dndUsers: [],
        };
    },

    methods: {
        async deleteDnDUser(id) {
            try {
                if (confirm("Are you sure you want to delete this user?")) {
                    await axios.delete(`/dnd-user/${id}`);
                    this.fetchDnDUsers();
                }
            } catch (error) {
                console.error("Error deleting dnd users:", error);
            }
        },
        async fetchDnDUsers() {
            try {
                this.isLoading = true;
                const response = await axios.get("/dnd-user");
                this.dndUsers = response.data;
            } catch (error) {
                console.error("Error fetching dnd user:", error);
            } finally {
                this.isLoading = false;
            }
        },
        initializeDataTable() {
            // Destroy existing DataTable instance if it exists
            if ($.fn.DataTable.isDataTable("#dataTable")) {
                $("#dataTable").DataTable().destroy();
                $("#dataTable").empty();
            }

            // Initialize DataTable
            $("#dataTable").DataTable({
                order: [[0, "desc"]],
            });
        },
    },
    mounted() {
        this.fetchDnDUsers();
    },
    watch: {
        dndUsers(newValue) {
            if (newValue.length) {
                this.$nextTick(() => {
                    this.initializeDataTable();
                });
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
