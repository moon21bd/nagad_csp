<template>
    <div class="card mb-4">
        <div class="card-header">
            <h1 class="title mb-3">
                Customer Information
                <small class="special-message pl-2" v-if="inDNDList">
                    <span v-if="specialMessage">
                        <i class="icon-alert-triangle text-danger"></i>
                        {{ specialMessage }}
                    </span>
                </small>
            </h1>
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
                <div class="verified-user d-flex">
                    <i class="icon-phone-call"></i>
                    <h5>
                        In Call..<span>+88{{ this.callerMobileNo }}</span>
                    </h5>
                </div>

                <!-- <div class="verify-user mr-0 mr-md-3">
                    <input
                        class="form-control"
                        type="tel"
                        v-model="callerMobileNo"
                        name="customer"
                        placeholder="Customer Account No"
                        required
                    />
                </div> -->

                <!-- <a
                    class="btn-prev-tickets ml-auto"
                    :class="{ show: callerPrevTickets }"
                    @click.prevent="togglePrevTickets()"
                >
                    Previous Tickets <i class="icon-arrow-down-circle"></i>
                </a> -->
            </div>
        </div>
        <div class="card-body">
            <ul class="nav nav-pills mb-3">
                <li class="nav-item">
                    <a
                        class="tickets-tabs active mr-2"
                        data-toggle="pill"
                        href="#create-ticket"
                        >Create Ticket</a
                    >
                </li>
                <li class="nav-item">
                    <a
                        class="tickets-tabs"
                        data-toggle="pill"
                        href="#prev-ticket"
                        >Previous Tickets</a
                    >
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="create-ticket">
                    <div class="row">
                        <!-- <div class="col-md-3">
                    <userInfo />
                </div> -->
                        <div class="col-md-10">
                            <form
                                ref="ticketForm"
                                @submit.prevent="handleSubmit"
                            >
                                <div
                                    class="form-row"
                                    v-if="
                                        isNagadSebaOrUddoktaPointCustomerGroup
                                    "
                                >
                                    <div class="col-md-12 form-group">
                                        <label class="control-label"
                                            >Customer Number<sup>*</sup></label
                                        >
                                        <input
                                            class="form-control"
                                            type="tel"
                                            v-model="customerPhoneNumber"
                                            name="customerPhoneNumber"
                                            placeholder="Customer Account No"
                                        />
                                    </div>
                                </div>

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
                                                    :label="
                                                        types.call_type_name
                                                    "
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
                                                :label="
                                                    category.call_category_name
                                                "
                                                :value="category.id"
                                            >
                                            </el-option>
                                        </el-select>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="control-label"
                                            >Service Sub Category<sup
                                                >*</sup
                                            ></label
                                        >

                                        <el-select
                                            class="d-block w-150"
                                            v-model="
                                                ticketInfos.callSubCategoryId
                                            "
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
                                                <h5
                                                    class="sub-title d-flex align-items-center text-danger"
                                                >
                                                    Ticket No:
                                                    {{ fieldsSet.id }}
                                                    <button
                                                        class="btn btn-danger btn-sm ml-auto"
                                                        @click="
                                                            deleteFieldsSet(
                                                                setIndex
                                                            )
                                                        "
                                                        :disabled="
                                                            requiredFieldsSets.length ===
                                                            1
                                                        "
                                                    >
                                                        <i
                                                            class="icon-trash"
                                                        ></i>
                                                        Remove
                                                    </button>
                                                </h5>
                                            </div>
                                            <div
                                                class="col-md-4"
                                                v-for="field in fieldsSet.fields"
                                                :key="field.id"
                                            >
                                                <div
                                                    class="form-group"
                                                    v-if="
                                                        field.input_type ===
                                                        'select'
                                                    "
                                                >
                                                    <label
                                                        class="control-label"
                                                        >{{
                                                            field.input_field_name
                                                        }}</label
                                                    >
                                                    <el-select
                                                        class="d-block w-100"
                                                        v-model="
                                                            ticketInfos
                                                                .requiredField[
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
                                                            >{{
                                                                option
                                                            }}</el-option
                                                        >
                                                    </el-select>
                                                </div>
                                                <div
                                                    class="form-group"
                                                    v-else-if="
                                                        field.input_type ===
                                                        'datetime'
                                                    "
                                                >
                                                    <label
                                                        class="control-label"
                                                        >{{
                                                            field.input_field_name
                                                        }}</label
                                                    >
                                                    <el-date-picker
                                                        class="d-block w-100"
                                                        v-model="
                                                            ticketInfos
                                                                .requiredField[
                                                                field.id
                                                            ]
                                                        "
                                                        type="datetime"
                                                        placeholder="Select date and time"
                                                    />
                                                </div>
                                                <div class="form-group" v-else>
                                                    <label
                                                        class="control-label"
                                                        >{{
                                                            field.input_field_name
                                                        }}</label
                                                    >
                                                    <input
                                                        type="text"
                                                        v-model="
                                                            ticketInfos
                                                                .requiredField[
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
                                        :disabled="
                                            requiredFieldsSets.length >= 3
                                        "
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
                                        ></textarea>
                                    </div>
                                </div>

                                <div
                                    v-if="
                                        serviceTypeConfigs?.is_show_popup_msg ===
                                        'yes'
                                    "
                                    class="ticket-item popupMessages"
                                >
                                    <h2 class="sub-title">
                                        Important Messages
                                    </h2>
                                    <ul class="m-0 pl-3">
                                        <li
                                            class="py-1"
                                            v-for="(
                                                msg, index
                                            ) in popupMessages"
                                            :key="index"
                                        >
                                            {{ msg }}
                                        </li>
                                    </ul>
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
                                            required
                                            @change="handleAttachmentFileUpload"
                                            accept="image/png, image/jpeg, .pdf, .doc, .docx, .xls, .xlsx"
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
                <div class="tab-pane fade" id="prev-ticket">
                    <form action="#">
                        <div class="form-row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input
                                        class="form-control"
                                        type="tel"
                                        v-model="callerMobileNo"
                                        name="customer"
                                        placeholder="Customer Account No"
                                        required
                                    />
                                </div>
                            </div>
                            <div class="col-md-3 form-group">
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
                            <div class="col-md-2">
                                <button
                                    class="btn btn-site"
                                    name="search-tickets"
                                >
                                    <i class="icon-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                    <div>
                        <h2 class="control-label">All Ticket Details</h2>
                        <div class="table-responsive">
                            <table class="table prev-table border rounded">
                                <thead>
                                    <tr>
                                        <th>Date Time</th>
                                        <th>Ticket No</th>
                                        <th>Sub Category</th>
                                        <th>Status</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="prevTickets.length === 0">
                                        <td colspan="5" class="text-center">
                                            No previous data found
                                        </td>
                                    </tr>
                                    <tr
                                        v-else
                                        v-for="ticket in prevTickets"
                                        :key="ticket.ticket_id"
                                    >
                                        <td>
                                            {{
                                                formatDateTime(
                                                    ticket.ticket_created_at
                                                )
                                            }}
                                        </td>
                                        <td>{{ ticket.uuid }}</td>
                                        <td>
                                            {{ ticket.call_sub_category_name }}
                                        </td>
                                        <td>{{ ticket.ticket_status }}</td>
                                        <td class="text-right">
                                            <router-link
                                                class="btn-action btn-edit"
                                                title="Ticket Timeline"
                                                :to="{
                                                    name: 'ticket-timeline',
                                                    params: {
                                                        id: ticket.ticket_id,
                                                    },
                                                }"
                                                ><i class="icon-tickets"></i
                                            ></router-link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div v-if="prevTickets.length && !isLoading">
                        <h2 class="control-label">Last 3 Ticket Details</h2>
                        <div class="table-responsive">
                            <table class="table prev-table border rounded">
                                <thead>
                                    <tr>
                                        <th>Date Time</th>
                                        <th>Ticket No</th>
                                        <th>Sub Category</th>
                                        <th>Status</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="prevTickets.length === 0">
                                        <td colspan="5" class="text-center">
                                            No previous data found
                                        </td>
                                    </tr>
                                    <tr
                                        v-else
                                        v-for="ticket in prevTickets"
                                        :key="ticket.ticket_id"
                                    >
                                        <td>
                                            {{
                                                formatDateTime(
                                                    ticket.ticket_created_at
                                                )
                                            }}
                                        </td>
                                        <td>{{ ticket.uuid }}</td>
                                        <td>
                                            {{ ticket.call_sub_category_name }}
                                        </td>
                                        <td>{{ ticket.ticket_status }}</td>
                                        <td class="text-right">
                                            <router-link
                                                class="btn-action btn-edit"
                                                title="Ticket Timeline"
                                                :to="{
                                                    name: 'ticket-timeline',
                                                    params: {
                                                        id: ticket.ticket_id,
                                                    },
                                                }"
                                                ><i class="icon-tickets"></i
                                            ></router-link>
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
import { formatDateTime } from "../../../utils/common";

