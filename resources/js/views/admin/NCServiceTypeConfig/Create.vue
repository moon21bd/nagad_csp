<template>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="sub-title mb-2">
                        <i class="icon-sliders text-danger"></i> Service Type
                        Configurations
                    </h4>
                    <form
                        ref="serviceTypeConfigForm"
                        @submit.prevent="handleSubmit"
                    >
                        <div class="form-row">
                            <div class="col-md-4 form-group">
                                <label class="control-label"
                                    >Service Type<sup>*</sup></label
                                >
                                <div class="custom-style">
                                    <el-select
                                        class="d-block w-100"
                                        v-model="
                                            serviceTypeConfigInfos.callTypeId
                                        "
                                        @change="fetchCategories"
                                        required
                                        filterable
                                        placeholder="Select Type"
                                    >
                                        <el-option
                                            v-for="type in callTypes"
                                            :key="type.id"
                                            :label="type.call_type_name"
                                            :value="type.id"
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
                                    v-model="
                                        serviceTypeConfigInfos.callCategoryId
                                    "
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
                                    v-model="
                                        serviceTypeConfigInfos.callSubCategoryId
                                    "
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

                        <div class="form-row">
                            <div v-if="requiredFields">
                                <div
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
                                                serviceTypeConfigInfos
                                                    .requiredField[
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
                                                serviceTypeConfigInfos
                                                    .requiredField[
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
                                                serviceTypeConfigInfos
                                                    .requiredField[
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
                            <div class="col-md-4 form-group">
                                <label class="control-label"
                                    >Group/Team Lists<sup>*</sup></label
                                >
                                <el-select
                                    v-model="serviceTypeConfigInfos.groupIds"
                                    multiple
                                    collapse-tags
                                    placeholder="Select Group/Team"
                                    style="width: 240px"
                                >
                                    <el-option
                                        v-for="group in groups"
                                        :key="group.id"
                                        :label="group.name"
                                        :value="group.id"
                                    />
                                </el-select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div
                                class="col-md-6 form-group d-flex align-items-center"
                            >
                                <label class="control-label m-0 mr-3"
                                    >Popup Message</label
                                >
                                <label class="radio mr-2"
                                    ><input
                                        type="radio"
                                        value="yes"
                                        name="is_show_popup_msg"
                                        v-model="
                                            serviceTypeConfigInfos.is_show_popup_msg
                                        "
                                        v-validate="'required'"
                                    /><span class="radio-mark"></span>Yes
                                </label>
                                <label class="radio">
                                    <input
                                        type="radio"
                                        value="no"
                                        name="is_show_popup_msg"
                                        v-model="
                                            serviceTypeConfigInfos.is_show_popup_msg
                                        "
                                        v-validate="'required'"
                                    /><span class="radio-mark"></span>No
                                </label>
                                <div
                                    class="text-danger"
                                    v-show="errors.has('gender')"
                                >
                                    {{ errors.first("gender") }}
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
                                    v-model="serviceTypeConfigInfos.comments"
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

export default {
    name: "ServiceTypeConfig",
    data: () => ({
        groups: [],
        requiredFields: [],
        callTypes: [],
        callCategories: [],
        callSubCategories: [],

        serviceTypeConfigInfos: {
            groupIds: null,
            callTypeId: null,
            callCategoryId: null,
            callSubCategoryId: null,
            requiredField: {},
            comments: "",
        },
    }),
    mounted() {
        this.fetchGroups();
        this.fetchCallTypes();
    },
    methods: {
        async fetchGroups() {
            try {
                const response = await axios.get("/groups");
                this.groups = response.data;
            } catch (error) {
                console.error("Error fetching groups:", error);
            }
        },
        async fetchCallTypes() {
            try {
                const response = await axios.get("/get-service-types");
                this.requiredFields = [];
                this.callSubCategories = [];
                this.serviceTypeConfigInfos.callSubCategoryId = null;
                this.callTypes = response.data;
            } catch (error) {
                console.error("Error fetching call types:", error);
            }
        },
        async fetchCategories() {
            try {
                const response = await axios.get(
                    `/get-category/${this.serviceTypeConfigInfos.callTypeId}`
                );
                this.requiredFields = [];
                this.callSubCategories = [];
                this.serviceTypeConfigInfos.callSubCategoryId = null;
                this.callCategories = response.data;
            } catch (error) {
                console.error("Error fetching categories:", error);
            }
        },
        async fetchSubCategory() {
            try {
                const response = await axios.get(
                    `/call-sub-by-call-cat-id/${this.serviceTypeConfigInfos.callTypeId}/${this.serviceTypeConfigInfos.callCategoryId}`
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
                        `get-required-field-by-sub-cat-id/${this.serviceTypeConfigInfos.callSubCategoryId}`
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
                this.serviceTypeConfigInfos.requiredField,
                "msisdn",
                this.$route.query.msisdn
            );
            try {
                // assigning mobile no from query param
                this.serviceTypeConfigInfos.callerMobileNo =
                    this.$route.query?.msisdn || null;

                const response = await axios.post(
                    "/tickets",
                    this.serviceTypeConfigInfos
                );
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
