<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2"
                :to="{ name: 'ticket-index' }"
                ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Edit Ticket</h1>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <form ref="ticketForm" @submit.prevent="handleSubmit">
                            <div class="form-row">
                                <!-- <div class="col-md-3 form-group">
                                    <label class="control-label"
                                        >Ticket ID<sup>*</sup></label
                                    >
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="ticketInfos.id"
                                        name="service_type"
                                        disabled
                                    />
                                </div> -->

                                <div class="col-md-3 form-group">
                                    <label class="control-label"
                                        >Service Type<sup>*</sup></label
                                    >
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="
                                            ticketInfos.call_type.call_type_name
                                        "
                                        name="service_type"
                                        disabled
                                    />
                                </div>

                                <div class="col-md-3 form-group">
                                    <label class="control-label"
                                        >Service Category<sup>*</sup></label
                                    >

                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="
                                            ticketInfos.call_category
                                                .call_category_name
                                        "
                                        name="service_category"
                                        disabled
                                    />
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="control-label"
                                        >Service Sub Category<sup>*</sup></label
                                    >

                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="
                                            ticketInfos.call_sub_category
                                                .call_sub_category_name
                                        "
                                        name="service_sub_category"
                                        disabled
                                    />
                                </div>
                            </div>

                            <div v-if="requiredFields.length > 0">
                                <div class="form-row">
                                    <div
                                        v-for="(field, index) in requiredFields"
                                        :key="field.id"
                                        :class="{
                                            'col-md-4 form-group': true,
                                            'form-row-end':
                                                (index + 1) % 3 === 0,
                                        }"
                                    >
                                        <label
                                            class="control-label"
                                            :for="'field-' + field.id"
                                        >
                                            {{ field.input_field_name
                                            }}<sup>*</sup>
                                        </label>
                                        <input
                                            type="text"
                                            :id="'field-' + field.id"
                                            class="form-control"
                                            :value="field.value"
                                            disabled
                                        />
                                    </div>
                                </div>
                            </div>

                            <div
                                v-if="
                                    ticketInfos.comments &&
                                    ticketInfos.comments.length > 0
                                "
                            >
                                <h2 class="sub-title text-danger">Comments</h2>
                                <div class="comments">
                                    <ul class="list-unstyled">
                                        <li
                                            v-for="comment in ticketInfos.comments"
                                            :key="comment.id"
                                            class="media"
                                        >
                                            <img
                                                class="mr-3"
                                                :src="comment.avatar_url"
                                                alt="User avatar"
                                            />
                                            <div class="media-body">
                                                <h5 class="mt-0 mb-1">
                                                    {{ comment.username }}
                                                    <span>{{
                                                        comment.date_time
                                                    }}</span>
                                                    <strong
                                                        >Nagad - Technology
                                                        Operation
                                                    </strong>
                                                </h5>
                                                <p>{{ comment.comment }}</p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Attachment Section -->
                            <div class="form-row">
                                <div
                                    class="col-md-6 form-group"
                                    v-if="
                                        ticketInfos.attachments &&
                                        ticketInfos.attachments.length
                                    "
                                >
                                    <label class="control-label"
                                        >Attachments</label
                                    >

                                    <div
                                        v-for="(
                                            attachment, index
                                        ) in ticketInfos.attachments"
                                        :key="index"
                                        class="attachment-item"
                                    >
                                        <div
                                            v-if="isImage(attachment.path_url)"
                                        >
                                            <a
                                                :href="attachment.path_url"
                                                target="_blank"
                                            >
                                                <img
                                                    :src="attachment.path_url"
                                                    alt="Attachment"
                                                    class="img-thumbnail"
                                                />
                                            </a>
                                        </div>
                                        <div
                                            v-else-if="
                                                isPDF(attachment.path_url)
                                            "
                                        >
                                            <a
                                                :href="attachment.path_url"
                                                target="_blank"
                                                >View PDF</a
                                            >
                                        </div>
                                        <div
                                            v-else-if="
                                                isDocument(attachment.path_url)
                                            "
                                        >
                                            <a
                                                :href="attachment.path_url"
                                                target="_blank"
                                                >View Document</a
                                            >
                                        </div>
                                        <div v-else>
                                            <a
                                                :href="attachment.path_url"
                                                target="_blank"
                                                >Download Attachment</a
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Comment Section -->

                            <div class="form-row">
                                <div
                                    class="col form-group"
                                    v-for="(comment, index) in ticketComments"
                                    :key="index"
                                >
                                    <label
                                        class="control-label"
                                        :for="'comment-' + index"
                                    >
                                        Comment {{ index + 1 }}
                                    </label>
                                    <textarea
                                        :id="'comment-' + index"
                                        class="form-control"
                                        v-model="comment.text"
                                        placeholder="Enter your comment"
                                    ></textarea>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger mt-2"
                                        @click="removeComment(index)"
                                        :disabled="ticketComments.length <= 1"
                                    >
                                        Remove
                                    </button>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-2">
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary w-100 d-block py-2"
                                        @click="addComment"
                                        :disabled="ticketComments.length >= 5"
                                    >
                                        <i class="icon-plus"></i> New
                                    </button>
                                </div>
                            </div>
                            <div class="dropdown-divider mt-0 mb-3"></div>

                            <div class="form-row">
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Ticket Status<sup>*</sup></label
                                    >
                                    <div class="custom-style">
                                        <el-select
                                            class="d-block w-100"
                                            v-model="selectedStatus"
                                            v-validate="'required'"
                                            filterable
                                            name="ticketStatus"
                                            placeholder="Select Status"
                                        >
                                            <el-option
                                                v-for="status in filteredStatuses"
                                                :key="status.value"
                                                :label="status.label"
                                                :value="status.value"
                                            >
                                            </el-option>
                                        </el-select>
                                        <small
                                            class="text-danger"
                                            v-show="errors.has('ticketStatus')"
                                        >
                                            {{ errors.first("ticketStatus") }}
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Forward Selection -->
                            <label
                                class="checkbox my-3 bg-light text-dark p-3 rounded"
                            >
                                <input
                                    type="checkbox"
                                    v-model="forwardSelected"
                                />
                                <span class="checkmark"></span> Forward Ticket
                            </label>

                            <!-- User/Group Selection -->
                            <div v-if="forwardSelected" class="form-row">
                                <div class="col-md-6 form-group">
                                    <div class="d-flex align-items-center">
                                        <label class="radio mr-3"
                                            ><input
                                                type="radio"
                                                value="user"
                                                v-model="activeSelection"
                                                @change="clearGroupSelection"
                                            /><span class="radio-mark"></span
                                            >Forward to User
                                        </label>
                                        <label class="radio">
                                            <input
                                                type="radio"
                                                value="group"
                                                v-model="activeSelection"
                                                @change="clearUserSelection"
                                            /><span class="radio-mark"></span
                                            >Forward to Group
                                        </label>
                                    </div>
                                    <el-select
                                        v-if="activeSelection === 'user'"
                                        class="mt-3 d-block w-100"
                                        v-model="selectedUser"
                                        filterable
                                        placeholder="Select User"
                                    >
                                        <el-option
                                            v-for="user in users"
                                            :key="user.id"
                                            :label="user.name"
                                            :value="user.id"
                                        >
                                        </el-option>
                                    </el-select>
                                    <el-select
                                        v-if="activeSelection === 'group'"
                                        class="mt-3 d-block w-100"
                                        v-model="selectedGroup"
                                        filterable
                                        placeholder="Select Group"
                                    >
                                        <el-option
                                            v-for="group in groups"
                                            :key="group.id"
                                            :label="group.name"
                                            :value="group.id"
                                        >
                                        </el-option>
                                    </el-select>
                                </div>

                                <div class="col-md-12 form-group">
                                    <label class="control-label"
                                        >Add Forwarding Note<sup>*</sup></label
                                    >
                                    <textarea
                                        v-model="forwardComment"
                                        class="form-control"
                                        id="forwardComment"
                                        rows="3"
                                        placeholder="Add a note about this forwarding"
                                    ></textarea>
                                </div>
                            </div>

                            <!-- Conditionally Disable or Hide Submit Button -->
                            <button
                                v-if="forwardSelected"
                                type="button"
                                class="btn btn-site"
                                @click="forwardTicket"
                                :disabled="!canForward()"
                            >
                                Forward
                            </button>

                            <button
                                v-else
                                class="btn btn-site"
                                type="submit"
                                :disabled="forwardSelected"
                            >
                                Update
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters, mapState } from "vuex";
import axios from "../../../axios";
export default {
    name: "TicketEdit",
    data: () => ({
        isFirstLoad: false,
        authUserId: null,
        ticketId: null,
        isLoading: false,
        selectedStatus: null,
        forwardSelected: false,
        activeSelection: null,
        selectedUser: null,
        selectedGroup: null,
        forwardComment: "",
        users: [],
        groups: [],
        requiredFields: [],
        maxTicketComments: 5,
        serviceTypeConfigs: {},
        filteredStatusList: [],
        ticketInfos: {
            id: null,
            call_type: {
                call_type_name: "",
            },
            call_category: {
                call_category_name: "",
            },
            call_sub_category: {
                call_sub_category_name: "",
            },
            requiredField: {},
            ticket_status: null,
            comments: [],
            attachments: [],
            path_url: "",
        },
        ticketComments: [{ text: "" }],
    }),
    watch: {
        "ticketInfos.ticket_status": function (newStatus) {
            this.updateFilteredStatuses();
        },
    },

    computed: {
        ...mapState("auth", ["user"]),
        ...mapGetters("auth", ["user"]),
        user() {
            return this.$store.getters["auth/user"];
        },
        filteredStatuses() {
            return this.filteredStatusList;
        },
    },
    created() {
        this.authUserId = this.$store.state.auth.user.id;
        this.ticketId = this.$route.params.id;
        this.checkFirstLoad();
        this.checkTicketStatus(this.ticketId);
        this.maxTicketComments = this.maxTicketComments;
        this.fetchUsersAndGroups();
    },
    methods: {
        ...mapActions("auth", ["setUser"]),
        updateFilteredStatuses() {
            const closedStatuses = [
                "CLOSED",
                "CLOSED - REACHED",
                "CLOSED - NOT RECEIVED",
                "CLOSED - NOT CONNECTED",
                "CLOSED - SWITCHED OFF",
                "CLOSED - NOT COOPERATED",
            ];

            switch (this.ticketInfos.ticket_status.toUpperCase()) {
                case "CREATED":
                    this.filteredStatusList = this.ticketInfos.statuses.filter(
                        (status) => status.value.toUpperCase() === "OPENED"
                    );
                    break;
                case "REOPEN":
                case "OPENED":
                    this.filteredStatusList = this.ticketInfos.statuses.filter(
                        (status) => status.value.toUpperCase() === "RESOLVED"
                    );
                    break;
                case "RESOLVED":
                    this.filteredStatusList = this.ticketInfos.statuses.filter(
                        (status) =>
                            status.value.toUpperCase() === "REOPEN" ||
                            closedStatuses.includes(status.value.toUpperCase())
                    );
                    break;
                case "CLOSED":
                case "CLOSED - REACHED":
                case "CLOSED - NOT RECEIVED":
                case "CLOSED - NOT CONNECTED":
                case "CLOSED - SWITCHED OFF":
                case "CLOSED - NOT COOPERATED":
                    this.filteredStatusList = this.ticketInfos.statuses.filter(
                        (status) => status.value.toUpperCase() === "REOPEN"
                    );
                    break;
                default:
                    this.filteredStatusList = this.ticketInfos.statuses;
            }
            return this.filteredStatusList;
        },
        checkFirstLoad() {
            const pageKey = "ticket_page_first_load";
            if (!localStorage.getItem(pageKey)) {
                this.isFirstLoad = true;
                localStorage.setItem(pageKey, "true");

                this.handleFirstLoad();
            }
            console.log("checkFirstLoad called! this is not the first time.");
        },
        handleFirstLoad() {
            console.log(
                "Ticket Timeline Data Added!!",
                localStorage.getItem("ticket_page_first_load")
            );
            this.sendTicketTimelineData();
        },
        clearFirstLoadFlag() {
            localStorage.removeItem("ticket_page_first_load");
        },
        sendTicketTimelineData() {
            axios
                .post(`/ticket/timeline/${this.ticketId}`, {
                    user_id: this.authUserId,
                })
                .then((response) => {
                    console.log("Timeline Data Posted!", response.data);
                })
                .catch((error) => {
                    console.log("Timeline Data Posting Error!!", error.data);
                });
        },
        fetchUsersAndGroups() {
            axios.get("/getActiveUsers").then((response) => {
                console.log("users", response.data);
                this.users = response.data;
            });
            axios.get("/getActiveGroups").then((response) => {
                this.groups = response.data;
            });
        },
        clearUserSelection() {
            this.selectedUser = null;
        },
        clearGroupSelection() {
            this.selectedGroup = null;
        },
        canForward() {
            // Check if the necessary selection is made
            return (
                (this.activeSelection === "user" && this.selectedUser) ||
                (this.activeSelection === "group" && this.selectedGroup)
            );
        },
        forwardTicket() {
            if (this.forwardComment === null) {
                this.$showToast(
                    "Commment is mandatory for forwarding the ticket.",
                    {
                        type: "error",
                    }
                );
                return;
            }

            const payload = {
                forward_type: this.activeSelection,
                forward_to:
                    this.activeSelection === "user"
                        ? this.selectedUser
                        : this.selectedGroup,
                comments: this.forwardComment,
            };

            axios
                .post(`/ticket/forward/${this.ticketId}`, payload)
                .then((response) => {
                    this.$showToast("Ticket forwarded successfully!", {
                        type: "success",
                    });
                    this.$router.push({ name: "ticket-index" });
                })
                .catch((error) => {
                    this.$showToast("Error forwarding ticket.", {
                        type: "error",
                    });
                });
        },
        checkTicketStatus(ticketId) {
            axios
                .get(`/ticket/status/${ticketId}`)
                .then((response) => {
                    if (!response.data.engaged) {
                        /* console.log(
                            "TICKET-STATUS",
                            response.data.engaged,
                            "ticketId",
                            ticketId
                        ); */

                        this.assignTicket(ticketId);
                    } else {
                        if (
                            response.data.assign_to_user_id !== this.authUserId
                        ) {
                            // If the current user is not assigned to the ticket, redirect them and show a message
                            this.$showToast(response.data.message, {
                                type: "error",
                            });
                            this.$router.push({ name: "ticket-index" });
                        } else {
                            console.log(
                                "This ticket is assigned to the current user: ",
                                this.authUserId
                            );
                        }
                    }
                })
                .catch((error) => {
                    this.$showToast(
                        "Something went wrong or This ticket is already engaged by another user.",
                        {
                            type: "error",
                        }
                    );
                    this.$router.push({ name: "ticket-index" });
                });
        },
        assignTicket(ticketId) {
            axios
                .post(`/ticket/assign/${ticketId}`)
                .then((response) => {
                    if (response.data.success && response.data.showAlert) {
                        this.$showToast(
                            "You have successfully taken ownership of this ticket.",
                            {
                                type: "success",
                            }
                        );

                        this.ticketInfos.ticket_status = "OPENED";

                        this.updateFilteredStatuses();
                    }
                })
                .catch((error) => {
                    this.$showToast(
                        "Could not assign the ticket. Maybe ticket assigned to another user.",
                        {
                            type: "error",
                        }
                    );
                });
        },
        fetchTicketInfos() {
            axios
                .get(`/tickets/${this.ticketId}`)
                .then((response) => {
                    const requiredFieldsArr = response.data.required_fields;
                    this.requiredFields = requiredFieldsArr.map((field) => ({
                        id: field.required_field_id,
                        input_field_name: field.required_field_name,
                        value: field.required_field_value,
                    }));

                    this.ticketInfos = {
                        ...response.data,
                        required_fields: this.requiredFields,
                        ticket_status: response.data.ticket_status,
                        attachments: response.data.attachments,
                        comments: response.data.user_comments,
                    };
                })
                .catch((error) => {
                    console.error("Error fetching ticket infos:", error);
                });
        },
        async handleSubmit() {
            this.$validator.validateAll().then(async (validated) => {
                if (validated) {
                    const payload = {
                        ...this.ticketInfos,
                        selectedStatus: this.selectedStatus,
                        comments: this.ticketComments,
                    };

                    try {
                        const response = await axios({
                            method: "PUT",
                            url: `tickets/${this.ticketId}`,
                            data: payload,
                            headers: { "Content-Type": "application/json" },
                        });

                        // clear first time load flag for adding ticket timeline data
                        this.clearFirstLoadFlag();
                        this.isLoading = false;
                        Vue.prototype.$showToast(response.data.message, {
                            type: "success",
                        });
                        this.$router.push({ name: "ticket-index" });
                    } catch (errors) {
                        if (errors.response && errors.response.data.errors) {
                            this.formErrors = errors.response.data.errors;
                        } else {
                            this.formErrors.push(
                                "Failed to update Ticket. Please try again later."
                            );
                        }
                    } finally {
                        this.isLoading = false;
                    }
                }
            });
        },
        addComment() {
            if (this.ticketComments.length < this.maxTicketComments) {
                this.ticketComments.push({ text: "" });
            } else {
                alert(
                    `You can only add up to ${this.maxTicketComments} comments.`
                );
            }
        },
        removeComment(index) {
            if (this.ticketComments.length > 1) {
                this.ticketComments.splice(index, 1);
            } else {
                alert("At least one comment is required.");
            }
        },
        isImage(file) {
            return /\.(jpg|jpeg|png|gif)$/i.test(file);
        },
        isPDF(file) {
            return /\.pdf$/i.test(file);
        },
        isDocument(file) {
            return /\.(doc|docx)$/i.test(file);
        },
        resetForm() {
            this.requiredFields = [];
            this.ticketInfos = {
                callTypeId: null,
                callCategoryId: null,
                callSubCategoryId: null,
                requiredField: {},
                ticketComments: "",
                attachment: "",
            };
        },
    },
    mounted() {
        this.fetchTicketInfos();
    },
};
</script>