export default {
    components: {
        // userInfo,
        noData,
    },
    name: "Tickets",
    data: () => ({
        isLoading: false,
        inDNDList: false,
        specialMessage: "",
        prevTickets: [],
        popupMessages: [],
        fieldSetIdentifier: 1,
        requiredFieldsSets: [],
        callerMobileNo: null,
        customerPhoneNumber: null,
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
        if (this.prevTickets.length === 0) {
            this.fetchPrevTickets(); // Fetch tickets if not already populated
        }
    },
    computed: {
        // Fetch the logged-in user's data from Vuex store
        user() {
            return this.$store.state.auth.user;
        },

        // Check if the group_id is 3 or 4
        isNagadSebaOrUddoktaPointCustomerGroup() {
            return (
                this.user &&
                (this.user.group_id === 3 || this.user.group_id === 5)
            );
        },
    },
    methods: {
        formatDateTime,
        phoneValidationRules(number) {
            const phoneRegex = /^(01)[3456789]{1}\d{8}$/;
            return phoneRegex.test(number) && number.length === 11;
        },
        deleteFieldsSet(setIndex) {
            if (this.requiredFieldsSets.length > 1) {
                this.requiredFieldsSets.splice(setIndex, 1);

                this.requiredFieldsSets.forEach((set, index) => {
                    set.id = index + 1;
                });
            }
        },
        checkDNDStatus() {
            const mobileNo = this.callerMobileNo;
            try {
                if (mobileNo) {
                    axios
                        .get(`/in-dnd/${mobileNo}`)
                        .then((response) => {
                            this.inDNDList = response.data.in_dnd;
                            this.specialMessage = response.data.specialMessage;
                        })
                        .catch((error) => {
                            console.error("Error fetching DND status:", error);
                        });
                }
            } catch (error) {
                console.error("Error fetching DND status:", error);
            }
        },
        // togglePrevTickets() {
        //     this.showPrevTickets = !this.showPrevTickets; // Toggle the view

        //     if (this.showPrevTickets && this.prevTickets.length === 0) {
        //         this.fetchPrevTickets(); // Fetch tickets if not already populated
        //     }
        // },

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
                    // id: this.fieldSetIdentifier,
                    id: this.requiredFieldsSets.length + 1,
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
        /* async getServiceTypeConfig() {
            try {
                const response = await axios.get(
                    `/get-service-type-configs/${this.ticketInfos.callTypeId}/${this.ticketInfos.callCategoryId}/${this.ticketInfos.callSubCategoryId}`
                );
                const serviceConfigs = response.data.data;
                this.serviceTypeConfigs = serviceConfigs;
                this.popupMessages = serviceConfigs.popup_msg_texts
                    ? JSON.parse(serviceConfigs.popup_msg_texts)
                    : [];
            } catch (error) {
                console.error("Error fetching sub categories:", error);
                this.serviceTypeConfigs = {};
            }
        }, */
        async getServiceTypeConfig() {
            try {
                const response = await axios.get(
                    `/get-service-type-configs/${this.ticketInfos.callTypeId}/${this.ticketInfos.callCategoryId}/${this.ticketInfos.callSubCategoryId}`
                );
                const serviceConfigs = response.data.data;

                if (serviceConfigs && typeof serviceConfigs === "object") {
                    this.serviceTypeConfigs = serviceConfigs;
                    // Safely parse popup_msg_texts
                    this.popupMessages = JSON.parse(
                        serviceConfigs.popup_msg_texts || "{}"
                    );
                } else {
                    // Handle unexpected structure
                    console.error(
                        "Unexpected serviceConfigs structure:",
                        serviceConfigs
                    );
                    this.serviceTypeConfigs = {};
                    this.popupMessages = {};
                }
            } catch (error) {
                console.error("Error fetching service type configs:", error);
                this.serviceTypeConfigs = {};
                this.popupMessages = {};
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
        /* validateRequiredFields() {
            let hasValidationError = false;
            const requiredFieldErrors = [];

            this.requiredFieldsSets.forEach((set) => {
                set.fields.forEach((field) => {
                    const fieldValue = this.ticketInfos.requiredField[field.id];
                    if (
                        !fieldValue ||
                        (typeof fieldValue === "string" &&
                            fieldValue.trim() === "")
                    ) {
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
        }, */
        /* validateRequiredFields() {
            let hasValidationError = false;
            const requiredFieldErrors = [];

            // Validate callTypeId
            if (!this.ticketInfos.callTypeId) {
                hasValidationError = true;
                requiredFieldErrors.push('The "Call Type" field is required.');
            }

            // Validate callCategoryId
            if (!this.ticketInfos.callCategoryId) {
                hasValidationError = true;
                requiredFieldErrors.push(
                    'The "Call Category" field is required.'
                );
            }

            // Validate callSubCategoryId
            if (!this.ticketInfos.callSubCategoryId) {
                hasValidationError = true;
                requiredFieldErrors.push(
                    'The "Call Sub-Category" field is required.'
                );
            }

            // Validate requiredField (dynamic form fields)
            this.requiredFieldsSets.forEach((set) => {
                set.fields.forEach((field) => {
                    const fieldValue = this.ticketInfos.requiredField[field.id];
                    if (
                        !fieldValue ||
                        (typeof fieldValue === "string" &&
                            fieldValue.trim() === "")
                    ) {
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
        }, */
        validateRequiredFields() {
            let hasValidationError = false;
            const requiredFieldErrors = [];

            // Validate phone number
            if (this.customerPhoneNumber) {
                if (!this.phoneValidationRules(this.customerPhoneNumber)) {
                    hasValidationError = true;
                    requiredFieldErrors.push(
                        "Invalid phone number format. Must be an 11-digit number starting with '01'."
                    );
                } else {
                    this.callerMobileNo = this.customerPhoneNumber;
                }
            } else if (this.callerMobileNo === null) {
                hasValidationError = true;
                requiredFieldErrors.push("Caller mobile number is required.");
            }

            // Validate callTypeId
            if (!this.ticketInfos.callTypeId) {
                hasValidationError = true;
                requiredFieldErrors.push('The "Call Type" field is required.');
            }

            // Validate callCategoryId
            if (!this.ticketInfos.callCategoryId) {
                hasValidationError = true;
                requiredFieldErrors.push(
                    'The "Call Category" field is required.'
                );
            }

            // Validate callSubCategoryId
            if (!this.ticketInfos.callSubCategoryId) {
                hasValidationError = true;
                requiredFieldErrors.push(
                    'The "Call Sub-Category" field is required.'
                );
            }

            // Validate callTypeId
            if (!this.ticketInfos.comments) {
                hasValidationError = true;
                requiredFieldErrors.push('The "Comment" field is required.');
            }

            // Validate requiredField (dynamic form fields)
            this.requiredFieldsSets.forEach((set) => {
                set.fields.forEach((field) => {
                    const fieldValue = this.ticketInfos.requiredField[field.id];
                    if (
                        !fieldValue ||
                        (typeof fieldValue === "string" &&
                            fieldValue.trim() === "")
                    ) {
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
            /* if (this.customerPhoneNumber) {
                // Validate the phone number
                if (!this.phoneValidationRules(this.customerPhoneNumber)) {
                    this.$showToast(
                        "Invalid phone number format. Must be an 11-digit number starting with '01'.",
                        {
                            type: "error",
                        }
                    );
                    return;
                }

                this.callerMobileNo = this.customerPhoneNumber;
            } else {
                if (this.callerMobileNo === null) {
                    this.$showToast("Caller mobile number needed.", {
                        type: "error",
                    });
                    return;
                }
            } */

            // console.log("customerPhoneNumber", this.customerPhoneNumber);
            // return;

            // Validate required fields
            const { hasValidationError, requiredFieldErrors } =
                this.validateRequiredFields();

            console.log(
                "hasValidationError",
                hasValidationError,
                "requiredFieldErrors",
                requiredFieldErrors
            );
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
            this.customerPhoneNumber = "";
            this.callerMobileNo = null;
            this.requiredFieldErrors = [];
        },
    },
    created() {
        this.callerMobileNo = this.$route.query?.msisdn || null;
    },
};
</script>
<style>
.popupMessages ul li {
    font-size: 14px;
}
h2.control-label {
    font-size: 16px;
}
</style>
