<template>
    <div>
        <form>
            <div class="form-group">
                <label for="name">Call Type Id</label>
                <select v-model="ncConfigsData.call_type_id" @change="fetchCategories" required>
                    <option :value="null" disabled>Select Call Type</option>
                    <option v-for="type in callTypes" :key="type.id" :value="type.id">
                        {{ type.call_type_name }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="name">Call Category Id</label>
                <select v-model="ncConfigsData.call_category_id" @change="fetchSubCategory" required>
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
                <select v-model="ncConfigsData.call_sub_category_id" required>
                    <option :value="null" disabled>Select Call Sub Category</option>
                    <option v-for="subCategory in callSubCategories"
                            :key="subCategory.id"
                            :value="subCategory.id">
                        {{ subCategory.call_sub_category_name }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="name">Input Filed Name</label>
                <input type="text" v-model="ncConfigsData.input_field_name" class="form-control" id=""
                       placeholder="Enter Call Type Name">
            </div>
            <div class="form-group">
                <label for="exampleFormControlSelect1">Input Type</label>
                <select class="form-control" v-model="ncConfigsData.input_type" id="">
                    <option value="integer">Number</option>
                    <option value="varchar">String</option>
                    <option value="select">Select/Option</option>
                    <option value="text">Text</option>
                    <option value="datetime">DateTime</option>
                </select>
            </div>
            <div class="form-group">
                <label for="name">Input Value</label>
                <input type="text" v-model="ncConfigsData.input_value" class="form-control" id="" placeholder="Enter Call Type Name">
            </div>
            <div class="form-group">
                <label for="name">Input Validation</label>
                <input type="text" v-model="ncConfigsData.input_validation" class="form-control" id=""
                       placeholder="Enter Call Type Name">
            </div>
            <div class="form-group">
                <label for="exampleFormControlSelect1">Status</label>
                <select class="form-control" v-model="ncConfigsData.status" id="status">
                    <option value="active" selected>Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button @click.prevent="submit" type="submit" class="btn btn-primary">Save</button>
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
            id: this.$route.params.id,
            ncConfigsData:{
                call_type_id: null,
                call_category_id: null,
                call_sub_category_id: '',
                input_field_name:'',
                input_type:'',
                input_value:'',
                input_validation:'',
                status: 'active'

            },
        }
    },
    mounted() {
        this.fetchCallTypes()
        this.fetchNcConfigs()
        // this.fetchCategories()
    },
    methods: {
        async init() {

        },
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
                const response = await axios.get(`/get-sub-category/${this.callCategoryId}`);
                this.callSubCategories = response.data;
            } catch (error) {
                console.error('Error fetching call sub categories:', error);
            }
        },
        async fetchNcConfigs() {
            try {
                const response = await axios.get(`/store_config/${this.id}`);
                this.ncConfigsData = response.data;
                 await this.getCategoryById()
                console.log('this-config-data',this.ncConfigsData.call_type_id)
            } catch (error) {
                console.error('Error fetching call categories:', error);
            }
        },
        async getCategoryById() {
            try {
                const response = await axios.get(`/get-category/${this.ncConfigsData.call_type_id}`);
                this.callCategories = response.data;
                await this.getSubCategoryById()
            } catch (error) {
                console.error('Error fetching call categories:', error);
            }
        },
        async getSubCategoryById() {
            try {
                const response = await axios.get(`/get-sub-category/${this.ncConfigsData.call_category_id}`);
                this.callSubCategories = response.data;
            } catch (error) {
                console.error('Error fetching call categories:', error);
            }
        },
        updateData() {
            return new Promise((resolve, reject) => {
                axios
                    .put(`/${this.actionUrl}/${this.id}`, this.ncConfigsData)
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
        async submit() {
            await this.updateData().then(response => {
            })
            this.$router.push({name:"configList"})
        }
    }
}
</script>
