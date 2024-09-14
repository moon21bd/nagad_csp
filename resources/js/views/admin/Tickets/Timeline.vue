<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                :to="{ name: 'ticket-index' }"
                ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Ticket I’d : {{ ticketId }}</h1>
            <div class="btn btn-site btn-sm ml-auto">
                <i class="icon-check-circle"></i>
                {{ ticket.ticket_status }}
            </div>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <h2 class="sub-title text-danger mb-3">
                            Ticket Timeline
                        </h2>

                        <el-timeline class="m-0 p-0">
                            <el-timeline-item
                                v-for="(activity, index) in timelines"
                                :key="index"
                                :icon="activity.icon"
                                :type="activity.type"
                                :color="activity.color"
                                :size="activity.size"
                                :timestamp="activity.timestamp"
                            >
                                <div>
                                    <strong
                                        >{{ activity.status }} by
                                        {{ activity.username }}</strong
                                    >
                                    <br />
                                    <small>({{ activity.agent_id }})</small>
                                    <!-- <br /> -->
                                    <!-- {{ activity.content }} -->
                                </div>
                            </el-timeline-item>
                        </el-timeline>
                    </div>

                    <div class="col-md-9">
                        <div class="table-responsive">
                            <h2 class="sub-title text-danger">Agent Info</h2>
                            <table class="table prev-table border rounded">
                                <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Agent ID</td>
                                        <td>{{ agentUser.id }}</td>
                                    </tr>
                                    <tr>
                                        <td>Agent Name</td>
                                        <td>
                                            {{ agentUser.employee_name }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>IP Address</td>
                                        <td>
                                            {{ agentUser.ip_address }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Device Name</td>
                                        <td>
                                            {{ agentUser.device_name }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Last Login</td>
                                        <td>
                                            {{ agentUser.last_login }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive">
                            <h2 class="sub-title text-danger">Service Types</h2>
                            <table class="table prev-table border rounded">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Service Type</td>
                                        <td>{{ ticket.call_type }}</td>
                                    </tr>
                                    <tr>
                                        <td>Service Category</td>
                                        <td>{{ ticket.call_category }}</td>
                                    </tr>
                                    <tr>
                                        <td>Service Sub Category</td>
                                        <td>{{ ticket.call_sub_category }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="table-responsive">
                            <h2 class="sub-title text-danger">
                                Required Field
                            </h2>
                            <table class="table prev-table border rounded">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(field, index) in requiredFields"
                                        :key="field.required_field_id"
                                    >
                                        <td>{{ field.required_field_name }}</td>
                                        <td>
                                            {{ field.required_field_value }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div
                            class="alert alert-danger text-center"
                            role="alert"
                        >
                            Tansferred to {{ this.groupNames }}
                        </div>
                        <h2 class="sub-title text-danger">Comments</h2>
                        <div class="comments">
                            <ul class="list-unstyled">
                                <li
                                    v-for="comment in comments"
                                    :key="comment.id"
                                    class="media"
                                >
                                    <img
                                        class="mr-3"
                                        :src="
                                            comment.avatar_url ||
                                            '/images/user-avatar.png'
                                        "
                                        alt="User avatar"
                                    />
                                    <div class="media-body">
                                        <h5 class="mt-0 mb-1">
                                            {{ comment.username }}
                                            <span>{{ comment.date_time }}</span>
                                        </h5>
                                        <p>{{ comment.comment }}</p>
                                    </div>
                                </li>
                            </ul>

                            <!-- <div class="comments-form">
                                <form action="">
                                    <div class="d-flex align-items-center">
                                        <img
                                            class="mr-3"
                                            src="/images/user-avatar.png"
                                            alt="Generic placeholder image"
                                        />
                                        <Textarea
                                            class="form-control"
                                            placeholder="Add a comment…"
                                            col="2"
                                        ></Textarea>
                                    </div>
                                    <div class="text-right">
                                        <button
                                            class="btn btn-send mt-3"
                                            type="button"
                                        >
                                            Send
                                        </button>
                                    </div>
                                </form>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            isLoading: false,
            ticket: {},
            ticketId: null,
            groupNames: "",
            agentUser: {},
            requiredFields: [],
            comments: [],
            timelines: [],
        };
    },
    created() {
        this.ticketId = this.$route.params.id;
        this.fetchTicketTimeline();
    },
    methods: {
        fetchTicketTimeline() {
            this.isLoading = true;
            const ticketId = this.ticketId;

            axios
                .get(`/ticket/${ticketId}/timeline`)
                .then((response) => {
                    const data = response.data.data;
                    this.ticket = data.ticket;
                    this.groupNames = data.group_names;
                    this.agentUser = data.agent_user_info;
                    this.requiredFields = data.required_fields;
                    this.comments = data.ticket.comments;

                    this.timelines = data.timelines.map((timeline) => {
                        let icon = "el-icon-time"; // Default icon
                        let color = "#FF4E4E";

                        switch (timeline.ticket_status) {
                            case "OPENED":
                                icon = "el-icon-folder-opened";
                                break;
                            case "CLOSED":
                                icon = "el-icon-check";
                                break;
                            case "PENDING":
                                icon = "el-icon-loading";
                                break;
                            case "RESOLVED":
                                icon = "el-icon-loading";
                                break;

                            default:
                                icon = "el-icon-time";
                        }

                        return {
                            icon: icon,
                            type:
                                timeline.ticket_status === "CLOSED"
                                    ? "primary"
                                    : "success",
                            color: color,
                            size: "large",
                            timestamp: this.formatTimestamp(
                                timeline.created_at
                            ),
                            status: timeline.ticket_status,
                            username: timeline.username,
                            agent_id: timeline.user_id,
                            content: `${timeline.ticket_status} by ${timeline.username} (${timeline.user_id} - Agent)`,
                        };
                    });
                })
                .catch((error) => {
                    console.error("Error fetching timeline:", error);
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        formatTimestamp(timestamp) {
            const options = {
                year: "numeric",
                month: "short",
                day: "numeric",
                hour: "numeric",
                minute: "numeric",
                hour12: true,
            };

            return new Date(timestamp).toLocaleDateString(undefined, options);
        },
    },
};
</script>
<style scoped>
.btn-site {
    background: #16a085;
    cursor: default !important;
}
.btn-send {
    background: #2c3e50;
    color: #fff;
}
</style>
