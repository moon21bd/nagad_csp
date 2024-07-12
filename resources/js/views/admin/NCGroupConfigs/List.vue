<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Group Configurations</h1>
            <router-link
                class="btn btn-site ml-auto"
                :to="{ name: 'create-group-config' }"
                ><i class="icon-plus"></i> New
            </router-link>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div v-if="groupConfigs.length && !isLoading">
                    <div class="table-responsive">
                        <table id="dataTable" class="table border rounded">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Access Lists</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="{
                                        group,
                                        id,
                                        access_lists,
                                        status,
                                    } in groupConfigs"
                                    :key="id"
                                >
                                    <td>{{ id }}</td>
                                    <td>
                                        {{ group.name }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-common mr-2 my-1"
                                            v-for="{
                                                access_name,
                                                id,
                                            } in access_lists"
                                            :key="id"
                                        >
                                            {{ access_name }}
                                        </span>
                                    </td>
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
                                                name: 'edit-group-config',
                                                params: { id },
                                            }"
                                            ><i class="icon-edit-pen"></i
                                        ></router-link>
                                        <a
                                            class="btn-action btn-trash"
                                            @click.prevent="confirmDelete(id)"
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
import axios from "axios";
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
            groupConfigs: [],
            // access_lists: [],
        };
    },
    created() {
        this.fetchGroupConfigs();
    },
    methods: {
        fetchGroupConfigs() {
            axios
                .get("/group-configs")
                .then((response) => {
                    this.groupConfigs = response.data;
                })
                .catch((error) => {
                    console.error(
                        "Error fetching group configurations:",
                        error
                    );
                });
        },
        confirmDelete(groupId) {
            if (
                confirm(
                    "Are you sure you want to delete this group configuration?"
                )
            ) {
                axios
                    .delete(`/group-configs/${groupId}`)
                    .then(() => {
                        // Optional: Remove deleted item from local data to reflect changes immediately
                        this.groupConfigs = this.groupConfigs.filter(
                            (item) => item.id !== groupId
                        );
                        console.log(
                            "Group configuration deleted successfully."
                        );
                    })
                    .catch((error) => {
                        console.error(
                            "Error deleting group configuration:",
                            error
                        );
                    });
            }
        },
        initializeDataTable() {
            this.$nextTick(() => {
                $("#dataTable").DataTable();
            });
        },
    },
    watch: {
        groupConfigs(newValue) {
            if (newValue.length) {
                this.initializeDataTable();
            }
        },
    },
};
</script>
