<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title m-0">Required fields configuration</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <form>
                            <div class="form-row">
                                <div class="col-md-4 form-group">
                                    <label class="control-label"
                                        >Call Type <sup>*</sup></label
                                    >
                                    <div class="custom-style">
                                        <select
                                            class="form-control"
                                            v-model="ticketInfos.callTypeId"
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
                                        >Call Category <sup>*</sup></label
                                    >

                                    <el-select
                                        class="d-block w-100"
                                        v-model="ticketInfos.callCategoryId"
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

                                    <el-select
                                        class="d-block w-100"
                                        v-model="ticketInfos.callSubCategoryId"
                                        @change="fetchRequiredFields"
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
                                <!-- Dynamic Inputs -->
                                <div v-if="requiredFields" class="col-md-12">
                                    <div class="form-row">
                                        <div
                                            class="col-md-6"
                                            v-for="(
                                                data, index
                                            ) in requiredFields"
                                            :key="index"
                                        >
                                            <div
                                                class="form-group"
                                                v-if="
                                                    data.input_type === 'select'
                                                "
                                            >
                                                <label for="input_field_name">{{
                                                    data.input_field_name
                                                }}</label>
                                                <select
                                                    class="form-control"
                                                    v-model="
                                                        ticketInfos
                                                            .requiredField[
                                                            data.id +
                                                                '|' +
                                                                data.input_field_name
                                                        ]
                                                    "
                                                >
                                                    <option
                                                        v-for="(
                                                            options, index
                                                        ) in inputTypeValues[
                                                            index
                                                        ].input_value"
                                                        :value="options"
                                                        :key="index"
                                                    >
                                                        {{ options }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div
                                                class="form-group"
                                                v-else-if="
                                                    data.input_type ===
                                                    'datetime'
                                                "
                                            >
                                                <label
                                                    for="exampleFormControlSelect1"
                                                    >{{
                                                        data.input_field_name
                                                    }}</label
                                                >

                                                <el-date-picker
                                                    class="d-block w-100"
                                                    format="YYYY-MM-DD h:i:s"
                                                    v-model="
                                                        ticketInfos
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
                                                <label for="name">{{
                                                    data.input_field_name
                                                }}</label>
                                                <input
                                                    type="text"
                                                    v-model="
                                                        ticketInfos
                                                            .requiredField[
                                                            data.id +
                                                                '|' +
                                                                data.input_field_name
                                                        ]
                                                    "
                                                    class="form-control"
                                                    :placeholder="
                                                        'Enter ' +
                                                        data.input_field_name
                                                    "
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button
                                type="submit"
                                @click.prevent="submit"
                                class="btn btn-site"
                            >
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
    name: "subCategoryDetailsAdd",
    data() {
        return {
            isLoading: false,
            requiredFields: [],
            inputTypeValues: [],
            callTypes: [],
            callCategories: [],
            callSubCategories: [],

            ticketInfos: {
                callTypeId: null,
                callCategoryId: null,
                callSubCategoryId: null,
                requiredField: [],
            },
        };
    },
    mounted() {
        this.fetchCallTypes();
    },
    methods: {
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
                console.error("Error fetching call categories:", error);
            }
        },
        async fetchSubCategory() {
            try {
                const response = await axios.get(
                    `/call-sub-by-call-cat-id/${this.ticketInfos.callTypeId}/${this.ticketInfos.callCategoryId}`
                );

                this.callSubCategories = response.data;
            } catch (error) {
                console.error("Error fetching call sub categories:", error);
            }
        },
        async submit() {
            console.log("this.ticketInfos", this.ticketInfos);
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
    },
};
</script>
