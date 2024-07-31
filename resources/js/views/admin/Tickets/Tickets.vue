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
                <div class="verified-user d-flex">
                    <i class="icon-phone-call"></i>
                    <h5>In Call..<span>+8801710455990</span></h5>
                </div>

                <a
                    class="btn-prev-tickets ml-auto"
                    :class="{ show: callerPrevTickets }"
                    @click.prevent="showPrevTickets()"
                >
                    Previous Tickets <i class="icon-arrow-down-circle"></i>
                </a>
            </div>
        </div>
        <div class="card-body">
            <div v-if="callerPrevTickets">
                <div class="table-responsive">
                    <table class="table prev-table border rounded">
                        <thead>
                            <tr>
                                <th>Service Type</th>
                                <th>Service Category</th>
                                <th>Service Sub Category</th>
                                <th class="text-right">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Complaint</td>
                                <td>PIN Related</td>
                                <td>Fraud Attempt</td>
                                <td class="text-right">
                                    Fraud incident was solved
                                </td>
                            </tr>
                            <tr>
                                <td>Service Request</td>
                                <td>Merchant Payment Related</td>
                                <td>SSL Commerz Fraud Issue</td>
                                <td class="text-right">
                                    SSL Commerz solved the issue
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <!-- <div class="col-md-3">
                    <userInfo />
                </div> -->
                <div class="col-md-9">
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
                                    @change="fetchRequiredFields"
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

                        <p>Required Fields Start</p>

                        <!-- <button
                            type="button"
                            class="btn"
                            data-toggle="modal"
                            data-target="#ticketSuccessPopup"
                        >
                            <i class="icon-phone"></i> Modal
                        </button> -->

                        <div v-if="requiredFields">
                            <div class="form-row">
                                <div
                                    class="col-md-4"
                                    v-for="(data, index) in requiredFields"
                                    :key="index"
                                >
                                    <div
                                        class="form-group"
                                        v-if="data.input_type === 'select'"
                                    >
                                        <label
                                            class="control-label"
                                            for="input_field_name"
                                            >{{ data.input_field_name }}</label
                                        >
                                        <el-select
                                            class="d-block w-100"
                                            v-model="
                                                ticketInfos.requiredField[
                                                    data.id +
                                                        '|' +
                                                        data.input_field_name
                                                ]
                                            "
                                            required
                                            filterable
                                            placeholder="Select Type"
                                        >
                                            <el-option
                                                v-for="(
                                                    options, index
                                                ) in inputTypeValues[index]
                                                    .input_value"
                                                :value="options"
                                                :key="index"
                                                >{{ options }}
                                            </el-option>
                                        </el-select>
                                    </div>
                                    <div
                                        class="form-group"
                                        v-else-if="
                                            data.input_type === 'datetime'
                                        "
                                    >
                                        <label
                                            class="control-label"
                                            for="exampleFormControlSelect1"
                                            >{{ data.input_field_name }}</label
                                        >

                                        <el-date-picker
                                            class="d-block w-100"
                                            v-model="
                                                ticketInfos.requiredField[
                                                    data.id +
                                                        '|' +
                                                        data.input_field_name
                                                ]
                                            "
                                            type="datetime"
                                            placeholder="Select date and time"
                                        >
                                        </el-date-picker>
                                    </div>
                                    <div class="form-group" v-else>
                                        <label
                                            class="control-label"
                                            for="name"
                                            >{{ data.input_field_name }}</label
                                        >
                                        <input
                                            type="text"
                                            v-model="
                                                ticketInfos.requiredField[
                                                    data.id +
                                                        '|' +
                                                        data.input_field_name
                                                ]
                                            "
                                            class="form-control"
                                            :placeholder="
                                                'Enter ' + data.input_field_name
                                            "
                                        />
                                    </div>
                                </div>
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
                        </div>
                        <p>Required Fields End</p>

                        <div class="form-row">
                            <div
                                v-if="
                                    serviceTypeConfigs?.is_show_attachment ===
                                    'yes'
                                "
                                class="col-md-4 form-group"
                            >
                                <label class="control-label"
                                    >Attachement<sup>*</sup></label
                                >
                                <input
                                    type="file"
                                    name="is_show_attachment"
                                    @change="handleAttachmentFileUpload"
                                />
                            </div>

                            <div
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
                            </div>
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

