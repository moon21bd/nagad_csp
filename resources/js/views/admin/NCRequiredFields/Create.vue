<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                :to="{ name: 'required-fields-config-index' }"
                ><i class="icon-left"></i
            ></router-link>
            <h1 class="title m-0">Required Fields Configurations</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <form @submit.prevent="createRequiredFields">
                            <!-- Service Type -->
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="control-label"
                                        >Service Type<sup>*</sup></label
                                    >
                                    <el-select
                                        class="d-block w-100"
                                        v-model="callTypeId"
                                        @change="fetchCategories"
                                        required
                                        filterable
                                        placeholder="Select Service Type"
                                    >
                                        <el-option
                                            v-for="types in callTypes"
                                            :key="types.id"
                                            :label="types.call_type_name"
                                            :value="types.id"
                                        ></el-option>
                                    </el-select>
                                </div>

                                <!-- Service Category -->
                                <div class="col-md-4 form-group">
                                    <label class="control-label"
                                        >Service Category<sup>*</sup></label
                                    >
                                    <el-select
                                        class="d-block w-100"
                                        v-model="callCategoryId"
                                        @change="fetchSubCategory"
                                        required
                                        filterable
                                        placeholder="Select Service Category"
                                    >
                                        <el-option
                                            v-for="category in callCategories"
                                            :key="category.id"
                                            :label="category.call_category_name"
                                            :value="category.id"
                                        ></el-option>
                                    </el-select>
                                </div>

                                <!-- Service Sub Category -->
                                <div class="col-md-4 form-group">
                                    <label class="control-label"
                                        >Service Sub Category<sup>*</sup></label
                                    >
                                    <el-select
                                        class="d-block w-100"
                                        v-model="callSubCategoryId"
                                        required
                                        filterable
                                        placeholder="Select Service Sub Category"
                                    >
                                        <el-option
                                            v-for="subCategory in callSubCategories"
                                            :key="subCategory.id"
                                            :label="
                                                subCategory.call_sub_category_name
                                            "
                                            :value="subCategory.id"
                                        ></el-option>
                                    </el-select>
                                </div>
                            </div>

                            <div
                                v-for="(field, index) in formFields"
                                :key="field.id"
                                class="required-fields-item"
                            >
                                <div class="form-row">
                                    <!-- Field Set Number -->
                                    <div class="col-12 mb-3">
                                        <h5
                                            class="sub-title d-flex align-items-center text-danger"
                                        >
                                            Fields No {{ index + 1 }}

                                            <button
                                                type="button"
                                                class="btn btn-danger btn-sm ml-auto"
                                                @click="removeFormField(index)"
                                                :disabled="
                                                    formFields.length === 1
                                                "
                                            >
                                                <i class="icon-trash"></i>
                                                Remove
                                            </button>
                                        </h5>
                                    </div>

                                    <!-- Input Field Name -->
                                    <div class="col-md-6 form-group">
                                        <label class="control-label"
                                            >Input Field Name<sup>*</sup></label
                                        >
                                        <input
                                            type="text"
                                            v-model="field.inputFiledName"
                                            class="form-control"
                                            placeholder="Enter Input Field Name"
                                        />
                                    </div>

                                    <!-- Input Type -->
                                    <div class="col-md-6 form-group">
                                        <label class="control-label"
                                            >Choose Input Type<sup
                                                >*</sup
                                            ></label
                                        >
                                        <select
                                            class="form-control"
                                            v-model="field.inputType"
                                        >
                                            <option value="select">
                                                Select/Option
                                            </option>
                                            <option value="integer">
                                                Number
                                            </option>
                                            <option value="varchar">
                                                String
                                            </option>
                                            <option value="text">Text</option>
                                            <option value="datetime">
                                                DateTime
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Input Value (conditional for select input) -->
                                    <div
                                        class="col-md-12 form-group"
                                        v-if="field.inputType === 'select'"
                                    >
                                        <label class="control-label"
                                            >Input Value<sup>*</sup></label
                                        >
                                        <input
                                            type="text"
                                            v-model="field.inputValue"
                                            class="form-control"
                                            placeholder="Enter Input Value"
                                        />
                                    </div>

                                    <!-- Input Validation -->
                                    <!-- <div class="col-md-12 form-group">
                                    <label class="control-label"
                                        >Input Validation/Rules<sup
                                            >*</sup
                                        ></label
                                    >
                                    <input
                                        type="text"
                                        v-model="field.inputValidation"
                                        class="form-control"
                                        placeholder="Enter Input Validation Rules"
                                    />
                                </div> -->

                                    <!-- Status -->
                                    <div
                                        class="form-group d-flex align-items-center"
                                    >
                                        <label class="control-label m-0 mr-3"
                                            >Status<sup>*</sup></label
                                        >
                                        <label class="radio mr-2"
                                            ><input
                                                type="radio"
                                                value="active"
                                                v-model="field.statusValue"
                                                required
                                            /><span class="radio-mark"></span
                                            >Active</label
                                        >
                                        <label class="radio"
                                            ><input
                                                type="radio"
                                                value="inactive"
                                                v-model="field.statusValue"
                                                required
                                            /><span class="radio-mark"></span
                                            >Inactive</label
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <button
                                    type="button"
                                    class="btn btn-site mb-3 bg-dark"
                                    @click="addFormField"
                                    :disabled="formFields.length >= maxFields"
                                >
                                    <i class="icon-submit"></i> New
                                </button>
                            </div>

                            <div
                                v-if="requiredFieldErrors.length > 0"
                                class="alert alert-danger"
                            >
                                <ul class="pl-3 m-0">
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

                            <div class="card-footer bg-white px-0 pb-0">
                                <button type="submit" class="btn btn-site">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "NCRequiredFieldsCreate",
    data() {
        return {
            isLoading: false,
            maxFields: 10,
            callTypeId: null,
            callCategoryId: null,
            callSubCategoryId: null,
            callTypes: [],
            callCategories: [],
            callSubCategories: [],
            formFields: [
                {
                    id: 1,
                    inputFiledName: "",
                    inputType: "",
                    inputValue: "",
                    inputValidation: "required",
                    statusValue: "",
                },
            ],
            requiredFieldErrors: [],
        };
    },
    watch: {
        callCategoryId: function (newCategory) {
            if (newCategory) {
                this.callSubCategoryId = null;
                this.callSubCategories = [];
            }
        },
        callTypeId: function (newCategory) {
            if (newCategory) {
                this.callCategoryId = null;
                this.callSubCategoryId = null;
                this.callSubCategories = [];
                this.callCategories = [];
                this.fetchSubCategory();
            }
        },
    },
    mounted() {
        this.fetchCallTypes();
    },
    methods: {
        async fetchCallTypes() {
            try {
                const response = await axios.get("/get-service-types");
                this.callSubCategories = [];
                this.callCategories = [];
                this.callTypes = response.data;
            } catch (error) {
                console.error("Error fetching service types:", error);
            }
        },

        async fetchCategories() {
            try {
                const response = await axios.get(
                    `/get-category/${this.callTypeId}`
                );
                this.callSubCategories = [];
                this.callCategories = response.data;
            } catch (error) {
                console.error("Error fetching categories:", error);
            }
        },

        async fetchSubCategory() {
            try {
                const response = await axios.get(
                    `/call-sub-by-call-cat-id/${this.callTypeId}/${this.callCategoryId}`
                );
                this.callSubCategories = response.data;
            } catch (error) {
                console.error("Error fetching subcategories:", error);
            }
        },

        validateRequiredFields() {
            const requiredFieldErrors = [];
            let hasValidationError = false;

            if (!this.callTypeId) {
                hasValidationError = true;
                requiredFieldErrors.push("Service Type is required.");
            }

            if (!this.callCategoryId) {
                hasValidationError = true;
                requiredFieldErrors.push("Service Category is required.");
            }

            if (!this.callSubCategoryId) {
                hasValidationError = true;
                requiredFieldErrors.push("Service Sub Category is required.");
            }

            // Validate each form field
            this.formFields.forEach((field, index) => {
                if (
                    !field.inputFiledName ||
                    field.inputFiledName.length > 128
                ) {
                    hasValidationError = true;
                    requiredFieldErrors.push(
                        "Input Field Name is required and should be max 128 characters."
                    );
                }

                if (!field.inputType) {
                    hasValidationError = true;
                    requiredFieldErrors.push("Input Type is required.");
                }

                /* if (!field.inputValidation) {
                    hasValidationError = true;
                    requiredFieldErrors.push(
                        "Input Validation/Rules are required."
                    );
                } */

                if (!field.statusValue) {
                    hasValidationError = true;
                    requiredFieldErrors.push("Status is required.");
                }
            });

            return {
                hasValidationError,
                requiredFieldErrors,
            };
        },

        addFormField() {
            if (this.formFields.length < this.maxFields) {
                this.formFields.push({
                    id: Date.now(),
                    inputFiledName: "",
                    inputType: "",
                    inputValue: "",
                    // inputValidation: "required",
                    statusValue: "",
                });
            }
        },

        removeFormField(index) {
            if (this.formFields.length > 1) {
                this.formFields.splice(index, 1);
            }
        },

        async createRequiredFields() {
            try {
                const { hasValidationError, requiredFieldErrors } =
                    this.validateRequiredFields();

                if (hasValidationError) {
                    this.requiredFieldErrors = requiredFieldErrors;
                    return;
                }

                const callType = this.callTypeId;
                const callCategory = this.callCategoryId;
                const callSubCategory = this.callSubCategoryId;

                const postData = this.formFields.map((field) => ({
                    inputFiledName: field.inputFiledName,
                    inputType: field.inputType,
                    inputValue: field.inputValue,
                    inputValidation: "required", // field.inputValidation,
                    statusValue: field.statusValue,
                }));

                const response = await axios.post("/required-fields-configs", {
                    formFields: postData,
                    callType,
                    callCategory,
                    callSubCategory,
                });
                this.$router.push({ name: "required-fields-config-index" });

                this.$showToast(response.data.message, {
                    type: "success",
                });
            } catch (error) {
                console.error("Error creating required fields config:", error);
            }
        },
    },
};
</script>
