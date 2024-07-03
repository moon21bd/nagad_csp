import Vue from "vue";
import Vuex from "vuex";
import createPersistedState from "vuex-persistedstate";
import axios from "axios";

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        user: null,
        groups: [],
        callTypes: [],
        callCategories: [],
        callSubCategories: []
    },
    getters: {
        user: (state) => state.user,
        groups: (state) => state.groups,
        callTypes: (state) => state.callTypes,
        callCategories: (state) => state.callCategories,
        callSubCategories: state => state.callSubCategories

    },
    actions: {
        user({commit}, user) {
            commit("setUser", user);
        },
        async fetchGroups({commit}) {
            try {
                const response = await axios.get("/groups");
                commit("setGroups", response.data);
            } catch (error) {
                console.error("Error fetching groups:", error);
            }
        },
        async fetchCallCategories({commit}) {
            try {
                const response = await axios.get("/call-categories");
                commit("setCallCategories", response.data);
                return response.data;
            } catch (error) {
                console.error("Error fetching call categories:", error);
            }
        },
        async createCallCategory({commit}, categoryData) {
            try {
                const response = await axios.post('/call-categories', categoryData);
                commit('addCallCategory', response.data);
            } catch (error) {
                console.error('Error creating call category:', error);
            }
        },
        async updateCallCategory({commit}, categoryData) {
            try {
                const response = await axios.put(`/call-categories/${categoryData.id}`, categoryData);
                commit('updateCallCategory', response.data);
            } catch (error) {
                console.error('Error updating call category:', error);
            }
        },
        async deleteCallCategory({commit}, categoryId) {
            try {
                await axios.delete(`/call-categories/${categoryId}`);
                commit('removeCallCategory', categoryId);
            } catch (error) {
                console.error('Error deleting call category:', error);
            }
        },
        async fetchCallSubCategories({commit}) {
            try {
                const response = await axios.get('/call-sub-categories');
                commit('setCallSubCategories', response.data);
                return response.data;
            } catch (error) {
                console.error('Error fetching call sub-categories:', error);
            }
        },
        async createCallSubCategory({commit}, subCategoryData) {
            try {
                const response = await axios.post('/call-sub-categories', subCategoryData);
                commit('addCallSubCategory', response.data);
            } catch (error) {
                console.error('Error creating call sub-category:', error);
            }
        },
        async updateCallSubCategory({commit}, subCategoryData) {
            try {
                const response = await axios.put(`/call-sub-categories/${subCategoryData.id}`, subCategoryData);
                commit('updateCallSubCategory', response.data);
            } catch (error) {
                console.error('Error updating call sub-category:', error);
            }
        },
        async deleteCallSubCategory({commit}, subCategoryId) {
            try {
                await axios.delete(`/call-sub-categories/${subCategoryId}`);
                commit('removeCallSubCategory', subCategoryId);
            } catch (error) {
                console.error('Error deleting call sub-category:', error);
            }
        },
        async fetchCallTypes({commit}) {
            try {
                const response = await axios.get("/call-types");
                commit("setCallTypes", response.data);
                return response.data;
            } catch (error) {
                console.error("Error fetching call types:", error);
            }
        },
        async createCallType({dispatch}, callTypeData) {
            try {
                await axios.post("/call-types", callTypeData);
                dispatch("fetchCallTypes");
            } catch (error) {
                console.error("Error creating call type:", error);
            }
        },
        async updateCallType({commit}, callType) {
            try {
                const response = await axios.put(`/call-types/${callType.id}`, callType);
                commit('updateCallType', response.data);
            } catch (error) {
                console.error('Error updating call type:', error);
            }
        },
        async deleteCallType({commit}, callTypeId) {
            try {
                await axios.delete(`/call-types/${callTypeId}`);
                commit("removeCallType", callTypeId);
            } catch (error) {
                console.error("Error deleting call type:", error);
            }
        },
    },
    mutations: {
        setUser(state, user) {
            state.user = user;
        },
        setGroups(state, groups) {
            state.groups = groups;
        },
        setCallCategories(state, callCategories) {
            state.callCategories = callCategories;
        },
        addCallCategory(state, newCategory) {
            state.callCategories.push(newCategory);
        },
        updateCallCategory(state, updatedCategory) {
            const index = state.callCategories.findIndex(category => category.id === updatedCategory.id);
            if (index !== -1) {
                Vue.set(state.callCategories, index, updatedCategory);
            }
        },
        removeCallCategory(state, categoryId) {
            state.callCategories = state.callCategories.filter(category => category.id !== categoryId);
        },
        setCallSubCategories(state, callSubCategories) {
            state.callSubCategories = callSubCategories;
        },
        addCallSubCategory(state, newSubCategory) {
            state.callSubCategories.push(newSubCategory);
        },
        updateCallSubCategory(state, updatedSubCategory) {
            const index = state.callSubCategories.findIndex(subCategory => subCategory.id === updatedSubCategory.id);
            if (index !== -1) {
                Vue.set(state.callSubCategories, index, updatedSubCategory);
            }
        },
        removeCallSubCategory(state, subCategoryId) {
            state.callSubCategories = state.callSubCategories.filter(subCategory => subCategory.id !== subCategoryId);
        },
        setCallTypes(state, callTypes) {
            state.callTypes = callTypes;
        },
        updateCallType(state, updatedCallType) {
            const index = state.callTypes.findIndex(type => type.id === updatedCallType.id);
            if (index !== -1) {
                Vue.set(state.callTypes, index, updatedCallType);
            }
        },
        removeCallType(state, callTypeId) {
            state.callTypes = state.callTypes.filter(
                (type) => type.id !== callTypeId
            );
        },
    },
    plugins: [createPersistedState()],
});

export default store;
