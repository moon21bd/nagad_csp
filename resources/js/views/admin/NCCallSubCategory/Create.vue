<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                to="/admin/call-sub-categories"
            ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Create Call Sub Category</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt=""/>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <form @submit.prevent="createSubCategory">
                            <!--                            <div>
                                                            <label for="call_type_id">Call Type:</label>
                                                            <select v-model="subCategory.call_type_id" @change="fetchCategories" required>
                                                                <option :value="null" disabled>Select Call Type</option>
                                                                <option v-for="type in callTypes" :key="type.id" :value="type.id">
                                                                    {{ type.call_type_name }}
                                                                </option>
                                                            </select>
                                                        </div>-->

                            <div class="form-group">
                                <label class="control-label">Call Type</label>
                                <el-select
                                    class="d-block w-100"
                                    v-model="subCategory.call_type_id"
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


                            <div class="form-group">
                                <label class="control-label">Call Category</label>
                                <el-select
                                    class="d-block w-100"
                                    v-model="subCategory.call_category_id"
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
                            <div class="form-group">
                                <label class="control-label">Sub Category Name</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="subCategory.call_sub_category_name"
                                    required
                                />
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="control-label m-0 mr-3">Status:</label>
                                <label class="radio mr-2">
                                    <input
                                        type="radio"
                                        value="active"
                                        v-model="subCategory.status"
                                        required/>
                                    <span class="radio-mark"></span>Active
                                </label>
                                <label class="radio">
                                    <input
                                        type="radio"
                                        value="inactive"
                                        v-model="subCategory.status"
                                        required
                                    /><span class="radio-mark"></span>Inactive
                                </label>
                            </div>
                            <button class="btn btn-site" type="submit">
                                Create
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
    data() {
        return {
            isLoading: false,
            subCategory: {
                call_type_id: null,
                call_category_id: null,
                call_sub_category_name: '',
                status: 'active'
            },
            callTypes: [],
            callCategories: []
        };
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
                const response = await axios.get(`/get-category/${this.subCategory.call_type_id}`);
                this.callCategories = response.data;
            } catch (error) {
                console.error('Error fetching call categories:', error);
            }
        },
        async createSubCategory() {
            try {
                await axios.post('/call-sub-categories', this.subCategory);
                this.$router.push({name: "call-sub-categories-index"})

            } catch (error) {
                console.error('Error creating call sub-category:', error);
            }
        },
    },
    mounted() {
        this.fetchCallTypes()
    }
};
</script>