// import userInfo from "./components/userInfo.vue";

export default {
    components: {
        // userInfo,
    },
    name: "Tickets",
    data: () => ({
        callerMobileNo: "",
        callerPrevTickets: false,
        // id: this.$route.params.id,
        requiredFields: [],
        callTypes: [],
        callCategories: [],
        callSubCategories: [],
        serviceTypeConfigs: {},
        ticketInfos: {
            callTypeId: null,
            callCategoryId: null,
            callSubCategoryId: null,
            requiredField: {},
            comments: "",
            attachement: "",
            is_verified: "no",
        },
        modalBody: "",
    }),
    mounted() {
        this.fetchCallTypes();
    },
    methods: {
        showPopup(message) {
            this.modalBody = message;
            $("#ticketSuccessPopup").modal("show");
        },
        handleAttachmentFileUpload(event) {
            console.log("event", event.target.files[0]);
            ticketInfos.attachement = event.target.files[0];
            /* let reader = new FileReader();
            reader.readAsDataURL(event.target.files[0]);
            reader.onload = () => {
                this.formData.avatar = reader.result;
                this.temp_avatar = reader.result;
            }; */
        },
        showPrevTickets() {
            this.callerPrevTickets = !this.callerPrevTickets;
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

                this.callSubCategories = response.data;
            } catch (error) {
                console.error("Error fetching sub categories:", error);
            }
        },
        fetchRequiredFieldsByCategory() {
            return new Promise((resolve, reject) => {
                axios
                    .get(
                        `get-required-field-by-sub-cat-id/${this.ticketInfos.callSubCategoryId}`
                    )
                    .then((response) => {
                        resolve(response);
                    })
                    .catch((error) => {
                        reject(error);
                    })
                    .finally(() => {});
            });
        },
        async fetchRequiredFields() {
            await this.getServiceTypeConfigByAllCat(
                this.ticketInfos.callTypeId,
                this.ticketInfos.callCategoryId,
                this.ticketInfos.callSubCategoryId
            ).then((response) => {
                console.log("response.data.data", response.data);
            });

            await this.fetchRequiredFieldsByCategory().then((response) => {
                this.requiredFields = response.data;
                this.generateInputTypes(response.data);
            });
        },
        async getServiceTypeConfigByAllCat(cti, cci, csci) {
            try {
                const response = await axios.get(
                    `/get-service-type-configs/${this.ticketInfos.callTypeId}/${this.ticketInfos.callCategoryId}/${this.ticketInfos.callSubCategoryId}`
                );
                console.log("response.data", response.data.data);
                this.serviceTypeConfigs = response.data.data;
            } catch (error) {
                this.serviceTypeConfigs = {};
                console.error("Error fetching sub categories:", error);
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
        async handleSubmit() {
            // this.showPopup("Form Submited.");

            // return false;
            console.log(
                "handleSubmit Called",
                this.ticketInfos.requiredField,
                "msisdn",
                this.$route.query.msisdn
            );
            try {
                // assigning mobile no from query param
                this.ticketInfos.callerMobileNo =
                    this.$route.query?.msisdn || null;

                const response = await axios.post("/tickets", this.ticketInfos);
                console.log(
                    "Form submitted successfully, resp: ",
                    response.data
                );
                return false;

                this.$refs.ticketForm.reset();
                this.resetForm();
            } catch (error) {
                console.log("There was an error submitting the form!");
            }
        },
    },
};
</script>
