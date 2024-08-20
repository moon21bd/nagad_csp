<template>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="sub-title mb-2">
                        <i class="icon-tickets text-danger"></i> Ticket
                    </h4>
                    <form ref="ticketForm" @submit.prevent="handleSubmit">
                        <div class="form-row">
                            <div class="col-md-3 form-group">
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
                            </div>

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
                                        'form-row-end': (index + 1) % 3 === 0,
                                    }"
                                >
                                    <label
                                        class="control-label"
                                        :for="'field-' + field.id"
                                    >
                                        {{ field.input_field_name }}<sup>*</sup>
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
                        <!-- <div v-else>No required fields available.</div> -->

                        <div class="form-row">
                            <div class="col-md-12 form-group">
                                <label class="control-label"
                                    >Comment<sup>*</sup></label
                                >
                                <textarea
                                    class="form-control"
                                    v-model="ticketInfos.comments"
                                    disabled
                                ></textarea>
                            </div>
                        </div>

                        <!-- Attachment Section -->
                        <div class="form-row">
                            <div class="col-md-4 form-group">
                                <label class="control-label">Attachment</label>
                                <div v-if="ticketInfos.attachment_url">
                                    <div
                                        v-if="
                                            isImage(ticketInfos.attachment_url)
                                        "
                                    >
                                        <a
                                            :href="`${ticketInfos.attachment_url}`"
                                            target="_blank"
                                        >
                                            <img
                                                :src="`${ticketInfos.attachment_url}`"
                                                alt="Attachment"
                                                class="img-thumbnail"
                                            />
                                        </a>
                                    </div>
                                    <div
                                        v-else-if="
                                            isPDF(ticketInfos.attachment_url)
                                        "
                                    >
                                        <a
                                            :href="`${ticketInfos.attachment_url}`"
                                            target="_blank"
                                        >
                                            View PDF
                                        </a>
                                    </div>
                                    <div
                                        v-else-if="
                                            isDocument(
                                                ticketInfos.attachment_url
                                            )
                                        "
                                    >
                                        <a
                                            :href="`${ticketInfos.attachment_url}`"
                                            target="_blank"
                                        >
                                            View Document
                                        </a>
                                    </div>
                                    <div v-else>
                                        <a
                                            :href="`${ticketInfos.attachment_url}`"
                                            target="_blank"
                                        >
                                            Download Attachment
                                        </a>
                                    </div>
                                </div>
                                <div v-else>No attachment available.</div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-4 form-group">
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
                                            v-for="status in ticketInfos.statuses"
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
                        <!-- Comment Section -->
                        <div class="form-row">
                            <div
                                class="col-md-4 form-group"
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
                                    class="btn btn-danger mt-2"
                                    @click="removeComment(index)"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="btn btn-primary"
                            @click="addComment"
                            :disabled="ticketComments.length >= 5"
                        >
                            Add Comment
                        </button>
                        <br />
                        <br />

                        <!-- Forward Selection -->
                        <div class="form-row">
                            <div class="col-md-12 form-group">
                                <label>
                                    <input
                                        type="checkbox"
                                        v-model="forwardSelected"
                                    />
                                    Forward Ticket
                                </label>
                            </div>
                        </div>

                        <!-- User/Group Selection -->
                        <div v-if="forwardSelected" class="form-row">
                            <div class="col-md-6 form-group">
                                <label class="control-label">
                                    <input
                                        type="radio"
                                        value="user"
                                        v-model="activeSelection"
                                        @change="clearGroupSelection"
                                    />
                                    Forward to User
                                </label>
                                <el-select
                                    v-if="activeSelection === 'user'"
                                    class="d-block w-100"
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
                            </div>

                            <div class="col-md-6 form-group">
                                <label class="control-label">
                                    <input
                                        type="radio"
                                        value="group"
                                        v-model="activeSelection"
                                        @change="clearUserSelection"
                                    />
                                    Forward to Group
                                </label>
                                <el-select
                                    v-if="activeSelection === 'group'"
                                    class="d-block w-100"
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
                        </div>

                        <!-- Comment Section -->
                        <div v-if="forwardSelected" class="form-row">
                            <div class="col-md-12 form-group">
                                <label for="forwardComment"
                                    >Add Forwarding Note:<sup>*</sup></label
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
                        <div class="form-row">
                            <button
                                type="button"
                                class="btn btn-warning"
                                @click="forwardTicket"
                                :disabled="!canForward()"
                                v-if="forwardSelected"
                            >
                                Forward
                            </button>
                        </div>
                        <br />
                        <br />

                        <button
                            class="btn btn-site"
                            type="submit"
                            :disabled="forwardSelected"
                        >
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "../../../axios";
import auth from "../../../store/auth";
import { mapActions, mapGetters, mapState } from "vuex";
export default {
    name: "TicketEdit",
    data: () => ({
        ticketId: null,
        isLoading: false,
        selectedStatus: "",
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
            comments: "",
            attachment: "",
            attachment_url: "",
        },
        ticketComments: [{ text: "" }],
    }),
    computed: {
        ...mapState("auth", ["user"]),
        ...mapGetters("auth", ["user"]),
        user() {
            return this.$store.getters["auth/user"];
        },
    },
    created() {
        this.ticketId = this.$route.params.id;
        this.checkTicketStatus(this.ticketId);
        this.maxTicketComments = this.maxTicketComments;
        this.fetchUsersAndGroups();
    },
    methods: {
        ...mapActions("auth", ["setUser"]),
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

            /* if (this.activeSelection === "user" && this.selectedUser) {
                // Logic to forward ticket to selected user
                axios
                    .post(`/ticket/forward/${this.ticketId}`, {
                        userId: this.selectedUser,
                    })
                    .then((response) => {
                        this.$showToast(
                            "Ticket successfully forwarded to user.",
                            { type: "success" }
                        );
                    })
                    .catch((error) => {
                        this.$showToast("Failed to forward ticket to user.", {
                            type: "error",
                        });
                    });
            } else if (this.activeSelection === "group" && this.selectedGroup) {
                // Logic to forward ticket to selected group
                axios
                    .post(`/ticket/forward/${this.ticketId}`, {
                        groupId: this.selectedGroup,
                    })
                    .then((response) => {
                        this.$showToast(
                            "Ticket successfully forwarded to group.",
                            { type: "success" }
                        );
                    })
                    .catch((error) => {
                        this.$showToast("Failed to forward ticket to group.", {
                            type: "error",
                        });
                    });
            } */
        },
        checkTicketStatus(ticketId) {
            axios
                .get(`/ticket/status/${ticketId}`)
                .then((response) => {
                    if (!response.data.engaged) {
                        console.log(
                            "TICKET-STATUS",
                            response.data.engaged,
                            "ticketId",
                            ticketId
                        );

                        this.assignTicket(ticketId);
                    } else {
                        if (
                            response.data.assign_to_user_id !==
                            this.$store.state.auth.user.id
                        ) {
                            // If the current user is not assigned to the ticket, redirect them and show a message
                            this.$showToast(response.data.message, {
                                type: "error",
                            });
                            this.$router.push({ name: "ticket-index" });
                        } else {
                            console.log(
                                "This ticket is assigned to the current user: ",
                                this.$store.state.auth.user.id
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
                    if (response.data.success) {
                        this.$showToast(
                            "You have successfully taken ownership of this ticket.",
                            {
                                type: "success",
                            }
                        );
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
