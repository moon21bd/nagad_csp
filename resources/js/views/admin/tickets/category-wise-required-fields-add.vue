<template>
    <div>
        <form>
            <!-- Call Type Select -->
            <div class="form-group">
                <label for="name">Call Type</label>
                <select
                    v-model="ticketInfos.callTypeId"
                    @change="fetchCategories"
                    required
                >
                    <option :value="null" disabled>Service Type</option>
                    <option
                        v-for="types in callTypes"
                        :key="types.id"
                        :value="types.id"
                    >
                        {{ types.call_type_name }}
                    </option>
                </select>
            </div>

            <!-- Call Category Select -->

            <div class="form-group">
                <label for="name">Call Category</label>
                <select
                    v-model="ticketInfos.callCategoryId"
                    @change="fetchSubCategory"
                    required
                >
                    <option :value="null" disabled>Service Category</option>
                    <option
                        v-for="category in callCategories"
                        :key="category.id"
                        :value="category.id"
                    >
                        {{ category.call_category_name }}
                    </option>
                </select>
            </div>

            <!-- Call Sub Category Select -->

            <div class="form-group">
                <label for="name">Service Sub Category</label>
                <select
                    v-model="ticketInfos.callSubCategoryId"
                    @change="fetchRequiredFields"
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
                        {{ subCategory.call_sub_category_name }}
                    </option>
                </select>
            </div>

            <!-- Dynamic Inputs -->
            <div v-if="requiredFields">
                <div v-for="(data, index) in requiredFields" :key="index">
                    <div class="form-group" v-if="data.input_type === 'select'">
                        <label for="input_field_name">{{
                            data.input_field_name
                        }}</label>
                        <select
                            class="form-control"
                            v-model="
                                ticketInfos.requiredField[
                                    data.id + '|' + data.input_field_name
                                ]
                            "
                        >
                            <option
                                v-for="(options, index) in inputTypeValues[
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
                        v-else-if="data.input_type === 'datetime'"
                    >
                        <label for="exampleFormControlSelect1">{{
                            data.input_field_name
                        }}</label>

                        <el-date-picker
                            class="d-block w-100"
                            format="YYYY-MM-DD h:i:s"
                            v-model="
                                ticketInfos.requiredField[
                                    data.id + '|' + data.input_field_name
                                ]
                            "
                            type="datetime"
                            placeholder="Select date and time"
                        >
                        </el-date-picker>
                    </div>
                    <div class="form-group" v-else>
                        <label for="name">{{ data.input_field_name }}</label>
                        <input
                            type="text"
                            v-model="
                                ticketInfos.requiredField[
                                    data.id + '|' + data.input_field_name
                                ]
                            "
                            class="form-control"
                            :placeholder="'Enter ' + data.input_field_name"
                        />
                    </div>
                </div>
            </div>

            <button
                type="submit"
                @click.prevent="submit"
                class="btn btn-primary"
            >
                Save
            </button>
        </form>
    </div>
</template>

<script>
export default {
    name: "subCategoryDetailsAdd",
    data() {
        return {
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
