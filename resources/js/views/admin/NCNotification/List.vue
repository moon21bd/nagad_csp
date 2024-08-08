<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title">Notifications</h1>
            <router-link
                class="btn btn-site ml-auto"
                :to="{ name: 'service-type-config-add' }"
                ><i class="icon-plus"></i> New
            </router-link>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div v-if="notifications.length && !isLoading">
                    <div class="table-responsive">
                        <table id="dataTable" class="table border rounded">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Group</th>
                                    <th>Title</th>
                                    <th>Link</th>
                                    <th>Channel</th>
                                    <th>Message</th>
                                    <th>Is Seen</th>
                                    <th>Is Read</th>
                                    <th>CreatedAt</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="notify in notifications"
                                    :key="notify.id"
                                >
                                    <td>{{ notify.id }}</td>
                                    <td>
                                        {{ notify.group?.name || "" }}
                                    </td>
                                    <td>
                                        {{ notify.title || "" }}
                                    </td>
                                    <td>
                                        <a
                                            style="white-space: nowrap"
                                            v-if="notify.link"
                                            :href="notify.link"
                                            class="btn btn-site"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            View Link
                                        </a>
                                        <span v-else>NA</span>
                                    </td>
                                    <td>
                                        {{
                                            capitalizedMessage(
                                                notify.channel
                                            ) || ""
                                        }}
                                    </td>
                                    <td>
                                        {{ notify.message }}
                                    </td>
                                    <td>
                                        {{ notify.is_seen }}
                                    </td>
                                    <td>
                                        {{ notify.is_read }}
                                    </td>
                                    <td>
                                        {{ formatDateTime(notify.created_at) }}
                                    </td>

                                    <td class="text-right">
                                        <a
                                            class="btn-action btn-trash"
                                            @click.prevent="
                                                deleteNotification(notify.id)
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
import { mapState } from "vuex";
import { capitalize, formatDateTime } from "../../../utils/common";
import noData from "../components/noData.vue";

export default {
    components: {
        noData,
    },
    data() {
        return {
            isLoading: false,
            notifications: [],
        };
    },
    computed: {
        ...mapState("auth", ["user"]),
    },

    methods: {
        formatDateTime,
        capitalizedMessage() {
            return capitalize(this.message);
        },
        async fetchNotifications(userId) {
            try {
                const response = await axios.get(
                    `/notifications?user_id=${userId}`
                );
                this.notifications = response.data;
            } catch (error) {
                console.error("Error fetching notifications:", error);
            }
        },
        async deleteNotification(id) {
            try {
                if (
                    confirm(
                        "Are you sure you want to delete this notification?"
                    )
                ) {
                    await axios.delete(`/notifications/${id}`);
                    await this.fetchNotifications();
                }
            } catch (error) {
                console.error("Error deleting notification:", error);
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
        this.fetchNotifications(this.user.id);
    },
    watch: {
        notifications(newValue) {
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
