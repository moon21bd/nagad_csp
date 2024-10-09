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

        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div
                    class="filter-tickets d-flex flex-wrap flex-md-nowrap mb-3"
                >
                    <el-select
                        v-model="filters.status"
                        @change="fetchTickets"
                        filterable
                        id="statusFilter"
                        placeholder="Ticket Status"
                    >
                        <el-option
                            v-for="status in statuses"
                            :key="status.value"
                            :label="status.label"
                            :value="status.value"
                        >
                        </el-option>
                    </el-select>
                    <el-select
                        v-model="filters.groups"
                        placeholder="Select Responsible Groups"
                        multiple
                        collapse-tags
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
                    <el-select
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

                    <el-select
                        v-model="filters.created_by"
                        @change="fetchTickets"
                        name="created_by"
                        placeholder="Select Created By"
                    >
                        <el-option value="">All</el-option>
                        <el-option
                            v-for="user in users"
                            :key="user.id"
                            :label="user.name"
                            :value="user.id"
                        >
                        </el-option>
                    </el-select>

                    <el-select
                        v-model="filters.ticket_source"
                        @change="fetchTickets"
                        name="ticket_source"
                        placeholder="Ticket Source"
                    >
                        <el-option value="">All</el-option>
                        <el-option
                            v-for="source in ticketSource"
                            :key="source.value"
                            :label="source.label"
                            :value="source.value"
                        >
                        </el-option>
                    </el-select>

                    <label class="checkbox text-nowrap"
                        ><input
                            type="checkbox"
                            name="my-tickets"
                            id="my-tickets"
                            v-model="filters.my_tickets"
                            @change="fetchTickets"
                        />
                        <span class="checkmark"></span>
                        My Tickets
                    </label>
                    <button
                        class="btn btn-site bg-dark ml-auto text-nowrap"
                        @click="resetFilters"
                    >
                        Reset Filter
                    </button>
                </div>
                <div v-if="tickets.length && !isLoading">
                    <div class="table-responsive">
                        <table id="dataTable" class="table border rounded">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Creation Time</th>
                                    <th>TicketID</th>
                                    <th>Creator</th>
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
                                    <td>
                                        <router-link
                                            :to="{
                                                name: 'ticket-timeline',
                                                params: { id: item.id },
                                            }"
                                            class="text-nowrap btn btn btn-sm btn-outline-danger"
                                            title="Ticket Timeline"
                                        >
                                            <i class="icon-clock"></i>
                                            {{ item.uuid }}
                                        </router-link>
                                    </td>

                                    <td class="text-nowrap">
                                        {{ item.creator?.name || "N/A" }}
                                    </td>
                                    <td
                                        v-html="
                                            renderTicketStatus(
                                                item.ticket_status
                                            )
                                        "
                                    ></td>

                                    <td>
                                        <div class="btn-group">
                                            <button
                                                type="button"
                                                class="btn btn btn-sm btn-outline-secondary dropdown-toggle"
                                                data-toggle="dropdown"
                                                aria-haspopup="true"
                                                aria-expanded="false"
                                            >
                                                Groups
                                                <i class="icon-more"></i>
                                            </button>
                                            <div
                                                class="dropdown-menu p-3 text-wrap dropdown-menu-right"
                                            >
                                                {{
                                                    item.responsible_group_names
                                                        ? item.responsible_group_names
                                                        : "N/A"
                                                }}
                                            </div>
                                        </div>
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
            statuses: [],
            filters: {
                status: "",
                groups: [],
                service_category: "",
                my_tickets: null,
                created_by: null,
                ticket_source: null,
            },
            users: [],
            ticketSource: [],
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
        async fetchUsers() {
            try {
                const response = await axios.get("/users");
                this.users = response.data.data;
            } catch (error) {
                console.error("Error fetching users:", error);
                this.users = [];
            }
        },
        fetchTicketStatuses() {
            axios
                .get("/ticket/statuses")
                .then((response) => {
                    this.statuses = response.data.statuses;
                })
                .catch((error) => {
                    console.error("Error fetching ticket statuses:", error);
                });
        },
        fetchTicketSources() {
            axios
                .get("/ticket/sources")
                .then((response) => {
                    console.log("response.data", response.data.sources);
                    this.ticketSource = response.data.sources;
                })
                .catch((error) => {
                    console.error("Error fetching ticket sources:", error);
                });
        },
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
                my_tickets: null,
                created_by: null,
                ticket_source: null,
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
                    params: {
                        ...this.filters,
                        my_tickets: this.filters.my_tickets
                            ? this.$store.state.auth.user.id
                            : null,
                    },
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

                this.serviceCategories = categoriesResponse.data.map(
                    (category) => ({
                        id: category.id,
                        name: `${category.call_type.call_type_name} > ${category.call_category_name}`,
                        type: category.call_type.call_type_name,
                        category: category.call_category_name,
                    })
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
        this.fetchTicketStatuses();
        this.fetchUsers();
        this.fetchTicketSources();
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
.table > tbody > tr > td .badge {
    font-size: 11px !important;
    min-width: 70px;
    padding: 5px 5px 4px;
    line-height: normal;
}
.table.dataTable > thead > tr > th,
.table.dataTable > tbody > tr > td {
    white-space: nowrap;
}
.table > thead > tr > th:last-child,
.table > tbody > tr > td:last-child {
    white-space: nowrap;
    position: relative;
    overflow: visible;
}
.table > thead > tr > th:last-child {
    background: #fff9f9;
}
.dropdown-menu {
    color: #b1b5b9;
    font-size: 14px;
    box-shadow: 0 0 18px -10px rgba(0, 0, 0, 0.15) !important;
}
.dropdown-menu::before {
    content: "";
    height: 10px;
    width: 10px;
    background: white;
    position: absolute;
    right: 10px;
    top: -5px;
    border-radius: 3px 0px 0px 0px;
    transform: rotate(45deg);
}
.dropdown-toggle::after {
    display: none;
}
.dropdown-item {
    padding: 0.5rem 1rem;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    line-height: 1;
    gap: 3px;
}
.dropdown-item i {
    font-size: 16px;
}
</style>
