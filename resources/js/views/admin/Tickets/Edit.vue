<template>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-9">
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
                        <div v-else>No required fields available.</div>

                        <div class="form-row">
                            <div class="col-md-12 form-group">
                                <label class="control-label"
                                    >Comments<sup>*</sup></label
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

                            <!-- <button
                                type="button"
                                class="btn btn-secondary ml-2"
                                @click="nextComment"
                                :disabled="comments.length >= 5"
                            >
                                Next Comment
                            </button> -->
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

                        <button class="btn btn-site" type="submit">
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

export default {
    name: "TicketEdit",
    data: () => ({
        id: null,
        isLoading: false,
        selectedStatus: "",
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

    created() {
        this.id = this.$route.params.id;
        this.maxTicketComments = this.maxTicketComments;
    },
    methods: {
        fetchTicketInfos() {
            axios
                .get(`/tickets/${this.id}`)
                .then((response) => {
                    console.log("API response:", response.data);

                    const requiredFieldsArray = JSON.parse(
                        response.data.required_fields
                    );

                    this.requiredFields = requiredFieldsArray.map((field) => ({
                        id: field.id,
                        input_field_name: field.field,
                        value: field.value,
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
            console.log("handleSubmit Called");
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
                            url: `tickets/${this.id}`,
                            data: payload,
                            headers: { "Content-Type": "application/json" },
                        });

                        this.isLoading = false;
                        console.log("response-tick", response);
                        Vue.prototype.$showToast(response.data.message, {
                            title: response.data.message,
                            toaster: `b-toaster-top-right`,
                            variant: "success",
                        });
                        this.$router.push({ name: "ticket-index" });
                    } catch (errors) {
                        console.log(
                            "err.response",
                            errors.response.data.errors
                        );
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
        nextComment() {
            if (this.ticketComments.length < this.maxTicketComments) {
                this.addComment();
                this.$nextTick(() => {
                    const nextCommentIndex = this.ticketComments.length - 1;
                    const nextCommentElement = document.getElementById(
                        `comment-${nextCommentIndex}`
                    );
                    if (nextCommentElement) {
                        nextCommentElement.focus();
                    }
                });
            } else {
                alert(
                    `You can only add up to ${this.maxTicketComments} comments.`
                );
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
