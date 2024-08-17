<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                :to="{ name: 'required-fields-config-index' }"
                ><i class="icon-left"></i>
            </router-link>
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
                            <div class="form-row">
                                <div class="col-md-4 form-group">
                                    <label class="control-label"
                                        >Service Type<sup>*</sup></label
                                    >
                                    <div class="custom-style">
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
                                        class="d-block w-100"
                                        v-model="callCategoryId"
                                        required
                                        filterable
                                        @change="fetchSubCategory"
                                        placeholder="Select Service Category"
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
                                        >Service Sub Category
                                        <sup>*</sup></label
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
                                        >
                                        </el-option>
                                    </el-select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Input Field Name<sup>*</sup></label
                                    >
                                    <input
                                        type="text"
                                        v-model="inputFiledName"
                                        class="form-control"
                                        id=""
                                        placeholder="Enter Input Field Name"
                                    />
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Choose Input Type<sup>*</sup></label
                                    >
                                    <select
                                        class="form-control"
                                        v-model="inputType"
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
                                    v-if="inputType === 'select'"
                                >
                                    <label class="control-label"
                                        >Input Value<sup>*</sup></label
                                    >
                                    <input
                                        type="text"
                                        v-model="inputValue"
                                        class="form-control"
                                        id=""
                                        placeholder="Enter Input Value"
                                    />
                                </div>
                                <div class="col-md-12 form-group">
                                    <label class="control-label"
                                        >Input Validation/Rules<sup
                                            >*</sup
                                        ></label
                                    >
                                    <input
                                        type="text"
                                        v-model="inputValidation"
                                        class="form-control"
                                        id=""
                                        placeholder="Enter Input Validation Rules"
                                    />
                                    <small
                                        v-if="help && help.length > 0"
                                        class="form-text text-muted"
                                        >Help text goes here</small
                                    >
                                </div>

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
                                            v-model="statusValue"
                                            required
                                        /><span class="radio-mark"></span>Active
                                    </label>
                                    <label class="radio">
                                        <input
                                            type="radio"
                                            value="inactive"
                                            v-model="statusValue"
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
export default {
    name: "NCRequiredFieldsCreate",
    data() {
        return {
            isLoading: false,
            name: "",
            statusValue: "",
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
        };
    },
    mounted() {
        this.fetchCallTypes();
    },
    methods: {
        async fetchCallTypes() {
            try {
                const response = await axios.get("/get-service-types");
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
                this.callCategories = response.data;
                console.log("call category", this.callCategories);
            } catch (error) {
                console.error("Error fetching service categories:", error);
            }
        },
        async fetchSubCategory() {
            try {
                const response = await axios.get(
                    `/call-sub-by-call-cat-id/${this.callTypeId}/${this.callCategoryId}`
                );
                this.callSubCategories = response.data;
            } catch (error) {
                console.error("Error fetching service sub categories:", error);
            }
        },
        async createRequiredFields() {
            try {
                const postData = {
                    inputFiledName: this.inputFiledName,
                    callTypeId: this.callTypeId,
                    callCategoryId: this.callCategoryId,
                    callSubCategoryId: this.callSubCategoryId,
                    inputValue: this.inputValue,
                    inputType: this.inputType,
                    inputValidation: this.inputValidation,
                    statusValue: this.statusValue,
                };
                await axios.post("/required-fields-configs", postData);
                // this.category = {};
                this.$router.push({ name: "required-fields-config-index" });
            } catch (error) {
                console.error("Error creating required fields config:", error);
            }
        },
    },
};
</script>
