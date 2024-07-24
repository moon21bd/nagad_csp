import Vue from "vue";
import Vuex from "vuex";
import axios from "../axios";

Vue.use(Vuex);

export default {
    namespaced: true,
    state: {
        permissions: [],
        userPermissions: [],
        groups: [],
        callTypes: [],
        callCategories: [],
        callSubCategories: [],
        callSubSubCategories: [],
        callSubSubCategory: {},
        subCategory: null,
        accessLists: [],
    },
    getters: {
        groups: (state) => state.groups,
        callTypes: (state) => state.callTypes,
        callCategories: (state) => state.callCategories,
        callSubCategories: (state) => state.callSubCategories,
        allCallSubSubCategories: (state) => state.callSubSubCategories,
        singleCallSubSubCategory: (state) => state.callSubSubCategory,
        getSubCategory: (state) => state.subCategory,
        accessLists: (state) => state.accessLists,
        hasPermission: (state) => (permissionName) => {
            return state.permissions.includes(permissionName);
        },
    },
    actions: {
        async updateCallCategory({ commit }, categoryData) {
            try {
                const response = await axios.put(
                    `/call-categories/${categoryData.id}`,
                    categoryData
                );
                commit("updateCallCategory", response.data);
            } catch (error) {
                console.error("Error updating call category:", error);
            }
        },
        async deleteCallCategory({ commit }, categoryId) {
            try {
                await axios.delete(`/call-categories/${categoryId}`);
                commit("removeCallCategory", categoryId);
            } catch (error) {
                console.error("Error deleting call category:", error);
            }
        },

        /*async createCallType({dispatch}, callTypeData) {
            try {
                await axios.post("/call-types", callTypeData);
                dispatch("fetchCallTypes");
            } catch (error) {
                console.error("Error creating call type:", error);
            }
        },*/
        async updateCallType({ commit }, callType) {
            try {
                const response = await axios.put(
                    `/call-types/${callType.id}`,
                    callType
                );
                commit("updateCallType", response.data);
            } catch (error) {
                console.error("Error updating service type:", error);
            }
        },
        async deleteCallType({ commit }, callTypeId) {
            try {
                await axios.delete(`/call-types/${callTypeId}`);
                commit("removeCallType", callTypeId);
            } catch (error) {
                console.error("Error deleting service type:", error);
            }
        },

        async fetchCallSubSubCategory({ commit }, id) {
            const response = await axios.get(`/call-sub-sub-categories/${id}`);
            commit("setCallSubSubCategory", response.data);
        },
        async createCallSubSubCategory({ dispatch }, data) {
            await axios.post("/call-sub-sub-categories", data);
            dispatch("fetchCallSubSubCategories");
        },
        async updateCallSubSubCategory({ dispatch }, { id, data }) {
            await axios.put(`/call-sub-sub-categories/${id}`, data);
            dispatch("fetchCallSubSubCategories");
        },
        async deleteCallSubSubCategory({ dispatch }, id) {
            await axios.delete(`/call-sub-sub-categories/${id}`);
            dispatch("fetchCallSubSubCategories");
        },
        async fetchCallSubCategory({ commit }, subCategoryId) {
            try {
                console.log("subCategoryId", subCategoryId);
                const response = await axios.get(
                    `/call-sub-categories/${subCategoryId}`
                );
                const subCategory = response.data;
                commit("setSubCategory", subCategory);
                return subCategory;
            } catch (error) {
                throw new Error(`Error fetching sub-category: ${error}`);
            }
        },
        async fetchAccessLists({ commit }) {
            try {
                const response = await axios.get("/access-lists");
                commit("setAccessLists", response.data);
            } catch (error) {
                console.error("Error fetching access lists:", error);
            }
        },
    },
    mutations: {
        setGroups(state, groups) {
            state.groups = groups;
        },
        removeGroup(state, groupId) {
            state.groups = state.groups.filter((group) => group.id !== groupId);
        },
        setCallCategories(state, callCategories) {
            state.callCategories = callCategories;
        },
        addCallCategory(state, newCategory) {
            state.callCategories.push(newCategory);
        },
        updateCallCategory(state, updatedCategory) {
            const index = state.callCategories.findIndex(
                (category) => category.id === updatedCategory.id
            );
            if (index !== -1) {
                Vue.set(state.callCategories, index, updatedCategory);
            }
        },
        removeCallCategory(state, categoryId) {
            state.callCategories = state.callCategories.filter(
                (category) => category.id !== categoryId
            );
        },
        setCallSubCategories(state, callSubCategories) {
            state.callSubCategories = callSubCategories;
        },
        addCallSubCategory(state, newSubCategory) {
            state.callSubCategories.push(newSubCategory);
        },
        updateCallSubCategory(state, updatedSubCategory) {
            const index = state.callSubCategories.findIndex(
                (subCategory) => subCategory.id === updatedSubCategory.id
            );
            if (index !== -1) {
                Vue.set(state.callSubCategories, index, updatedSubCategory);
            }
        },
        removeCallSubCategory(state, subCategoryId) {
            state.callSubCategories = state.callSubCategories.filter(
                (subCategory) => subCategory.id !== subCategoryId
            );
        },
        /*setCallTypes(state, callTypes) {
            state.callTypes = callTypes;
        },*/
        updateCallType(state, updatedCallType) {
            const index = state.callTypes.findIndex(
                (type) => type.id === updatedCallType.id
            );
            if (index !== -1) {
                Vue.set(state.callTypes, index, updatedCallType);
            }
        },
        removeCallType(state, callTypeId) {
            state.callTypes = state.callTypes.filter(
                (type) => type.id !== callTypeId
            );
        },
        setCallSubSubCategories: (state, callSubSubCategories) =>
            (state.callSubSubCategories = callSubSubCategories),
        setCallSubSubCategory: (state, callSubSubCategory) =>
            (state.callSubSubCategory = callSubSubCategory),
        setSubCategory(state, subCategory) {
            state.subCategory = subCategory;
            state.subCategory.call_type = subCategory.call_type || null;
            state.subCategory.call_category = subCategory.call_category || null;
        },
        setAccessLists(state, accessLists) {
            state.accessLists = accessLists;
        },
    },
};
