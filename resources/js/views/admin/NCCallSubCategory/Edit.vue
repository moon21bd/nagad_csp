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
                    <option v-for="category in callCategoriesForType" :key="category.id" :value="category.id">
                        {{ category.call_category_name }}
                    </option>
                </select>
            </div>
            <div>
                <label for="call_sub_category_name">Sub Category Name:</label>
                <input type="text" v-model="subCategory.call_sub_category_name" required/>
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
        const subCategoryId = this.$route.params.id;
        if (subCategoryId) {
            this.fetchSubCategory(subCategoryId);
        }
    },
    methods: {
        async fetchSubCategory(id) {
            try {
                const response = await this.$store.dispatch('fetchCallSubCategory', id);
                console.log('API Response:', response);

                if (response && response.call_type_id && response.call_category_id) {
                    this.subCategory = {
                        ...response,
                        call_type: { id: response.call_type_id },
                        call_category: { id: response.call_category_id },
                    };
                } else {
                    console.error('Response or its nested properties are null or undefined.');
                }
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
        },
        fetchCategories() {
            if (this.subCategory.call_type_id) {
                this.$store.dispatch('fetchCategoriesByType', this.subCategory.call_type_id);
            }
        }
    }
};
</script>
