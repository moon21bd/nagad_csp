<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Tickets</h1>
            <router-link
                v-if="
                    hasRole('admin|superadmin|owner') ||
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
                                    <td>
                                        {{ formatDateTime(item.created_at) }}
                                    </td>
                                    <td>{{ item.uuid }}</td>
                                    <td>
                                        <span
                                            :class="
                                                item.ticket_status === 'OPEN'
                                                    ? 'active'
                                                    : 'inactive'
                                            "
                                            class="badge"
                                            >{{ item.ticket_status }}</span
                                        >
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
                                                hasRole(
                                                    'admin|superadmin|owner'
                                                ) ||
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
                                                hasRole(
                                                    'admin|superadmin|owner'
                                                ) ||
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
                                            @click.prevent="delete item.id"
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
        async delete(id) {
            try {
                if (confirm("Are you sure you want to delete this Ticket?")) {
                    await axios.delete(`/tickets/${id}`);
                    this.fetchTickets();
                }
            } catch (error) {
                console.error("Error deleting user:", error);
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
