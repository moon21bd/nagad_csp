import Vue from "vue";
import Vuex from "vuex";
import createPersistedState from "vuex-persistedstate";
import axios from "axios";

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        permissions: [],
        userPermissions: [],
        user: null,
        groups: [],
        callTypes: [],
        callCategories: [],
        callSubCategories: [],
        callSubSubCategories: [],
        callSubSubCategory: {},
        subCategory: null,
        accessLists: [],
        ncRequiredConfigs:[],

    },
    getters: {
        user: (state) => state.user,
        groups: (state) => state.groups,
        callTypes: (state) => state.callTypes,
        callCategories: (state) => state.callCategories,
        callSubCategories: state => state.callSubCategories,
        ncRequiredConfigs: state => state.ncRequiredConfigs,
        allCallSubSubCategories: (state) => state.callSubSubCategories,
        singleCallSubSubCategory: (state) => state.callSubSubCategory,
        getSubCategory: state => state.subCategory,
        accessLists: state => state.accessLists,
        hasPermission: (state) => (permissionName) => {
            return state.permissions.includes(permissionName);
        },

    },
    actions: {
        async fetchPermissions({ commit }) {
            try {
                const response = await axios.get('/user/permissions');
                commit('setPermissions', response.data.permissions);
            } catch (error) {
                console.error('Error fetching permissions:', error);
            }
        },
        async fetchUserPermissions({commit}) {
            try {
                const response = await axios.get('/user/permissions');
                commit('setUserPermissions', response.data);
            } catch (error) {
                console.error('Failed to fetch permissions', error);
            }
        },
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
        async fetchCallSubSubCategories({commit}) {
            const response = await axios.get('/call-sub-sub-categories');
            commit('setCallSubSubCategories', response.data);
        },

        async fetchCallSubSubCategory({commit}, id) {
            const response = await axios.get(`/call-sub-sub-categories/${id}`);
            commit('setCallSubSubCategory', response.data);
        },

        async createCallSubSubCategory({dispatch}, data) {
            await axios.post('/call-sub-sub-categories', data);
            dispatch('fetchCallSubSubCategories');
        },

        async updateCallSubSubCategory({dispatch}, {id, data}) {
            await axios.put(`/call-sub-sub-categories/${id}`, data);
            dispatch('fetchCallSubSubCategories');
        },

        async deleteCallSubSubCategory({dispatch}, id) {
            await axios.delete(`/call-sub-sub-categories/${id}`);
            dispatch('fetchCallSubSubCategories');
        },
        async fetchNcRequiredConfig({commit}) {
            try {
                const response = await axios.get('/store_config');
                commit('setNcRequiredConfig', response.data);
                return response.data;
            } catch (error) {
                console.error('Error fetching nc-configs:', error);
            }
        },
        async createNcRequiredConfig({commit}, ncConfigData) {
            try {
                const response = await axios.post('/store_config', ncConfigData);
                commit('addNcRequiredConfig', response.data);
            } catch (error) {
                console.error('Error creating nc-required-config:', error);
            }
        },
        async updateNcRequiredConfig({commit}, ncConfigData) {
            try {
                const response = await axios.put(`/store_config/${ncConfigData.id}`, ncConfigData);
                commit('updateNcRequiredConfig', response.data);
            } catch (error) {
                console.error('Error updating nc-required-config:', error);
            }
        },
        async deleteNcRequiredConfig({commit}, ncConfigData) {
            try {
                await axios.delete(`/store_config/${ncConfigData}`);
                commit('removeNcRequiredConfig', ncConfigData);
            } catch (error) {
                console.error('Error deleting nc-required-config:', error);
            }
        },
        async fetchCallSubCategory({commit}, subCategoryId) {
            try {
                console.log('subCategoryId', subCategoryId)
                const response = await axios.get(`/call-sub-categories/${subCategoryId}`);
                const subCategory = response.data;
                commit('setSubCategory', subCategory);
                return subCategory;
            } catch (error) {
                throw new Error(`Error fetching sub-category: ${error}`);
            }
        },
        async fetchAccessLists({commit}) {
            try {
                const response = await axios.get('/access-lists');
                commit('setAccessLists', response.data);
            } catch (error) {
                console.error('Error fetching access lists:', error);
            }
        }
    },
    mutations: {
        setPermissions(state, permissions) {
            state.permissions = permissions;
        },
        setUserPermissions(state, permissions) {
            state.userPermissions = permissions;
        },
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
        setNcRequiredConfig(state, ncRequiredConfig) {
            state.ncRequiredConfigs = ncRequiredConfig;
        },
        addNcRequiredConfig(state, newNcRequiredConfig) {
            state.ncRequiredConfigs.push(newNcRequiredConfig);
        },
        updateNcRequiredConfig(state, updatedNcRequiredConfig) {
            const index = state.ncRequiredConfigs.findIndex(subCategory => subCategory.id === updatedNcRequiredConfig.id);
            if (index !== -1) {
                Vue.set(state.callSubCategories, index, updatedSubCategory);
            }
        },
        removeNcRequiredConfig(state, subCategoryId) {
            state.callSubCategories = state.callSubCategories.filter(subCategory => subCategory.id !== subCategoryId);
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
        setCallSubSubCategories: (state, callSubSubCategories) => (state.callSubSubCategories = callSubSubCategories),
        setCallSubSubCategory: (state, callSubSubCategory) => (state.callSubSubCategory = callSubSubCategory),
        setSubCategory(state, subCategory) {
            state.subCategory = subCategory;
            state.subCategory.call_type = subCategory.call_type || null;
            state.subCategory.call_category = subCategory.call_category || null;
        },
        setAccessLists(state, accessLists) {
            state.accessLists = accessLists;
        },
    },
    plugins: [createPersistedState()],
});

export default store;
