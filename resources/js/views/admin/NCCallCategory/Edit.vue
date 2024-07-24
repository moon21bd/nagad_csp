<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                :to="{ name: 'service-categories-index' }"
            >
                <i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Edit Service Category</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <form @submit.prevent="updateCategory">
                            <div class="form-group">
                                <label class="control-label"
                                    >Service Type<sup>*</sup></label
                                >
                                <el-select
                                    class="d-block w-100"
                                    v-model="category.call_type_id"
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
                                <label class="control-label"
                                    >Category Name<sup>*</sup></label
                                >
                                <input
                                    class="form-control"
                                    v-model="category.call_category_name"
                                    type="text"
                                    required
                                />
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="control-label m-0 mr-3"
                                    >Status<sup>*</sup></label
                                >
                                <label class="radio mr-2">
                                    <input
                                        type="radio"
                                        value="active"
                                        v-model="category.status"
                                        required
                                    /><span class="radio-mark"></span>Active
                                </label>
                                <label class="radio">
                                    <input
                                        type="radio"
                                        value="inactive"
                                        v-model="category.status"
                                        required
                                    /><span class="radio-mark"></span>Inactive
                                </label>
                            </div>
                            <button class="btn btn-site" type="submit">
                                Update
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
    data() {
        return {
            isLoading: false,
            category: {
                id: "",
                call_type_id: "",
                call_category_name: "",
                status: "active",
            },
            callTypes: [],
            id: this.$route.params.id,
        };
    },
    methods: {
        async fetchCategory() {
            try {
                const response = await axios.get("/call-categories/" + this.id);
                this.category = response.data;
                console.log("response", response.data);
            } catch (error) {
                console.error("Error fetching service type:", error);
            }
        },
        async fetchCallTypes() {
            try {
                const response = await axios.get("/get-service-types");
                this.callTypes = response.data;
            } catch (error) {
                console.error("Error fetching service types:", error);
            }
        },
        async updateCategory() {
            try {
                await axios.put(`/call-categories/${this.id}`, this.category);
                this.$router.push({ name: "service-categories-index" });
            } catch (error) {
                console.error("Error updating service category:", error);
            }
        },
    },
    mounted() {
        this.fetchCallTypes();
        this.fetchCategory();
    },
};
</script>
