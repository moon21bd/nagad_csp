<template>
    <div>
        <h1>Edit Call Sub Category</h1>
        <form @submit.prevent="updateSubCategory">
            <div>
                <label for="call_type_id">Call Type:</label>
                <select v-model="subCategory.call_type_id" @change="fetchCategories" required>
                    <option :value="null" disabled>Select Call Type</option>
                    <option v-for="type in callTypes" :key="type.id" :value="type.id">{{ type.call_type_name }}</option>
                </select>
            </div>
            <div>
                <label for="call_category_id">Call Category:</label>
                <select v-model="subCategory.call_category_id" required>
                    <option :value="null" disabled>Select Call Category</option>
                    <option v-for="category in callCategoriesForType" :key="category.id" :value="category.id">{{ category.call_category_name }}</option>
                </select>
            </div>
            <div>
                <label for="call_sub_category_name">Sub Category Name:</label>
                <input type="text" v-model="subCategory.call_sub_category_name" required />
            </div>
            <div>
                <label for="status">Status:</label>
                <select v-model="subCategory.status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit">Update</button>
        </form>
    </div>
</template>

<script>
export default {
    data() {
        return {
            subCategory: {
                id: null,
                call_type_id: null,
                call_category_id: null,
                call_sub_category_name: '',
                status: 'active'
            }
        };
    },
    computed: {
        callTypes() {
            return this.$store.getters.callTypes;
        },
        callCategories() {
            return this.$store.getters.callCategories;
        },
        callCategoriesForType() {
            if (!this.subCategory.call_type_id) return [];
            return this.callCategories.filter(category => category.call_type_id === this.subCategory.call_type_id);
        }
    },
    created() {
        // Fetch sub-category details from API based on route param or Vuex state
        const subCategoryId = this.$route.params.id;
        if (subCategoryId) {
            this.fetchSubCategory(subCategoryId);
        }
    },
    methods: {
        async fetchSubCategory(id) {
            try {
                const response = await this.$store.dispatch('fetchCallSubCategory', id);
                this.subCategory = response.data;
            } catch (error) {
                console.error('Error fetching sub-category:', error);
            }
        },
        async updateSubCategory() {
            try {
                await this.$store.dispatch('updateCallSubCategory', this.subCategory);
                this.$router.push('/admin/call-sub-categories');
            } catch (error) {
                console.error('Error updating sub-category:', error);
            }
        }
    }
};
</script>
