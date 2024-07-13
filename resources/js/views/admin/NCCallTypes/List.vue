<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Call Types</h1>
            <router-link
                class="btn btn-site ml-auto"
                to="/admin/call-types/create"
            ><i class="icon-plus"></i> New
            </router-link>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt=""/>
            </div>
            <div class="card-body">
                <div v-if="callTypes.length && !isLoading">
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
                                        call_type_name,
                                        id,
                                        status,
                                    } in callTypes"
                                :key="id"
                            >
                                <td>{{ call_type_name }}</td>
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
                                                name: 'call-types-edit',
                                                params: { id },
                                            }"
                                    ><i class="icon-edit-pen"></i
                                    ></router-link>
                                    <a
                                        class="btn-action btn-trash"
                                        @click.prevent="deleteCallType(id)"
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
            callTypes: []
        };
    },

    methods: {
        async deleteCallType(id) {
            try {
                if (confirm("Are you sure you want to delete this call Type?")) {
                    await axios.delete(`/call-types/${id}`);
                    this.fetchCallTypes()
                }

            } catch (error) {
                console.error("Error deleting call type:", error);
            }
        },
        async fetchCallTypes() {
            try {
                const response = await axios.get("/call-types");
                console.log('response', response.data)
                this.callTypes = response.data;
            } catch (error) {
                console.error("Error fetching call types:", error);
            }
        },
        initializeDataTable() {
            this.$nextTick(() => {
                $("#dataTable").DataTable();
            });
        },
    },
    mounted() {
        this.fetchCallTypes()
    },
    watch: {
        callTypes(newValue, oldValue) {
            if (newValue.length) {
                this.initializeDataTable();
            }
        },
    },
};
</script>

