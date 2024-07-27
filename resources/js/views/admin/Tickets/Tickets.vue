<template>
    <div class="card mb-4">
        <div class="card-header">
            <h1 class="title mb-3">Customer Information</h1>
            <div class="d-flex align-items-center">
                <form action="" class="verify-user mr-0 mr-md-3">
                    <input
                        class="form-control"
                        type="tel"
                        v-model="callerMobileNo"
                        name="customer"
                        placeholder="Customer Account No"
                        required
                    />
                    <button class="btn"><i class="icon-search"></i></button>
                </form>
                <div class="verified-user d-flex">
                    <i class="icon-phone-call"></i>
                    <h5>In Call..<span>+8801987654321</span></h5>
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
                <div class="col-md-3">
                    <userInfo />
                </div>
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

import userInfo from "./components/userInfo.vue";

export default {
    components: {
        userInfo,
    },
    name: "Tickets",
    data: () => ({
        callerMobileNo: "",
        callerPrevTickets: false,
        //id: this.$route.params.id,
        requiredFields: [],
        callTypes: [],
        callCategories: [],
        callSubCategories: [],

        ticketInfos: {
            callTypeId: null,
            callCategoryId: null,
            callSubCategoryId: null,
            requiredField: {},
            comments: "",
        },
    }),
    mounted() {
        this.fetchCallTypes();
    },
    methods: {
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
            await this.fetchRequiredFieldsByCategory().then((response) => {
                this.requiredFields = response.data;
                this.generateInputTypes(response.data);
            });
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
