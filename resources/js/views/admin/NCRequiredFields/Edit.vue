<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                to="/admin/required-fields-config"
                ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Required fields configuration</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <form @submit.prevent="updateRequiredFieldsData">
                            <div class="form-row">
                                <div class="col-md-4 form-group">
                                    <label class="control-label"
                                        >Call Type Id <sup>*</sup></label
                                    >
                                    <div class="custom-style">
                                        <select
                                            class="form-control"
                                            v-model="
                                                requiredFieldsConfigsData.call_type_id
                                            "
                                            @change="fetchCategories"
                                            required
                                        >
                                            <option :value="null" disabled>
                                                Select Call Type
                                            </option>
                                            <option
                                                v-for="types in callTypes"
                                                :key="types.id"
                                                :value="types.id"
                                            >
                                                {{ types.call_type_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="control-label"
                                        >Call Category Id <sup>*</sup></label
                                    >

                                    <el-select
                                        class="d-block w-100"
                                        v-model="
                                            requiredFieldsConfigsData.call_category_id
                                        "
                                        required
                                        filterable
                                        @change="fetchSubCategory"
                                        placeholder="Select Call Category"
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
                                        >Call Sub Category Id
                                        <sup>*</sup></label
                                    >
                                    <!-- <select
                                        v-model="callSubCategoryId"
                                        required
                                    >
                                        <option :value="null" disabled>
                                            Select Call Sub Category
                                        </option>
                                        <option
                                            v-for="subCategory in callSubCategories"
                                            :key="subCategory.id"
                                            :value="subCategory.id"
                                        >
                                            {{
                                                subCategory.call_sub_category_name
                                            }}
                                        </option>
                                    </select> -->

                                    <el-select
                                        class="d-block w-100"
                                        v-model="
                                            requiredFieldsConfigsData.call_sub_category_id
                                        "
                                        required
                                        filterable
                                        placeholder="Select Call Sub Category"
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
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Input Field Name</label
                                    >
                                    <input
                                        type="text"
                                        v-model="
                                            requiredFieldsConfigsData.input_field_name
                                        "
                                        class="form-control"
                                        id=""
                                        placeholder="Enter Input Field Name"
                                    />
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Choose Input Type</label
                                    >
                                    <select
                                        class="form-control"
                                        v-model="
                                            requiredFieldsConfigsData.input_type
                                        "
                                        id=""
                                    >
                                        <option value="select">
                                            Select/Option
                                        </option>
                                        <option value="integer">Number</option>
                                        <option value="varchar">String</option>
                                        <option value="text">Text</option>
                                        <option value="datetime">
                                            DateTime
                                        </option>
                                    </select>
                                </div>
                                <div
                                    class="col-md-12 form-group"
                                    v-if="
                                        requiredFieldsConfigsData.input_type ===
                                        'select'
                                    "
                                >
                                    <label class="control-label"
                                        >Input Value</label
                                    >
                                    <input
                                        type="text"
                                        v-model="
                                            requiredFieldsConfigsData.input_value
                                        "
                                        class="form-control"
                                        id=""
                                        placeholder="Enter Input Value"
                                    />
                                </div>
                                <div class="col-md-12 form-group">
                                    <label class="control-label"
                                        >Input Validation Rules</label
                                    >
                                    <input
                                        type="text"
                                        v-model="
                                            requiredFieldsConfigsData.input_validation
                                        "
                                        class="form-control"
                                        id=""
                                        placeholder="Enter Input Validation Rules"
                                    />
                                    <small class="form-text text-muted"
                                        >We'll never share your email with
                                        anyone else.</small
                                    >
                                </div>
                                <!-- <div class="col-md-12 form-group">
                                    <label for="exampleFormControlSelect1"
                                        >Status</label
                                    >
                                    <select
                                        class="form-control"
                                        v-model="statusValue"
                                        id="status"
                                    >
                                        <option value="active" selected>
                                            Active
                                        </option>
                                        <option value="inactive">
                                            Inactive
                                        </option>
                                    </select>
                                </div> -->
                                <div
                                    class="form-group d-flex align-items-center"
                                >
                                    <label class="control-label m-0 mr-3"
                                        >Status:</label
                                    >
                                    <label class="radio mr-2"
                                        ><input
                                            type="radio"
                                            value="active"
                                            v-model="
                                                requiredFieldsConfigsData.status
                                            "
                                            required
                                        /><span class="radio-mark"></span>Active
                                    </label>
                                    <label class="radio">
                                        <input
                                            type="radio"
                                            value="inactive"
                                            v-model="
                                                requiredFieldsConfigsData.status
                                            "
                                            required
                                        /><span class="radio-mark"></span
                                        >Inactive
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-site">
                                Save
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "../../../axios";

export default {
    name: "Create",
    data() {
        return {
            isLoading: false,
            name: "",
            statusValue: "",
            actionUrl: "store_config",
            apiUrl: "",
            callTypeId: null,
            callCategoryId: null,
            callSubCategoryId: null,
            inputValue: "",
            inputType: "",
            inputFiledName: "",
            inputValidation: "required,",
            callTypes: [],
            callCategories: {},
            callSubCategories: {},
            id: this.$route.params.id,
            requiredFieldsConfigsData: {
                call_type_id: null,
                call_category_id: null,
                call_sub_category_id: "",
                input_field_name: "",
                input_type: "",
                input_value: "",
                input_validation: "",
                status: "active",
            },
        };
    },
    mounted() {
        this.fetchCallTypes();
        this.fetchRequiredFieldsConfigs();
        // this.fetchCategories()
    },
    methods: {
        async fetchCallTypes() {
            try {
                const response = await axios.get("/call-types");
                this.callTypes = response.data;
            } catch (error) {
                console.error("Error fetching call types:", error);
            }
        },
        async fetchCategories() {
            try {
                const response = await axios.get(
                    `/get-category/${this.requiredFieldsConfigsData.call_type_id}`
                );
                this.callCategories = response.data;
                console.log("call category", response.data);
            } catch (error) {
                console.error("Error fetching call categories:", error);
            }
        },
        async fetchSubCategory() {
            this.getSubCategoryById();
        },
        async getSubCategoryById() {
            try {
                const response = await axios.get(
                    `/call-sub-by-call-cat-id/${this.requiredFieldsConfigsData.call_type_id}/${this.requiredFieldsConfigsData.call_category_id}`
                );
                this.callSubCategories = response.data;
            } catch (error) {
                console.error("Error fetching call sub categories:", error);
            }
        },
        async fetchRequiredFieldsConfigs() {
            try {
                const response = await axios.get(
                    `/required-fields-configs/${this.id}`
                );
                this.requiredFieldsConfigsData = response.data;
                await this.getCategoryById();
            } catch (error) {
                console.error("Error fetching call categories:", error);
            }
        },
        async getCategoryById() {
            try {
                const response = await axios.get(
                    `/get-category/${this.requiredFieldsConfigsData.call_type_id}`
                );
                this.callCategories = response.data;
                await this.getSubCategoryById();
            } catch (error) {
                console.error("Error fetching call categories:", error);
            }
        },
        async updateRequiredFieldsData() {
            try {
                await axios.put(
                    `/required-fields-configs/${this.id}`,
                    this.requiredFieldsConfigsData
                );
                this.requiredFieldsConfigsData = {};
                this.$router.push({ name: "required-fields-config-index" });
            } catch (error) {
                console.error("Error creating required fields config:", error);
            }
        },
    },
};
</script>
