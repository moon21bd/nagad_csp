<template>
    <div class="card mb-4">
        <div class="card-header">
            <h1 class="title mb-3">Customer Information</h1>
            <div class="d-flex">
                <form action="" class="verify-user mr-0 mr-md-3">
                    <input
                        class="form-control"
                        type="tel"
                        v-model="mobileNo"
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
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <userInfo/>
                </div>
                <div class="col-md-8">
                    <h4 class="sub-title mb-2">
                        <i class="icon-tickets text-danger"></i> Create Ticket
                    </h4>
                    <form ref="ticketForm" @submit.prevent="handleSubmit">
                        <div class="form-row">
                            <div class="col-md-4 form-group">
                                <label class="control-label">Service Type</label>
                                <div class="custom-style">
                                    <select
                                        class="form-control"
                                        v-model="ticketInfos.callTypeId"
                                        @change="fetchCategories" required
                                    >
                                        <option :value="null" disabled>Service Type</option>
                                        <option v-for="type in callTypes" :key="type.id" :value="type.id">
                                            {{ type.call_type_name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label">Service Category:</label>
                                <select
                                    class="form-control"
                                    v-model="ticketInfos.callCategoryId"
                                    @change="fetchSubCategory" required
                                >

                                    <option :value="null" disabled>Service Category</option>
                                    <option v-for="category in callCategories"
                                            :key="category.id"
                                            :value="category.id">
                                        {{ category.call_category_name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label"
                                >Service Sub-Category:</label
                                >
                                <select
                                    class="form-control"
                                    v-model="ticketInfos.callSubCategoryId" @change="fetchRequiredFields" required
                                >
                                    <option :value="null" disabled>Select Call Sub Category</option>
                                    <option v-for="subCategory in callSubCategories"
                                            :key="subCategory.id"
                                            :value="subCategory.id">
                                        {{ subCategory.call_sub_category_name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">

                            <div v-for="(data, index) in requiredFields" v-if="requiredFields" :key="index">
                                <div class="form-group" v-if="data.input_type === 'select'">
                                    <label class="control-label" for="input_field_name">{{
                                            data.input_field_name
                                        }}</label>
                                    <el-select
                                        class="d-block w-100"
                                        v-model="ticketInfos.requiredField[data.id + '|' + data.input_field_name]"
                                        required
                                        filterable
                                        placeholder="Select Type"
                                    >
                                        <el-option v-for="option in inputTypeValues[index].input_value"
                                                   :value="option">{{ option }}
                                        </el-option>
                                    </el-select>

                                </div>
                                <div class="form-group" v-else-if="data.input_type === 'datetime'">
                                    <label class="control-label"
                                           for="exampleFormControlSelect1">{{ data.input_field_name }}</label>

                                    <el-date-picker
                                        class="d-block w-100"
                                        format="YYYY-MM-DD h:i:s"
                                        v-model="ticketInfos.requiredField[data.id + '|' + data.input_field_name]"
                                        type="datetime"
                                        placeholder="Select date and time"
                                    >
                                    </el-date-picker>
                                </div>
                                <div class="form-group" v-else>
                                    <label class="control-label" for="name">{{ data.input_field_name }}</label>
                                    <input type="text"
                                           v-model="ticketInfos.requiredField[data.id + '|' + data.input_field_name]"
                                           class="form-control"
                                           :placeholder="'Enter ' + data.input_field_name">
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
    </div>
</template>

<script>
import axios from "../../../axios";

import userInfo from "./components/userInfo.vue";
import datetime from 'vuejs-datetimepicker';

export default {
    components: {
        userInfo,
        datetime
    },
    name: "Tickets",
    data: () => ({

        requiredFields: [],
        selectOptionValue: [],
        callTypes: [],
        callCategories: [],
        callSubCategories: [],

        ticketInfos: {
            callTypeId: null,
            callCategoryId: null,
            callSubCategoryId: null,
            requiredField: []
        },

        tickets: "Tickets",
        mobileNo: "",
        ticketsData: {
            callType: "",
            category: "",
            subCategory: "",
            incidentType: "",
            incidentBrief: "",
            victimAccountNo: "",
            fraudsterAccountNo: "",
            customerCallingNo: "",
            fraudsterCallingNo: "",
            dateTime: "",
            transactionID: "",
            paymentChannel: "",
            amountAsPortal: null,
            amountAsCustomer: null,
            fraudulentAmountAvailability: false,
            responsibleTeam: "",
            remarks: "",
        },

        categories: [],
        subCategories: [],
        groups: [],
    }),
    mounted() {
        this.fetchCallTypes()
    },
    methods: {
        async fetchCallTypes() {
            try {
                const response = await axios.get("/call-types");
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
                const response = await axios.get(`/get-category/${this.ticketInfos.callTypeId}`);
                this.requiredFields = [];
                this.callSubCategories = [];
                this.ticketInfos.callSubCategoryId = null;
                this.callCategories = response.data;

            } catch (error) {
                console.error('Error fetching call categories:', error);
            }
        },
        async fetchSubCategory() {
            try {
                const response = await axios.get(`/call-sub-by-call-cat-id/${this.ticketInfos.callTypeId}/${this.ticketInfos.callCategoryId}`);

                this.callSubCategories = response.data;
            } catch (error) {
                console.error('Error fetching call sub categories:', error);
            }
        },
        async submit() {
            console.log('this.ticketInfos', this.ticketInfos);
        },
        fetchRequiredFieldsByCategory() {
            return new Promise((resolve, reject) => {
                axios
                    .get(`get-required-field-by-sub-cat-id/${this.ticketInfos.callSubCategoryId}`)
                    .then((response) => {
                        resolve(response)
                    })
                    .catch((error) => {
                        reject(error)
                    })
                    .finally(() => {
                    })
            })
        },
        async fetchRequiredFields() {
            await this.fetchRequiredFieldsByCategory().then(response => {
                this.requiredFields = response.data
                this.generateInputTypes(response.data)
            })
        },
        generateInputTypes(value) {
            this.inputTypeValues = value
            for (let i = 0; i < value.length; i++) {
                if (value[i].input_type === 'select') {
                    this.inputTypeValues[i].input_value = value[i].input_value.split(",")
                }
                this.inputTypeValues[i].input_validation = value[i].input_validation.split(",")

            }
        },
        async handleSubmit() {
            console.log('handleSubmit Called', this.ticketInfos)
            try {
                /*await axios.post(
                    "http://localhost:3000/tickets",
                    this.ticketsData
                );
                alert("Form submitted successfully");
                this.$refs.ticketForm.reset();
                this.resetForm();*/
            } catch (error) {
                console.log("There was an error submitting the form!");
            }
        },

    }
};
</script>
