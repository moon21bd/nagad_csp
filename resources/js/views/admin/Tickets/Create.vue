<template>
    <div class="card mb-4">
        <div class="card-header">
            <h1 class="title mb-3">
                Customer Information

                <small
                    class="special-message pl-2 text-danger font-weight-bold"
                    v-if="inDNDList"
                >
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
            </div>
        </div>
        <div class="card-body">
            <ul class="nav nav-pills mb-3">
                <li class="nav-item">
                    <a
                        class="tickets-tabs active mr-2"
                        data-toggle="pill"
                        href="#create-ticket"
                        @click="resetFilter"
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
                                                        @change="
                                                            formatDateTimeTest(
                                                                field.id
                                                            )
                                                        "
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
                                            >Attachment</label
                                        >
                                        <input
                                            type="file"
                                            name="is_show_attachment"
                                            @change="handleAttachmentFileUpload"
                                            accept="image/png, image/jpeg, .pdf, .doc, .docx, .xls, .xlsx"
                                        />
                                    </div>
                                </div>

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
                    <form>
                        <div class="form-row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input
                                        class="form-control"
                                        type="tel"
                                        v-model="searchCallerMobileNo"
                                        name="searchCallerMobileNo"
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
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button
                                    class="btn btn-site text-nowrap"
                                    name="search-tickets"
                                    @click.prevent="searchTickets"
                                >
                                    <i class="icon-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                    <div>
                        <h2 class="control-label">All Ticket Details</h2>
                        <div class="table-responsive">
                            <table
                                id="dataTable"
                                class="table prev-table border rounded"
                            >
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
                    <div class="modal-header border-0">
                        <button
                            type="button"
                            class="close text-danger"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <i class="icon-close-circle"></i>
                        </button>
                    </div>
                    <div class="modal-body ticket-success">
                        <div class="ticket-success-icon">
                            <i class="icon-check"></i>
                        </div>

                        <h3 class="sub-title mb-3">
                            Ticket Created Successfully
                        </h3>
                        <p>
                            {{ modalBody }}
                        </p>
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
import { debounce } from "lodash";

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
        searchCallerMobileNo: null,
        customerPhoneNumber: null,
        callerPrevTickets: false,
        selectedStatus: null,
        requiredFields: [],
        callTypes: [],
        callCategories: [],
        callSubCategories: [],
        serviceTypeConfigs: {},
        requiredFieldErrors: [],
        isPrevTicketActive: false,
        dataTable: false,
        ticketInfos: {
            statuses: [],
            callTypeId: null,
            callCategoryId: null,
            callSubCategoryId: null,
            requiredField: {},
            comments: "",
            attachment: "",
            attachmentType: "",
            is_verified: "no",
        },
        modalBody: "",
    }),

    mounted() {
        this.fetchCallTypes();
        this.checkDNDStatus();
        this.fetchTicketStatuses();
    },
    computed: {
        user() {
            return this.$store.state.auth.user;
        },
        isNagadSebaOrUddoktaPointCustomerGroup() {
            return (
                this.user &&
                (this.user.group_id === 3 || this.user.group_id === 5)
            );
        },
    },
    created() {
        this.callerMobileNo = this.$route.query?.msisdn || null;
    },
    watch: {
        "ticketInfos.callCategoryId": function (newCategory) {
            if (newCategory) {
                this.ticketInfos.callSubCategoryId = null;
                this.callSubCategories = [];
                this.requiredFields = [];
                this.requiredFieldsSets = [];
                this.fetchSubCategory();
            }
        },
        prevTickets(newTickets) {
            if ($.fn.DataTable.isDataTable("#dataTable")) {
                $("#dataTable").DataTable().destroy(); // Destroy old instance
            }

            if (newTickets.length > 0) {
                this.$nextTick(() => {
                    $("#dataTable").DataTable(); // Initialize when new data exists
                });
            }
        },
    },
    methods: {
        formatDateTimeTest(fieldId) {
            const isoDateString = this.ticketInfos.requiredField[fieldId];
            if (isoDateString) {
                const date = new Date(isoDateString);

                // Extract the components
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, "0"); // Months are zero-based
                const day = String(date.getDate()).padStart(2, "0");
                const hours = String(date.getHours()).padStart(2, "0");
                const minutes = String(date.getMinutes()).padStart(2, "0");
                const seconds = String(date.getSeconds()).padStart(2, "0");

                // Format to 'YYYY-MM-DD HH:mm:ss'
                const formattedDate = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                this.ticketInfos.requiredField[fieldId] = formattedDate;
            }
        },
        resetFilter() {
            this.searchCallerMobileNo = "";
            this.selectedStatus = "";
            this.prevTickets = [];
        },
        async fetchPrevTickets() {
            if (!this.searchCallerMobileNo) {
                this.$showToast("Please enter a mobile number.", {
                    type: "error",
                });
                return;
            }

            this.isLoading = true;
            this.prevTickets = [];

            try {
                const response = await axios.get(
                    `/previous-ticket/${this.searchCallerMobileNo}`
                );
                this.prevTickets = response.data;

                if (this.prevTickets.length > 0) {
                    this.initializeDataTable();
                } else {
                    this.$showToast("No previous tickets found.", {
                        type: "info",
                    });
                }
            } catch (error) {
                console.error("Error fetching previous tickets:", error);
                this.$showToast("Error fetching previous tickets.", {
                    type: "error",
                });
            } finally {
                this.isLoading = false;
            }
        },

        async searchTickets() {
            if (!this.searchCallerMobileNo) {
                this.$showToast("Please enter a mobile number.", {
                    type: "error",
                });
                return;
            }

            this.isLoading = true;

            try {
                const response = await axios.get("/ticket/search", {
                    params: {
                        mobile_no: this.searchCallerMobileNo,
                        status: this.selectedStatus,
                    },
                });
                this.prevTickets = response.data;

                if (this.prevTickets.length > 0) {
                    this.updateDataTable();
                } else {
                    this.$showToast(
                        "No tickets found for the given criteria.",
                        { type: "info" }
                    );
                }
            } catch (error) {
                console.error("Failed to fetch tickets", error);
                this.$showToast("Error fetching tickets.", { type: "error" });
            } finally {
                this.isLoading = false;
            }
        },

        initializeDataTable() {
            this.$nextTick(() => {
                const tableElement = $("#dataTable");

                if (this.dataTable) {
                    this.dataTable.destroy();
                }

                this.dataTable = tableElement.DataTable({
                    columns: [
                        { title: "Date Time" },
                        { title: "Ticket No" },
                        { title: "Sub Category" },
                        { title: "Status" },
                        { title: "Action", className: "text-right" },
                    ],
                    data: this.prevTickets.map((ticket) => [
                        this.formatDateTime(ticket.ticket_created_at),
                        ticket.uuid,
                        ticket.call_sub_category_name,
                        ticket.ticket_status,
                        `<a href="/ticket-timeline/${ticket.ticket_id}" class="btn-action btn-edit">
                        <i class="icon-tickets"></i>
                    </a>`,
                    ]),
                    ordering: false,
                });
            });
        },
        updateDataTable() {
            if (this.dataTable) {
                this.dataTable.clear();
                this.dataTable.rows.add(
                    this.prevTickets.map((ticket) => [
                        this.formatDateTime(ticket.ticket_created_at),
                        ticket.uuid,
                        ticket.call_sub_category_name,
                        ticket.ticket_status,
                        `<a href="/ticket-timeline/${ticket.ticket_id}" class="btn-action btn-edit">
                    <i class="icon-tickets"></i>
                </a>`,
                    ])
                );
                this.dataTable.draw();
            }
        },
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
        fetchTicketStatuses() {
            axios
                .get("/ticket/statuses")
                .then((response) => {
                    this.ticketInfos.statuses = response.data.statuses;
                })
                .catch((error) => {
                    console.error("Error fetching ticket statuses:", error);
                });
        },
        addFieldsSet() {
            if (this.requiredFieldsSets.length < 3) {
                const newFields = this.requiredFields.map((field) => ({
                    id: `${field.id}-${this.fieldSetIdentifier}`,

                    input_field_name: field.input_field_name,
                    input_type: field.input_type,
                    input_value: field.input_value || [],
                    value: "",
                }));

                this.requiredFieldsSets.push({
                    id: this.requiredFieldsSets.length + 1,
                    fields: newFields,
                });

                this.fieldSetIdentifier++;

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
            $("#ticketSuccessPopup").on("hidden.bs.modal", () => {
                this.redirectToTicketList();
            });
        },
        redirectToTicketList() {
            this.$router.push({ name: "ticket-index" });
        },
        handleAttachmentFileUpload(event) {
            const file = event.target.files[0]; // Get the file
            const reader = new FileReader();

            reader.readAsDataURL(file); // Convert file to Base64
            reader.onload = () => {
                this.ticketInfos.attachment = reader.result; // Store Base64 data
                this.ticketInfos.attachmentType = file.type; // Store the file's MIME type
            };
        },
        async fetchCallTypes() {
            try {
                this.requiredFields = [];
                this.requiredFieldsSets = [];
                this.fieldSetIdentifier = 1;
                this.callCategories = [];
                this.callSubCategories = [];
                this.ticketInfos.callCategoryId = null;
                this.ticketInfos.callSubCategoryId = null;
                this.serviceTypeConfigs = {};
                this.popupMessages = {};
                const response = await axios.get("/get-service-types");

                this.callTypes = response.data;
            } catch (error) {
                console.error("Error fetching call types:", error);
            }
        },
        async fetchCategories() {
            try {
                this.requiredFields = [];
                this.callCategories = [];
                this.ticketInfos.callCategoryId = null;
                this.requiredFieldsSets = [];
                this.fieldSetIdentifier = 1;
                this.serviceTypeConfigs = {};
                this.popupMessages = {};
                this.callSubCategories = [];
                this.ticketInfos.callSubCategoryId = null;
                const response = await axios.get(
                    `/get-category/${this.ticketInfos.callTypeId}`
                );

                this.callCategories = response.data;
            } catch (error) {
                console.error("Error fetching categories:", error);
            }
        },
        async fetchSubCategory() {
            try {
                this.requiredFields = [];
                this.requiredFieldsSets = [];
                this.serviceTypeConfigs = {};
                this.popupMessages = {};

                const response = await axios.get(
                    `/call-sub-by-call-cat-id/${this.ticketInfos.callTypeId}/${this.ticketInfos.callCategoryId}`
                );

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
                    this.fieldSetIdentifier++;
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

                if (serviceConfigs && typeof serviceConfigs === "object") {
                    this.serviceTypeConfigs = serviceConfigs;

                    this.popupMessages = JSON.parse(
                        serviceConfigs.popup_msg_texts || "{}"
                    );
                } else {
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
        validateRequiredFields() {
            let hasValidationError = false;
            const requiredFieldErrors = [];

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

            if (!this.ticketInfos.callTypeId) {
                hasValidationError = true;
                requiredFieldErrors.push('The "Call Type" field is required.');
            }

            if (!this.ticketInfos.callCategoryId) {
                hasValidationError = true;
                requiredFieldErrors.push(
                    'The "Call Category" field is required.'
                );
            }

            if (!this.ticketInfos.callSubCategoryId) {
                hasValidationError = true;
                requiredFieldErrors.push(
                    'The "Call Sub-Category" field is required.'
                );
            }

            if (!this.ticketInfos.comments) {
                hasValidationError = true;
                requiredFieldErrors.push('The "Comment" field is required.');
            }

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
            const { hasValidationError, requiredFieldErrors } =
                this.validateRequiredFields();

            if (hasValidationError) {
                this.requiredFieldErrors = requiredFieldErrors;
                return;
            }

            try {
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
                } else if (
                    response.data.status === "error" &&
                    response.data.code === 409
                ) {
                    this.$showToast(response.data.message, {
                        type: "error",
                    });
                }
            } catch (error) {
                if (
                    error.response.data.status === "error" &&
                    error.response.data.code === 409
                ) {
                    this.$showToast(error.response.data.message, {
                        type: "error",
                    });
                } else {
                    this.$showToast(
                        "There was an error submitting the ticket form.",
                        {
                            type: "error",
                        }
                    );
                }
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
                selectedStatus: null,
                is_verified: "no",
            };
            this.popupMessages = {};
            this.customerPhoneNumber = "";
            this.callerMobileNo = null;
            this.searchCallerMobileNo = null;
            this.requiredFieldErrors = [];
        },
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
