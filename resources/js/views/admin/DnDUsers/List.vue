<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">DnD Users</h1>
            <router-link
                class="btn btn-site ml-auto"
                :to="{ name: 'dnduser-create' }"
                ><i class="icon-plus"></i> New
            </router-link>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div v-if="callTypes.length && !isLoading">
                    <div class="table-responsive">
                        <table id="dataTable" class="table border rounded">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Mobile No.</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="{
                                        dnd_username,
                                        id,
                                        mobile_no,
                                    } in dndUsers"
                                    :key="id"
                                >
                                    <td>{{ id }}</td>
                                    <td>{{ dnd_username }}</td>
                                    <td>{{ mobile_no }}</td>

                                    <td class="text-right">
                                        <router-link
                                            class="btn-action btn-edit"
                                            :to="{
                                                name: 'dnduser-edit',
                                                params: { id },
                                            }"
                                            ><i class="icon-edit-pen"></i
                                        ></router-link>
                                        <a
                                            class="btn-action btn-trash"
                                            @click.prevent="deleteDnDUser(id)"
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
            status: "active",
            callTypes: [],
        };
    },

    methods: {
        async deleteDnDUser(id) {
            try {
                if (confirm("Are you sure you want to delete this user?")) {
                    await axios.delete(`/dnduser/${id}`);
                    this.fetchDnDUsers();
                }
            } catch (error) {
                console.error("Error deleting dnd users:", error);
            }
        },
        async fetchDnDUsers() {
            try {
                const response = await axios.get("/dndusers");
                this.DnDUsers = response.data;
            } catch (error) {
                console.error("Error fetching dnd user:", error);
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
        this.fetchDnDUsers();
    },
    watch: {
        DnDUsers(newValue) {
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
