<template>
    <div class="card mb-4">
        <div class="card-header">
            <h1 class="title mb-3">Customer Information</h1>
            <div class="d-flex align-items-center">
                <!-- do not delete this -->
                <!-- <form action="" class="verify-user mr-0 mr-md-3">
                    <input
                        class="form-control"
                        type="tel"
                        v-model="callerMobileNo"
                        name="customer"
                        placeholder="Customer Account No"
                        required
                    />
                    <button
                        class="btn"
                        data-toggle="modal"
                        data-target="#ticketSuccessPopup"
                    >
                        <i class="icon-search"></i>
                    </button>
                </form> -->
                <div class="verified-user d-flex" :class="{ dnd: !inDNDList }">
                    <i v-if="inDNDList" class="icon-phone-call"></i>
                    <i v-else class="icon-slash text-danger"></i>
                    <h5>
                        In Call..<span>+88{{ this.callerMobileNo }}</span>
                    </h5>
                </div>

                <a
                    class="btn-prev-tickets ml-auto"
                    :class="{ show: callerPrevTickets }"
                    @click.prevent="togglePrevTickets()"
                >
                    Previous Tickets <i class="icon-arrow-down-circle"></i>
                </a>
            </div>
        </div>
        <div class="card-body">
            <div v-if="showPrevTickets">
                <div v-if="prevTickets.length && !isLoading">
                    <div class="table-responsive">
                        <table class="table prev-table border rounded">
                            <thead>
                                <tr>
                                    <th>Date Time</th>
                                    <th>Ticket No</th>
                                    <th>Sub Category</th>
                                    <th>Ticket Status</th>
                                </tr>
                            </thead>
                            <tr v-if="prevTickets.length === 0">
                                <td colspan="4" class="text-center">
                                    No previous data found
                                </td>
                            </tr>
                            <tr
                                v-else
                                v-for="ticket in prevTickets"
                                :key="ticket.id"
                            >
                                <td>{{ ticket.ticket_created_at }}</td>
                                <td>{{ ticket.uuid }}</td>
                                <td>{{ ticket.call_sub_category_name }}</td>
                                <td>{{ ticket.ticket_status }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- <div class="col-md-3">
                    <userInfo />
                </div> -->
                <div class="col-md-10">
                    <h4 class="sub-title mb-2">
                        <i class="icon-tickets text-danger"></i> Create Ticket
                    </h4>
                    <form ref="ticketForm" @submit.prevent="handleSubmit">
                        <div class="form-row">
                            <div class="col-md-4 form-group">
                                <label class="control-label"
                                    >Service Type<sup>*</sup></label
                                >
                                <div class="custom-style">
                                    <el-select
                                        class="d-block w-100"
                                        v-model="ticketInfos.callTypeId"
                                        @change="fetchCategories"
                                        required
                                        filterable
                                        placeholder="Select Type"
                                    >
                                        <el-option
                                            v-for="types in callTypes"
                                            :key="types.id"
                                            :label="types.call_type_name"
                                            :value="types.id"
                                        >
                                        </el-option>
                                    </el-select>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label"
                                    >Service Category<sup>*</sup></label
                                >

                                <el-select
                                    class="d-block w-150"
                                    v-model="ticketInfos.callCategoryId"
                                    @change="fetchSubCategory"
                                    required
                                    filterable
                                    placeholder="Select Category"
                                >
                                    <el-option
                                        v-for="category in callCategories"
                                        :key="category.id"
                                        :label="category.call_category_name"
                                        :value="category.id"
                                    >
                                    </el-option>
                                </el-select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label"
                                    >Service Sub Category<sup>*</sup></label
                                >

                                <el-select
                                    class="d-block w-150"
                                    v-model="ticketInfos.callSubCategoryId"
                                    @change="onCategoryChange"
                                    required
                                    filterable
                                    placeholder="Select Sub Category"
                                >
                                    <el-option
                                        v-for="subCategory in callSubCategories"
                                        :key="subCategory.id"
                                        :label="
                                            subCategory.call_sub_category_name
                                        "
                                        :value="subCategory.id"
                                    >
                                    </el-option>
                                </el-select>
                            </div>
                        </div>

                        <div v-if="requiredFieldsSets.length > 0">
                            <div
                                class="ticket-item"
                                v-for="(
                                    fieldsSet, setIndex
                                ) in requiredFieldsSets"
                                :key="setIndex"
                            >
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <h5 class="sub-title text-danger">
                                            Set No: {{ fieldsSet.id }}
                                        </h5>
                                    </div>
                                    <div
                                        class="col-md-4"
                                        v-for="field in fieldsSet.fields"
                                        :key="field.id"
                                    >
                                        <div
                                            class="form-group"
                                            v-if="field.input_type === 'select'"
                                        >
                                            <label class="control-label">{{
                                                field.input_field_name
                                            }}</label>
                                            <el-select
                                                class="d-block w-100"
                                                v-model="
                                                    ticketInfos.requiredField[
                                                        field.id
                                                    ]
                                                "
                                                required
                                                filterable
                                                placeholder="Select Type"
                                            >
                                                <el-option
                                                    v-for="(
                                                        option, i
                                                    ) in field.input_value"
                                                    :key="i"
                                                    :value="option"
                                                    >{{ option }}</el-option
                                                >
                                            </el-select>
                                        </div>
                                        <div
                                            class="form-group"
                                            v-else-if="
                                                field.input_type === 'datetime'
                                            "
                                        >
                                            <label class="control-label">{{
                                                field.input_field_name
                                            }}</label>
                                            <el-date-picker
                                                class="d-block w-100"
                                                v-model="
                                                    ticketInfos.requiredField[
                                                        field.id
                                                    ]
                                                "
                                                type="datetime"
                                                placeholder="Select date and time"
                                            />
                                        </div>
                                        <div class="form-group" v-else>
                                            <label class="control-label">{{
                                                field.input_field_name
                                            }}</label>
                                            <input
                                                type="text"
                                                v-model="
                                                    ticketInfos.requiredField[
                                                        field.id
                                                    ]
                                                "
                                                class="form-control"
                                                :placeholder="
                                                    'Enter ' +
                                                    field.input_field_name
                                                "
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button
                                type="button"
                                class="btn btn-site mb-3"
                                @click="addFieldsSet"
                                :disabled="requiredFieldsSets.length >= 3"
                            >
                                <i class="icon-submit"></i> New
                            </button>
                        </div>

                        <div class="form-row">
                            <div class="col-md-12 form-group">
                                <label class="control-label"
                                    >Comments<sup>*</sup></label
                                >
                                <textarea
                                    class="form-control"
                                    v-model="ticketInfos.comments"
                                    required
                                ></textarea>
                            </div>
                        </div>

                        <div class="form-row">
                            <div
                                v-if="
                                    serviceTypeConfigs?.is_show_popup_msg ===
                                    'yes'
                                "
                                class="alert alert-danger mt-3"
                            >
                                <p>Important Messages:</p>
                                <ul>
                                    <li
                                        v-for="(msg, index) in popupMessages"
                                        :key="index"
                                    >
                                        {{ msg }}
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="form-row">
                            <div
                                v-if="
                                    serviceTypeConfigs?.is_show_attachment ===
                                    'yes'
                                "
                                class="col-md-6 form-group uploads"
                            >
                                <label class="control-label"
                                    >Attachment<sup>*</sup></label
                                >
                                <input
                                    type="file"
                                    name="is_show_attachment"
                                    @change="handleAttachmentFileUpload"
                                />
                            </div>

                            <!-- <div
                                v-if="
                                    serviceTypeConfigs?.is_verification_check ===
                                    'yes'
                                "
                                class="col-md-4 form-group"
                            >
                                <label class="control-label m-0 mr-3"
                                    >Is Verified</label
                                >
                                <div class="d-flex">
                                    <label class="radio mr-2">
                                        <input
                                            type="radio"
                                            value="yes"
                                            name="is_verified"
                                            v-model="ticketInfos.is_verified"
                                        />
                                        <span class="radio-mark"></span>Yes
                                    </label>
                                    <label class="radio">
                                        <input
                                            type="radio"
                                            value="no"
                                            name="is_verified"
                                            v-model="ticketInfos.is_verified"
                                        />
                                        <span class="radio-mark"></span>No
                                    </label>
                                </div>
                            </div> -->
                        </div>

                        <!-- Error Message Display Section -->
                        <div
                            v-if="requiredFieldErrors.length > 0"
                            class="alert alert-danger"
                        >
                            <ul>
                                <li
                                    v-for="(
                                        error, index
                                    ) in requiredFieldErrors"
                                    :key="index"
                                >
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                        <button class="btn btn-site" type="submit">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div
            class="modal fade"
            id="ticketSuccessPopup"
            tabindex="-1"
            role="dialog"
            data-backdrop="static"
            aria-labelledby="ticketSuccessPopupLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ticketSuccessPopupLabel">
                            Ticket Created
                        </h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <i class="icon-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">{{ modalBody }}</div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal"
                        >
                            Close
                        </button>
                        <!-- <button type="button" class="btn btn-primary">
                            Save changes
                        </button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "../../../axios";
