<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                to="/admin/call-categories"
                ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Edit Call Category</h1>
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
                                <label class="control-label">Call Type</label>
                                <div class="custom-style">
                                    <select
                                        class="form-control"
                                        v-model="category.call_type_id"
                                        required
                                    >
                                        <option value="" disabled>
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
                                    >Category Name</label
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
                                    >Status:</label
                                >
                                <label class="radio mr-2"
                                    ><input
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
        };
    },
    computed: {
        callTypes() {
            return this.$store.getters.callTypes;
        },
    },
    async created() {
        await this.$store.dispatch("fetchCallTypes");
        await this.fetchCategory();
    },
    methods: {
        async fetchCategory() {
            try {
                const categoryId = this.$route.params.id;
                const categories = await this.$store.dispatch(
                    "fetchCallCategories"
                );
                this.category = categories.find(
                    (category) => category.id === parseInt(categoryId)
                );
                if (!this.category) {
                    console.error("Category not found");
                    this.$router.push("/admin/nc-call-categories");
                }
            } catch (error) {
                console.error("Error fetching category:", error);
            }
        },
        async updateCategory() {
            try {
                await this.$store.dispatch("updateCallCategory", this.category);
                this.$router.push("/admin/call-categories");
            } catch (error) {
                console.error("Error updating category:", error);
            }
        },
    },
};
</script>
