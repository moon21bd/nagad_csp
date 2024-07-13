<template>
    <div>
        <form @submit.prevent="createRequiredFields">
            <div class="form-group">
                <label for="name">Call Type Id</label>
                <select v-model="callTypeId" @change="fetchCategories" required>
                    <option :value="null" disabled>Select Call Type</option>
                    <option v-for="type in callTypes" :key="type.id" :value="type.id">
                        {{ type.call_type_name }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="name">Call Category Id</label>
                <select v-model="callCategoryId" @change="fetchSubCategory" required>
                    <option :value="null" disabled>Select Call Category</option>
                    <option v-for="category in callCategories"
                            :key="category.id"
                            :value="category.id">
                        {{ category.call_category_name }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="name">Call Sub Category Id</label>
                <select v-model="callSubCategoryId" required>
                    <option :value="null" disabled>Select Call Sub Category</option>
                    <option v-for="subCategory in callSubCategories"
                            :key="subCategory.id"
                            :value="subCategory.id">
                        {{ subCategory.call_sub_category_name }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="name">Input Field Name</label>
                <input type="text" v-model="inputFiledName" class="form-control" id=""
                       placeholder="Enter Input Field Name">
            </div>
            <div class="form-group">
                <label for="exampleFormControlSelect1">Input Type</label>
                <select class="form-control" v-model="inputType" id="">
                    <option value="select">Select/Option</option>
                    <option value="integer">Number</option>
                    <option value="varchar">String</option>
                    <option value="text">Text</option>
                    <option value="datetime">DateTime</option>
                </select>
            </div>
            <div class="form-group">
                <label for="name">Input Value</label>
                <input type="text" v-model="inputValue" class="form-control" id="" placeholder="Enter Input Value">
            </div>
            <div class="form-group">
                <label for="name">Input Validation Rules</label>
                <input type="text" v-model="inputValidation" class="form-control" id=""
                       placeholder="Enter Input Validation Rules">
            </div>
            <div class="form-group">
                <label for="exampleFormControlSelect1">Status</label>
                <select class="form-control" v-model="statusValue" id="status">
                    <option value="active" selected>Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</template>

<script>
export default {
    name: 'Create',
    data() {
        return {
            name: '',
            statusValue: '',
            actionUrl: 'store_config',
            apiUrl: '',
            callTypeId: null,
            callCategoryId: null,
            callSubCategoryId: null,
            inputValue: '',
            inputType: '',
            inputFiledName: '',
            inputValidation: 'required,',
            callTypes: [],
            callCategories: {},
            callSubCategories: {},
        }
    },
    mounted() {
        this.fetchCallTypes()
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
                const response = await axios.get(`/get-category/${this.callTypeId}`);
                this.callCategories = response.data;
                console.log('call category', this.callCategories)
            } catch (error) {
                console.error('Error fetching call categories:', error);
            }
        },
        async fetchSubCategory() {
            try {
                const response = await axios.get(`/call-sub-by-call-cat-id/${this.callTypeId}/${this.callCategoryId}`);
                this.callSubCategories = response.data;
            } catch (error) {
                console.error('Error fetching call sub categories:', error);
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
                    statusValue: this.statusValue
                };
                await axios.post('/required-fields-configs', postData);
                // this.category = {};
                this.$router.push({name: "required-fields-config-index"})
            } catch (error) {
                console.error('Error creating required fields config:', error);
            }
        },

    }
}
</script>
