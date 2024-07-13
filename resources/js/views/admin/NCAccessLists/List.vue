<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Access Lists</h1>
            <router-link
                class="btn btn-site ml-auto"
                to="/admin/access-lists/create"
            ><i class="icon-plus"></i> New
            </router-link>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt=""/>
            </div>
            <div class="card-body">
                <div v-if="accessLists.length && !isLoading">
                    <div class="table-responsive">
                        <table id="dataTable" class="table border rounded">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr
                                v-for="{
                                        access_name,
                                        id,
                                        status,
                                    } in accessLists"
                                :key="id"
                            >
                                <td>{{ access_name }}</td>
                                <td>
                                        <span
                                            :class="
                                                status === 'active'
                                                    ? 'active'
                                                    : 'inactive'
                                            "
                                            class="badge"
                                        >{{ status }}</span
                                        >
                                </td>
                                <td class="text-right">
                                    <router-link
                                        class="btn-action btn-edit"
                                        :to="{
                                                name: 'edit-access',
                                                params: { id },
                                            }"
                                    ><i class="icon-edit-pen"></i
                                    ></router-link>
                                    <a
                                        class="btn-action btn-trash"
                                        @click.prevent="deleteAccess(id)"
                                    >
                                        <i class="icon-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <no-data v-else/>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "../../../axios";
import "datatables.net-dt/css/jquery.dataTables.min.css";
import "datatables.net-dt/js/dataTables.dataTables";
import noData from "../components/noData.vue";

export default {
    components: {
        noData,
    },
    data() {
        return {
            accessLists: [],
            isLoading: false,
            status: "active",
        };
    },
    methods: {
        async getLists() {
            this.isLoading = true;
            try {
                const response = await axios.get("/access-lists");
                this.accessLists = response.data;
            } catch (error) {
                console.error("Error fetching access lists:", error);
            } finally {
                this.isLoading = false;
            }
        },
        async deleteAccess(id) {
            if (confirm("Are you sure you want to delete this access list?")) {
                try {
                    await axios.delete(`/access-lists/${id}`);
                    this.accessLists = this.accessLists.filter(
                        (access) => access.id !== id
                    );
                } catch (error) {
                    console.error("Error deleting access list:", error);
                }
            }
        },
        initializeDataTable() {
            this.$nextTick(() => {
                $("#dataTable").DataTable();
            });
        },
    },
    created() {
        this.getLists();
    },
    watch: {
        accessLists(newValue, oldValue) {
            if (newValue.length) {
                this.initializeDataTable();
            }
        },
    },
};
</script>
