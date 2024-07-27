<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Groups</h1>
            <router-link
                class="btn btn-site ml-auto"
                :to="{ name: 'createGroup' }"
                ><i class="icon-plus"></i> New
            </router-link>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div v-if="groups.length && !isLoading">
                    <div class="table-responsive">
                        <table id="dataTable" class="table border rounded">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="{ name, id, status } in groups"
                                    :key="id"
                                >
                                    <td>{{ id }}</td>
                                    <td>{{ name }}</td>
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
                                                name: 'manageGroupRoles',
                                                params: { id },
                                            }"
                                        >
                                            <i class="icon-settings"></i>
                                        </router-link>

                                        <router-link
                                            class="btn-action btn-edit"
                                            :to="{
                                                name: 'editGroup',
                                                params: { id },
                                            }"
                                        >
                                            <i class="icon-edit-pen"></i>
                                        </router-link>

                                        <a
                                            class="btn-action btn-trash"
                                            @click.prevent="deleteGroup(id)"
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
            status: "active",
            groups: [],
        };
    },
    methods: {
        async fetchGroups() {
            try {
                const response = await axios.get("/groups");
                this.groups = response.data;
            } catch (error) {
                console.error("Error fetching groups:", error);
            }
        },
        async deleteGroup(groupId) {
            try {
                if (confirm("Are you sure you want to delete this group?")) {
                    await axios.delete(`/groups/${groupId}`);
                    this.fetchGroups();
                }
            } catch (error) {
                console.error("Error deleting group:", error);
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
        this.fetchGroups();
    },
    watch: {
        groups(newValue) {
            if (newValue.length) {
                this.initializeDataTable();
            }
        },
    },
};
</script>