import noData from "../components/noData.vue";
// import userInfo from "./components/userInfo.vue";

export default {
    components: {
        // userInfo,
        noData,
    },
    name: "Tickets",
    data: () => ({
        isLoading: false,
        inDNDList: false,
        showPrevTickets: false,
        prevTickets: [],
        popupMessages: [],
        fieldSetIdentifier: 1,
        requiredFieldsSets: [],
        callerMobileNo: null,
        callerPrevTickets: false,
        requiredFields: [],
        callTypes: [],
        callCategories: [],
        callSubCategories: [],
        serviceTypeConfigs: {},
        requiredFieldErrors: [],
        ticketInfos: {
            callTypeId: null,
            callCategoryId: null,
            callSubCategoryId: null,
            requiredField: {},
            comments: "",
            attachment: "",
            is_verified: "no",
        },
        modalBody: "",
    }),
    mounted() {
        this.fetchCallTypes();
        this.checkDNDStatus();
    },
    methods: {
        checkDNDStatus() {
            const mobileNo = this.callerMobileNo;
            try {
                if (mobileNo) {
                    axios
                        .get(`/in-dnd/${mobileNo}`)
                        .then((response) => {
                            this.inDNDList = response.data.in_dnd;
                        })
                        .catch((error) => {
                            console.error("Error fetching DND status:", error);
                        });
                }
            } catch (error) {
                console.error("Error fetching DND status:", error);
            }
        },
        togglePrevTickets() {
            this.showPrevTickets = !this.showPrevTickets; // Toggle the view

            if (this.showPrevTickets && this.prevTickets.length === 0) {
                this.fetchPrevTickets(); // Fetch tickets if not already populated
            }
        },

        fetchPrevTickets() {
            if (this.callerMobileNo === null) {
                this.$showToast("Caller mobile number needed.", {
                    type: "error",
                });
                return;
            }

            axios
                .get(`/previous-ticket/${this.callerMobileNo}`)
                .then((response) => {
                    this.prevTickets = response.data;

                    this.isLoading = false;

                    // After fetching, assign the data to callerPrevTickets
                    // this.callerPrevTickets = this.prevTickets.length > 0;
                })
                .catch((error) => {
                    console.error("Error fetching previous tickets:", error);
                });
        },
        addFieldsSet() {
            if (this.requiredFieldsSets.length < 3) {
                console.log("this.requiredFields", this.requiredFields);

                const newFields = this.requiredFields.map((field) => ({
                    id: `${field.id}-${this.fieldSetIdentifier}`,

                    input_field_name: field.input_field_name,
                    input_type: field.input_type,
                    input_value: field.input_value || [],
                    value: "", // Initialize value as empty string
                }));

                this.requiredFieldsSets.push({
                    id: this.fieldSetIdentifier,
                    fields: newFields,
                });

                this.fieldSetIdentifier++;

                // Ensure each new field is initialized in ticketInfos
                newFields.forEach((field) => {
                    if (!this.ticketInfos.requiredField[field.id]) {
                        this.$set(this.ticketInfos.requiredField, field.id, "");
                    }
                });
            }
        },

        showPopup(message) {
            this.modalBody = message;
            $("#ticketSuccessPopup").modal("show");
        },
        handleAttachmentFileUpload(event) {
            let reader = new FileReader();
            reader.readAsDataURL(event.target.files[0]);
            reader.onload = () => {
                this.ticketInfos.attachment = reader.result;
            };
        },

        async fetchCallTypes() {
            try {
                const response = await axios.get("/get-service-types");
                this.requiredFields = [];
                this.callSubCategories = [];
                this.ticketInfos.callSubCategoryId = null;
                this.callTypes = response.data;
            } catch (error) {
                console.error("Error fetching call types:", error);
            }
        },
        async fetchCategories() {
            try {
                const response = await axios.get(
                    `/get-category/${this.ticketInfos.callTypeId}`
                );
                this.requiredFields = [];
                this.callSubCategories = [];
                this.ticketInfos.callSubCategoryId = null;
                this.callCategories = response.data;
            } catch (error) {
                console.error("Error fetching categories:", error);
            }
        },
        async fetchSubCategory() {
            try {
                const response = await axios.get(
                    `/call-sub-by-call-cat-id/${this.ticketInfos.callTypeId}/${this.ticketInfos.callCategoryId}`
                );
                this.callSubCategories;
                this.callSubCategories = response.data;
            } catch (error) {
                console.error("Error fetching sub categories:", error);
            }
        },
        async fetchRequiredFieldsByCategory() {
            try {
                const response = await axios.get(
                    `get-required-field-by-sub-cat-id/${this.ticketInfos.callSubCategoryId}`
                );
                return response.data;
            } catch (error) {
                console.error("Error fetching required fields:", error);
                throw error;
            }
        },
        async fetchRequiredFields() {
            try {
                const data = await this.fetchRequiredFieldsByCategory();
                if (data.length) {
                    this.requiredFields = data;
                    this.requiredFieldsSets = [
                        { id: this.fieldSetIdentifier, fields: data },
                    ];
                    this.fieldSetIdentifier++; // Increment identifier for the first set
                    this.generateInputTypes(data);
                }
            } catch (error) {
                console.log(
                    "An error occurred while fetching required fields."
                );
            }
        },
        async getServiceTypeConfig() {
            try {
                const response = await axios.get(
                    `/get-service-type-configs/${this.ticketInfos.callTypeId}/${this.ticketInfos.callCategoryId}/${this.ticketInfos.callSubCategoryId}`
                );
                const serviceConfigs = response.data.data;
                this.serviceTypeConfigs = serviceConfigs;
                this.popupMessages = JSON.parse(serviceConfigs.popup_msg_texts);
            } catch (error) {
                console.error("Error fetching sub categories:", error);
                this.serviceTypeConfigs = {};
            }
        },
        generateInputTypes(value) {
            this.inputTypeValues = value;
            for (let i = 0; i < value.length; i++) {
                if (value[i].input_type === "select") {
                    this.inputTypeValues[i].input_value =
                        value[i].input_value.split(",");
                }
                this.inputTypeValues[i].input_validation =
                    value[i].input_validation.split(",");
            }
        },
        parseRequiredFields(submittingFields) {
            const finalData = {};

            Object.keys(submittingFields).forEach((key) => {
                const [id, set] = key.split("-");

                if (!finalData[id]) {
                    finalData[id] = {};
                }

                if (set) {
                    finalData[id][set] = submittingFields[key];
                } else {
                    finalData[id][1] = submittingFields[key];
                }
            });

            return finalData;
        },
        validateRequiredFields() {
            let hasValidationError = false;
            const requiredFieldErrors = [];

            this.requiredFieldsSets.forEach((set) => {
                set.fields.forEach((field) => {
                    const fieldValue = this.ticketInfos.requiredField[field.id];
                    if (!fieldValue || fieldValue.trim() === "") {
                        hasValidationError = true;
                        requiredFieldErrors.push(
                            `The field "${field.input_field_name}" is required.`
                        );
                    }
                });
            });

            return {
                hasValidationError,
                requiredFieldErrors,
            };
        },
        async handleSubmit() {
            if (this.callerMobileNo === null) {
                this.$showToast("Caller mobile number needed.", {
                    type: "error",
                });
                return;
            }

            // Validate required fields
            const { hasValidationError, requiredFieldErrors } =
                this.validateRequiredFields();

            if (hasValidationError) {
                this.requiredFieldErrors = requiredFieldErrors;
                return;
            }

            try {
                // Track click event
                this.$trackClick(
                    "ticket_create",
                    JSON.stringify(this.ticketInfos),
                    null,
                    null
                );

                const requiredField = this.parseRequiredFields(
                    this.ticketInfos.requiredField
                );

                const payload = {
                    ...this.ticketInfos,
                    callerMobileNo: this.callerMobileNo,
                    requiredField,
                    is_verified: this.ticketInfos.is_verified || "",
                };

                const response = await axios.post("/tickets", payload);

                if (response.data.status === "success") {
                    this.$refs.ticketForm.reset();
                    this.resetForm();
                    this.showPopup(
                        `${response.data.message}. TicketId: ${response.data.data.ticketId}`
                    );
                }
            } catch (error) {
                // Handle error response
                console.error("There was an error submitting the ticket!");
                this.$showToast(
                    "There was an error submitting the ticket form.",
                    {
                        type: "error",
                    }
                );
            }
        },

        async onCategoryChange() {
            await this.fetchRequiredFields();
            await this.getServiceTypeConfig();
        },
        resetForm() {
            this.fieldSetIdentifier = 1;
            this.requiredFieldsSets = [];
            this.ticketInfos = {
                callTypeId: null,
                callCategoryId: null,
                callSubCategoryId: null,
                requiredField: {},
                comments: "",
                attachment: "",
                is_verified: "no",
            };
        },
    },
    created() {
        this.callerMobileNo = this.$route.query?.msisdn || null;
    },
};
</script>
