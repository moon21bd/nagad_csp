<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Tickets</h1>
            <router-link
                v-if="canCreateTicket"
                class="btn btn-site ml-auto"
                :to="{ name: 'ticket-create' }"
            >
                <i class="icon-plus"></i> New
            </router-link>
        </div>

        <div class="filter-section mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="statusFilter">Ticket Status</label>
                    <select
                        v-model="filters.status"
                        @change="fetchTickets"
                        class="form-control"
                        id="statusFilter"
                    >
                        <option value="">All</option>
                        <option value="OPENED">Opened</option>
                        <option value="PENDING">Pending</option>
                        <option value="CLOSED">Closed</option>
                        <option value="RESOLVED">Resolved</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="groupFilter">Responsible Groups</label>
                    <el-select
                        v-model="filters.groups"
                        placeholder="Select Groups"
                        multiple
                        @change="fetchTickets"
                    >
                        <el-option
                            v-for="group in groupOptions"
                            :key="group.id"
                            :label="group.name"
                            :value="group.id"
                        >
                        </el-option>
                    </el-select>
                </div>

                <div class="col-md-4">
                    <label class="control-label" for="serviceCategoryFilter"
                        >Service Category</label
                    >

                    <el-select
                        class="form-control"
                        v-model="filters.service_category"
                        @change="fetchTickets"
                        filterable
                        placeholder="Select Service Sub Category"
                    >
                        <el-option value="">All</el-option>
                        <el-option
                            v-for="category in serviceCategories"
                            :key="category.id"
                            :label="category.name"
                            :value="category.id"
                        >
                        </el-option>
                    </el-select>
                </div>
            </div>
            <button class="btn btn-secondary ml-2" @click="resetFilters">
                Reset Filter
            </button>
        </div>

        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div v-if="tickets.length && !isLoading">
                    <div class="table-responsive">
                        <table id="dataTable" class="table border rounded">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Creation Time</th>
                                    <th>Ticket ID</th>
                                    <th>Status</th>
                                    <th>Responsible Groups</th>
                                    <th>Call No</th>
                                    <th>Service Type</th>
                                    <th>Service Category</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, key) in tickets" :key="key">
                                    <td>{{ item.id }}</td>
                                    <td>
                                        {{ formatDateTime(item.created_at) }}
                                    </td>
                                    <td>{{ item.uuid }}</td>
                                    <td
                                        v-html="
                                            renderTicketStatus(
                                                item.ticket_status
                                            )
                                        "
                                    ></td>

                                    <td>
                                        {{ item.responsible_group_names }}
                                    </td>
                                    <td>
                                        {{ item.caller_mobile_no }}
                                    </td>
                                    <td>
                                        {{
                                            item.call_type?.call_type_name || ""
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            item.call_category
                                                ?.call_category_name || ""
                                        }}
                                    </td>
                                    <td class="text-right">
                                        <router-link
                                            v-if="
                                                hasRole('admin|superadmin') ||
                                                hasPermission('ticket-edit')
                                            "
                                            class="btn-action btn-edit"
                                            :to="{
                                                name: 'ticket-edit',
                                                params: { id: item.id },
                                            }"
                                            ><i class="icon-edit-pen"></i
                                        ></router-link>

                                        <router-link
                                            v-if="
                                                hasRole('admin|superadmin') ||
                                                hasPermission('ticket-timeline')
                                            "
                                            class="btn-action btn-edit"
                                            title="Ticket Timeline"
                                            :to="{
                                                name: 'ticket-timeline',
                                                params: { id: item.id },
                                            }"
                                            ><i class="icon-tickets"></i
                                        ></router-link>
                                        <a
                                            v-if="
                                                hasRole('superadmin') ||
                                                hasPermission('ticket-delete')
                                            "
                                            class="btn-action btn-trash"
                                            @click.prevent="
                                                deleteTicket(item.id)
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
import PermissionsComponent from "../../../components/PermissionsComponent.vue";
import { formatDateTime } from "../../../utils/common";
import noData from "../components/noData.vue";

export default {
    components: {
        noData,
    },
    mixins: [PermissionsComponent],
    data() {
        return {
            isLoading: false,
            tickets: [],
            groupOptions: [],
            serviceCategories: [],
            filters: {
                status: "",
                groups: [],
                service_category: "",
            },
        };
    },
    computed: {
        // Check if the user has permission to create tickets
        canCreateTicket() {
            return (
                this.hasRole("admin|superadmin") ||
                this.hasPermission("ticket-create")
            );
        },
    },

    methods: {
        formatDateTime,
        async deleteTicket(id) {
            try {
                if (confirm("Are you sure you want to delete this Ticket?")) {
                    const response = await axios.delete(`/tickets/${id}`);

                    if (response.status === 200) {
                        this.fetchTickets();
                        this.$showToast("Ticket deleted successfully!", {
                            type: "success",
                        });
                    } else {
                        this.$showToast(
                            "Failed to delete the ticket. Please try again.",
                            {
                                type: "error",
                            }
                        );
                    }
                }
            } catch (error) {
                console.error("Error deleting ticket:", error);

                this.$showToast(
                    "An error occurred while deleting the ticket.",
                    {
                        type: "error",
                    }
                );
            }
        },
        async resetFilters() {
            this.startDate = "";
            this.endDate = "";
            this.filters = {
                status: "",
                groups: [],
                service_category: "",
            };
            this.isLoading = true;
            try {
                const response = await axios.get("/tickets");
                this.tickets = response.data;
            } catch (error) {
                console.error("Error resetting filter:", error);
            } finally {
                this.isLoading = false;
            }
        },
        async fetchTickets() {
            try {
                this.isLoading = true;
                const response = await axios.get("/tickets", {
                    params: this.filters, // Send filters to backend
                });
                this.tickets = response.data;
            } catch (error) {
                console.error("Error fetching tickets:", error);
            } finally {
                this.isLoading = false;
            }
        },
        async fetchFilterOptions() {
            try {
                const [groupsResponse, categoriesResponse] = await Promise.all([
                    axios.get("/groups"),
                    axios.get("/call-categories"),
                ]);

                this.groupOptions = groupsResponse.data.map((group) => ({
                    id: group.id,
                    name: group.name,
                }));
                console.log("Filtered groupOptions", this.groupOptions);

                this.serviceCategories = categoriesResponse.data.map(
                    (category) => ({
                        id: category.id,
                        name: `${category.call_type.call_type_name} > ${category.call_category_name}`,
                        type: category.call_type.call_type_name,
                        category: category.call_category_name,
                    })
                );

                console.log(
                    "Filtered serviceCategories",
                    this.serviceCategories
                );
            } catch (error) {
                console.error("Error fetching filter options:", error);
            }
        },
        renderTicketStatus(status) {
            const statusMap = {
                OPENED: { class: "badge-warning", label: "OPENED" },
                PENDING: { class: "badge-danger", label: "PENDING" },
                CLOSED: { class: "badge-success", label: "CLOSED" },
                RESOLVED: { class: "badge-info", label: "RESOLVED" },
            };
            return statusMap[status]
                ? `<span class="badge ${statusMap[status].class}">${statusMap[status].label}</span>`
                : `<span class="badge badge-dark">${status}</span>`;
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
        this.fetchFilterOptions();
        this.fetchTickets();
    },
    watch: {
        tickets(newValue) {
            if (newValue.length) {
                this.initializeDataTable();
            }
        },
    },
};
</script>
<style scoped>
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
.table > tbody > tr > td .badge {
    font-size: 11px !important;
    min-width: 70px;
    padding: 5px 5px 4px;
    line-height: normal;
}
</style>
