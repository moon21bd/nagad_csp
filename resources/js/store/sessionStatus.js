import axios from "../axios";

const state = {
    currentStatus: null,
    statusHistory: [],
};

const getters = {
    currentStatus: (state) => state.currentStatus,
    statusHistory: (state) => state.statusHistory,
};

const mutations = {
    SET_STATUS(state, status) {
        state.currentStatus = status;

        const timestamp = new Date().toISOString();
        state.statusHistory.push({
            status: status,
            updatedAt: timestamp,
        });
    },
    SET_CURRENT_STATUS(state, status) {
        state.currentStatus = status;
    },
    SET_STATUS_HISTORY(state, history) {
        state.statusHistory = history;
    },
};

const actions = {
    async updateStatus({ commit }, status) {
        try {
            await axios.post("/session-status", { status });
            commit("SET_STATUS", status);
        } catch (error) {
            console.error("Error updating session status:", error);
        }
    },
    async fetchCurrentStatus({ commit }) {
        try {
            const response = await axios.get("/session-status/current");
            commit("SET_CURRENT_STATUS", response.data.status);
        } catch (error) {
            console.error("Error fetching current session status:", error);
        }
    },
    loadStatusHistory({ commit }) {
        const sampleHistory = [
            { status: "Active", updatedAt: "2024-10-01T10:00:00Z" },
            { status: "Break", updatedAt: "2024-10-02T12:00:00Z" },
            { status: "Active", updatedAt: "2024-10-03T14:00:00Z" },
        ];
        commit("SET_STATUS_HISTORY", sampleHistory);
    },
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
};
