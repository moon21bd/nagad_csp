<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Tickets</h1>
            <router-link
                v-if="
                    hasRole('admin|superadmin') ||
                    hasPermission('ticket-create')
                "
                class="btn btn-site ml-auto"
                :to="{ name: 'ticket-create' }"
                ><i class="icon-plus"></i> New
            </router-link>
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
                                    <td>
                                        <!-- <span
                                            :class="
                                                item.ticket_status === 'OPENED'
                                                    ? 'active'
                                                    : 'inactive'
                                            "
                                            class="badge"
                                            >{{ item.ticket_status }}</span
                                        > -->
                                        <span
                                            v-if="
                                                item.ticket_status === 'OPENED'
                                            "
                                            class="badge badge-warning"
                                            >{{ item.ticket_status }}</span
                                        >
                                        <span
                                            v-else-if="
                                                item.ticket_status === 'PENDING'
                                            "
                                            class="badge badge-danger"
                                            >{{ item.ticket_status }}</span
                                        >
                                        <span
                                            v-else-if="
                                                item.ticket_status === 'CLOSED'
                                            "
                                            class="badge badge-success"
                                            >{{ item.ticket_status }}</span
                                        >
                                        <span
                                            v-else-if="
                                                item.ticket_status ===
                                                'RESOLVED'
                                            "
                                            class="badge badge-info"
                                            >{{ item.ticket_status }}</span
                                        >
                                        <span v-else class="badge badge-dark">{{
                                            item.ticket_status
                                        }}</span>
                                    </td>
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
        };
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
        async fetchTickets() {
            try {
                this.isLoading = true;
                const response = await axios.get("/tickets");
                console.log("response", response);
                this.tickets = response.data;
            } catch (error) {
                console.error("Error fetching tickets:", error);
            } finally {
                this.isLoading = false;
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
