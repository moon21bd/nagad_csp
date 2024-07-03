<template>
    <div>
        <h1>Create Call Sub Category</h1>
        <form @submit.prevent="createSubCategory">
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
            <button type="submit">Create</button>
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
    methods: {
        async fetchCategories() {
            if (!this.subCategory.call_type_id) return;
            try {
                // Optionally, you can fetch categories here if they haven't been fetched before.
                // await this.$store.dispatch('fetchCallCategories');
            } catch (error) {
                console.error('Error fetching call categories:', error);
            }
        },
        async createSubCategory() {
            try {
                await this.$store.dispatch('createCallSubCategory', this.subCategory);
                this.$router.push('/admin/call-sub-categories');
            } catch (error) {
                console.error('Error creating sub-category:', error);
            }
        }
    }
};
</script>
