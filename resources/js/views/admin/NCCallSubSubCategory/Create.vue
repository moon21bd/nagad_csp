<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                to="/admin/call-sub-sub-categories"
                ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Create Call Sub Sub Category</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <form @submit.prevent="createSubSubCategory">
                            <div class="form-group">
                                <label class="control-label">Call Type</label>
                                <div class="custom-style">
                                    <select
                                        @change="fetchCategories"
                                        class="form-control"
                                        v-model="subSubCategory.call_type_id"
                                        required
                                    >
                                        <option selected value="" disabled>
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
                            <div class="form-group">
                                <label class="control-label"
                                    >Call Category</label
                                >
                                <el-select
                                    class="d-block w-100"
                                    v-model="subSubCategory.call_category_id"
                                    required
                                    filterable
                                    placeholder="Select Category"
                                >
                                    <el-option
                                        v-for="category in callCategoriesForType"
                                        :key="category.id"
                                        :label="category.call_category_name"
                                        :value="category.id"
                                    >
                                    </el-option>
                                </el-select>
                            </div>
                            <div class="form-group">
                                <label class="control-label"
                                    >Call Sub Category</label
                                >
                                <el-select
                                    class="d-block w-100"
                                    v-model="
                                        subSubCategory.call_sub_category_id
                                    "
                                    required
                                    filterable
                                    placeholder="Select Call Sub Category"
                                >
                                    <el-option
                                        v-for="subCategory in callSubCategoriesForCategory"
                                        :key="subCategory.id"
                                        :label="
                                            subCategory.call_sub_category_name
                                        "
                                        :value="subCategory.id"
                                    >
                                    </el-option>
                                </el-select>
                            </div>
                            <div class="form-group">
                                <label class="control-label"
                                    >Sub Sub Category Name</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="
                                        subSubCategory.call_sub_sub_category_name
                                    "
                                    required
                                />
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="control-label m-0 mr-3"
                                    >Status:</label
                                >
                                <label class="radio mr-2"
                                    ><input
                                        type="radio"
                                        value="active"
                                        v-model="subSubCategory.status"
                                        required
                                    /><span class="radio-mark"></span>Active
                                </label>
                                <label class="radio">
                                    <input
                                        type="radio"
                                        value="inactive"
                                        v-model="subSubCategory.status"
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
            subSubCategory: {
                call_type_id: "",
                call_category_id: null,
                call_sub_category_id: null,
                call_sub_sub_category_name: "",
                status: "active",
            },
        };
    },
    computed: {
        callTypes() {
            return this.$store.getters.callTypes;
        },
        callCategories() {
            return this.$store.getters.callCategories;
        },
        callSubCategories() {
            return this.$store.getters.callSubCategories;
        },
        callCategoriesForType() {
            if (!this.subSubCategory.call_type_id) return [];
            return this.callCategories.filter(
                (category) =>
                    category.call_type_id === this.subSubCategory.call_type_id
            );
        },
        callSubCategoriesForCategory() {
            if (!this.subSubCategory.call_category_id) return [];
            return this.callSubCategories.filter(
                (subCategory) =>
                    subCategory.call_category_id ===
                    this.subSubCategory.call_category_id
            );
        },
    },
    methods: {
        async fetchCategories() {
            if (!this.subSubCategory.call_type_id) return;
            try {
                // Optionally fetch categories if they haven't been fetched before.
                // await this.$store.dispatch('fetchCallCategories');
            } catch (error) {
                console.error("Error fetching call categories:", error);
            }
        },
        async createSubSubCategory() {
            try {
                await this.$store.dispatch(
                    "createCallSubSubCategory",
                    this.subSubCategory
                );
                this.$router.push("/admin/call-sub-sub-categories");
            } catch (error) {
                console.error("Error creating sub sub-category:", error);
            }
        },
    },
    created() {
        // Fetch initial data
        this.$store.dispatch("fetchCallTypes");
        this.$store.dispatch("fetchCallCategories");
        this.$store.dispatch("fetchCallSubCategories");
    },
};
</script>
