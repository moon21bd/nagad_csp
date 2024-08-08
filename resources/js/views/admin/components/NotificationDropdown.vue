<template>
    <li class="nav-item dropdown no-arrow nav-notify mx-1">
        <a
            class="nav-link dropdown-toggle"
            href="#"
            id="alertsDropdown"
            role="button"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
        >
            <i class="icon-bell"></i>
            <!-- Counter - Alerts -->
            <span class="badge badge-counter">{{ notifications.length }}</span>
        </a>
        <!-- Dropdown - Alerts -->
        <div
            class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
            aria-labelledby="alertsDropdown"
        >
            <h6 class="dropdown-header px-3">Notification Center</h6>
            <a
                v-for="notification in notifications"
                :key="notification.id"
                class="dropdown-item px-3 d-flex align-items-center"
                :href="notification.link"
            >
                <div class="mr-3 d-none">
                    <div
                        class="icon-circle"
                        :class="iconClass(notification.channel)"
                    >
                        <i
                            :class="icon(notification.channel)"
                            class="text-white"
                        ></i>
                    </div>
                </div>
                <div>
                    <div class="small text-gray-500">
                        {{ timeAgo(notification.created_at) }}
                    </div>
                    <span class="nav-notify-heading">{{
                        notification.message
                    }}</span>
                </div>
            </a>
            <a
                v-if="notifications.length === 0"
                class="dropdown-item text-center small text-gray-500"
                href="#"
            >
                No notifications
            </a>

            <router-link
                class="justify-content-center align-items-center p-2 d-flex small text-gray-500text-center small text-gray-500"
                :to="{
                    name: 'notification-list',
                }"
                >Show All Notifications <i class="ml-1 icon-arrow-right"></i
            ></router-link>
        </div>
    </li>
</template>

<script>
import { mapState } from "vuex";
import axios from "../../../axios";
import { timeAgo } from "../../../utils/common";

export default {
    name: "NotificationDropdown",
    data() {
        return {
            notifications: [],
        };
    },
    computed: {
        ...mapState("auth", ["user"]),
    },
    created() {
        if (this.user && this.user.id) {
            this.fetchNotifications(this.user.id);
        }
    },
    methods: {
        timeAgo,
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
        icon(channel) {
            switch (channel) {
                case "sms":
                    return "fas fa-sms";
                case "email":
                    return "fas fa-envelope";
                case "web":
                    return "fas fa-globe";
                case "others":
                    return "fas fa-info-circle";
                default:
                    return "fas fa-bell";
            }
        },
        iconClass(channel) {
            switch (channel) {
                case "sms":
                    return "bg-primary";
                case "email":
                    return "bg-success";
                case "web":
                    return "bg-warning";
                case "others":
                    return "bg-info";
                default:
                    return "bg-secondary";
            }
        },
    },
};
</script>
