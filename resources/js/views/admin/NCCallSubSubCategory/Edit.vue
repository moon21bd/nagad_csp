<template>
    <div>
        <h1>Edit Call Sub Sub Category</h1>
        <form @submit.prevent="updateSubSubCategory">
            <div>
                <label for="call_type_id">Call Type:</label>
                <select v-model="subSubCategory.call_type_id" @change="fetchCategories" required>
                    <option :value="null" disabled>Select Call Type</option>
                    <option v-for="type in callTypes" :key="type.id" :value="type.id">{{ type.call_type_name }}</option>
                </select>
            </div>
            <div>
                <label for="call_category_id">Call Category:</label>
                <select v-model="subSubCategory.call_category_id" required>
                    <option :value="null" disabled>Select Call Category</option>
                    <option v-for="category in callCategoriesForType" :key="category.id" :value="category.id">{{ category.call_category_name }}</option>
                </select>
            </div>
            <div>
                <label for="call_sub_category_id">Call Sub Category:</label>
                <select v-model="subSubCategory.call_sub_category_id" required>
                    <option :value="null" disabled>Select Call Sub Category</option>
                    <option v-for="subCategory in callSubCategoriesForCategory" :key="subCategory.id" :value="subCategory.id">{{ subCategory.call_sub_category_name }}</option>
                </select>
            </div>
            <div>
                <label for="call_sub_sub_category_name">Sub Sub Category Name:</label>
                <input type="text" v-model="subSubCategory.call_sub_sub_category_name" required />
            </div>
            <div>
                <label for="status">Status:</label>
                <select v-model="subSubCategory.status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit">Update</button>
        </form>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
    data() {
        return {
            subSubCategory: {
                call_type_id: null,
                call_category_id: null,
                call_sub_category_id: null,
                call_sub_sub_category_name: '',
                status: ''
            }
        };
    },
    computed: {
        ...mapGetters('globalStore', ['singleCallSubSubCategory']),
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
            return this.callCategories.filter(category => category.call_type_id === this.subSubCategory.call_type_id);
        },
        callSubCategoriesForCategory() {
            if (!this.subSubCategory.call_category_id) return [];
            return this.callSubCategories.filter(subCategory => subCategory.call_category_id === this.subSubCategory.call_category_id);
        }
    },
    methods: {
        async fetchCategories() {
            if (!this.subSubCategory.call_type_id) return;
            try {
                // Optionally fetch categories if they haven't been fetched before.
                // await this.$store.dispatch('fetchCallCategories');
            } catch (error) {
                console.error('Error fetching call categories:', error);
            }
        },
        async updateSubSubCategory() {
            try {
                await this.$store.dispatch('globalStore/updateCallSubSubCategory', {
                    id: this.$route.params.id,
                    data: this.subSubCategory
                });
                this.$router.push('/admin/call-sub-sub-categories');
            } catch (error) {
                console.error('Error updating sub sub-category:', error);
            }
        }
    },
    created() {
        this.$store.dispatch('globalStore/fetchCallTypes');
        this.$store.dispatch('globalStore/fetchCallCategories');
        this.$store.dispatch('globalStore/fetchCallSubCategories');
        this.$store.dispatch('globalStore/fetchCallSubSubCategory', this.$route.params.id).then(() => {
            const category = this.singleCallSubSubCategory;
            this.subSubCategory.call_type_id = category.call_type_id;
            this.subSubCategory.call_category_id = category.call_category_id;
            this.subSubCategory.call_sub_category_id = category.call_sub_category_id;
            this.subSubCategory.call_sub_sub_category_name = category.call_sub_sub_category_name;
            this.subSubCategory.status = category.status;
        });
    }
};
</script>
