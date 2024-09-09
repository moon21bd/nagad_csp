<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Bulk Tickets Status Updates</h1>
            <router-link
                class="btn btn-site ml-auto"
                :to="{ name: 'bulk-tickets-status-update-create' }"
                ><i class="icon-plus"></i> New
            </router-link>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div v-if="bulkTicketsStatusUpdates.length && !isLoading">
                    <div class="table-responsive">
                        <table id="dataTable" class="table border rounded">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Lot</th>
                                    <th>Excel</th>
                                    <th>Created By</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="{
                                        id,
                                        lot,
                                        excel_file_url,
                                        created_by_username,
                                    } in bulkTicketsStatusUpdates"
                                    :key="id"
                                >
                                    <td>{{ id }}</td>
                                    <td>{{ lot }}</td>
                                    <td>
                                        <a
                                            v-if="excel_file_url"
                                            :href="excel_file_url"
                                            target="_blank"
                                            class="text-decoration-underline"
                                            >Download</a
                                        >
                                    </td>
                                    <td>{{ created_by_username }}</td>

                                    <td class="text-right">
                                        <a
                                            class="btn-action btn-trash"
                                            @click.prevent="
                                                deleteBulkTicket(id)
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
import axios from "../../../axios";
import noData from "../components/noData.vue";

export default {
    components: {
        noData,
    },
    data() {
        return {
            isLoading: false,
            bulkTicketsStatusUpdates: [],
            dataTable: null,
        };
    },
    methods: {
        async fetchbulkTicketsStatusUpdates() {
            try {
                this.isLoading = true;
                const response = await axios.get("/bulk-ticket-statuses");
                this.bulkTicketsStatusUpdates = response.data;
                this.initializeOrUpdateDataTable();
            } catch (error) {
                console.error(
                    "Error fetching bulk tickets upload data:",
                    error
                );
            } finally {
                this.isLoading = false;
            }
        },
        async deleteBulkTicket(id) {
            try {
                if (confirm("Are you sure you want to delete this data?")) {
                    await axios.delete(`/bulk-ticket-statuses/${id}`);
                    this.fetchbulkTicketsStatusUpdates(); // Re-fetch data after deletion
                }
            } catch (error) {
                console.error("Error deleting bulk ticket:", error);
            }
        },
        initializeOrUpdateDataTable() {
            this.$nextTick(() => {
                if (this.dataTable) {
                    // Destroy the existing DataTable before re-initializing
                    this.dataTable.destroy();
                }
                // Initialize DataTable
                this.dataTable = $("#dataTable").DataTable({
                    data: this.bulkTicketsStatusUpdates, // Use the correct data array
                    columns: [
                        { data: "id" },
                        { data: "lot" },
                        {
                            data: "excel_file_url",
                            render: function (data) {
                                return data
                                    ? `<a href="${data}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="icon-download mr-1"></i>Download</a>`
                                    : "";
                            },
                        },
                        { data: "created_by_username" },
                        {
                            data: null,
                            className: "text-right",
                            render: function (data, type, row) {
                                return `
                                    <a class="btn-action btn-trash" data-id="${row.id}">
                                        <i class="icon-trash"></i>
                                    </a>
                                `;
                            },
                        },
                    ],
                    order: [[0, "desc"]],
                });

                // Attach event listener for delete buttons
                $("#dataTable").on("click", ".btn-trash", (event) => {
                    const id = $(event.currentTarget).data("id");
                    this.deleteBulkTicket(id);
                });
            });
        },
    },
    mounted() {
        this.fetchbulkTicketsStatusUpdates();
    },
    beforeDestroy() {
        if (this.dataTable) {
            this.dataTable.destroy();
            this.dataTable = null;
        }
    },
};
</script>
