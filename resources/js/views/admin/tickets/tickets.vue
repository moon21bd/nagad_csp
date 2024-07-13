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
                                <label class="control-label">Call Type</label>
                                <div class="custom-style">
                                    <select
                                        class="form-control"
                                        v-model="ticketsData.callType"
                                        required
                                    >
                                        <option value="" disabled>
                                            Select Call Type
                                        </option>
                                        <option value="Service Request">
                                            Service Request
                                        </option>
                                        <option value="Complaint">
                                            Complaint
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label">Category:</label>
                                <select
                                    class="form-control"
                                    name=""
                                    v-model="ticketsData.category"
                                    @change="getSubcategories"
                                >
                                    <option value="" disabled>
                                        Select Category
                                    </option>
                                    <option
                                        v-for="{ name, id } in categories"
                                        :key="id"
                                        :value="name"
                                    >
                                        {{ name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label"
                                >Sub-Category:</label
                                >
                                <select
                                    class="form-control"
                                    v-model="ticketsData.subCategory"
                                >
                                    <option value="" disabled>
                                        Select Sub-Category
                                    </option>
                                    <option
                                        v-for="subCategory in subCategories"
                                        :key="subCategory"
                                        :value="subCategory"
                                    >
                                        {{ subCategory }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-4 form-group">
                                <label class="control-label"
                                >Incident Type</label
                                >
                                <div class="custom-style">
                                    <select
                                        class="form-control"
                                        v-model="ticketsData.incidentType"
                                        required
                                    >
                                        <option value="" disabled>
                                            Select Incident Type
                                        </option>
                                        <option value="Attempt">Attempt</option>
                                        <option value="Activity">
                                            Activity
                                        </option>
                                        <option value="GD">GD</option>
                                        <option value="Hundi">Hundi</option>
                                        <option value="Below 500">
                                            Below 500
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="control-label"
                                >Victim Account No</label
                                >
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="ticketsData.victimAccountNo"
                                    required
                                />
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="control-label"
                                >Fraudster Account No</label
                                >
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="ticketsData.fraudsterAccountNo"
                                    required
                                />
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="control-label"
                                >Brief of the Incident</label
                                >
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="ticketsData.incidentBrief"
                                />
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="control-label"
                                >Customer calling No</label
                                >
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="ticketsData.customerCallingNo"
                                    required
                                />
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="control-label"
                                >Fraudster Calling No</label
                                >
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="ticketsData.fraudsterCallingNo"
                                    required
                                />
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label"
                                >Date & Time:</label
                                >
                                <el-date-picker
                                    class="d-block w-100"
                                    v-model="ticketsData.dateTime"
                                    type="datetime"
                                    placeholder="Select date and time"
                                >
                                </el-date-picker>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="control-label"
                                >Transaction ID:</label
                                >
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="ticketsData.transactionID"
                                    required
                                />
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label"
                                >Payment Channel</label
                                >
                                <div class="custom-style">
                                    <select
                                        class="form-control"
                                        v-model="ticketsData.paymentChannel"
                                        required
                                    >
                                        <option value="" disabled>
                                            Select Payment Channel
                                        </option>
                                        <option value="Apps">Apps</option>
                                        <option value="Ussd">Ussd</option>
                                        <option value="E-Com">E-Com</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="control-label"
                                >Amount as Portal</label
                                >
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="ticketsData.amountAsPortal"
                                    required
                                />
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="control-label"
                                >Amount as customer</label
                                >
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="ticketsData.amountAsCustomer"
                                    required
                                />
                            </div>

                            <div class="col-md-4 form-group">
                                <label class="control-label"
                                >Responsible Team:</label
                                >
                                <select
                                    class="form-control"
                                    name=""
                                    v-model="ticketsData.responsibleTeam"
                                >
                                    <option value="" disabled>
                                        Select Team
                                    </option>
                                    <option
                                        v-for="{ name, id } in groups"
                                        :key="id"
                                        :value="name"
                                    >
                                        {{ name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="control-label">Remarks</label>
                                <textarea
                                    class="form-control"
                                    v-model="ticketsData.remarks"
                                ></textarea>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="checkbox"
                                ><input
                                    type="checkbox"
                                    v-model="
                                            ticketsData.fraudulentAmountAvailability
                                        "
                                    required
                                /><span class="checkmark"></span>Fraudulent
                                    Amount Availability</label
                                >
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="control-label"
                                >Do you agree?</label
                                >
                                <div class="d-flex">
                                    <label class="radio mr-2"
                                    ><input
                                        type="radio"
                                        id="yes"
                                        value="1"
                                        name="check"
                                    /><span class="radio-mark"></span>Yes
                                    </label>
                                    <label class="radio">
                                        <input
                                            type="radio"
                                            id="no"
                                            value="0"
                                            name="check"
                                        /><span class="radio-mark"></span>No
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

    methods: {
        async getCategories() {
            try {
                const response = await axios.get(
                    "http://localhost:3000/categories"
                );
                this.categories = response.data;
            } catch (error) {
                console.log(error);
            }
        },

        getSubcategories() {
            this.subCategories =
                this.categories.find(
                    (category) => category.name === this.ticketsData.category
                )?.subcategories ?? [];
        },
        async getGroups() {
            try {
                const response = await axios.get(
                    "http://localhost:3000/groups"
                );
                this.groups = response.data;
            } catch (error) {
                console.log(error);
            }
        },
        async handleSubmit() {
            try {
                await axios.post(
                    "http://localhost:3000/tickets",
                    this.ticketsData
                );
                alert("Form submitted successfully");
                this.$refs.ticketForm.reset();
                this.resetForm();
            } catch (error) {
                console.log("There was an error submitting the form!");
            }
        },

        resetForm() {
            this.ticketsData = {
                callType: "",
                category: "",
                subCategory: "",
                incidentType: "",
                paymentChannel: "",
                responsibleTeam: "",
            };
            this.subCategories = [];
        },
    },
    created() {
        this.getCategories();
        this.getGroups();
    },
};
</script>
