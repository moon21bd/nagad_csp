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
                                                name: 'editGroup',
                                                params: { id },
                                            }"
                                            ><i class="icon-edit-pen"></i
                                        ></router-link>
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
export default {
    components: {
        noData,
    },
    data() {
        return {
            isLoading: false,
            status: "active",
        };
    },
    computed: {
        groups() {
            return this.$store.state.groups;
        },
    },
    methods: {
        async deleteGroup(groupId) {
            if (confirm("Are you sure you want to delete this group?")) {
                try {
                    await this.$store.dispatch("deleteGroup", groupId);
                    console.log("Group deleted successfully.");
                } catch (error) {
                    console.error("Error deleting group:", error);
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
        this.$store.dispatch("fetchGroups");
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
