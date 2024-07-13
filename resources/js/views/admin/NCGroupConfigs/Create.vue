<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                to="/admin/group-configs"
            ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Create Group Configuration</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt=""/>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <form @submit.prevent="handleSubmit">
                            <div class="form-group">
                                <label class="control-label"
                                >Select Group</label
                                >
                                <el-select
                                    class="d-block w-100"
                                    v-model="formData.group_id"
                                    required
                                    filterable
                                    placeholder="Select Group"
                                >
                                    <el-option
                                        v-for="group in groups"
                                        :key="group.id"
                                        :label="group.name"
                                        :value="group.id"
                                    >
                                    </el-option>
                                </el-select>
                            </div>
                            <div class="form-group mb-0">
                                <label class="control-label d-block"
                                >Access Lists</label
                                >
                                <div
                                    v-for="accessList in accessLists"
                                    :key="accessList.id"
                                    class="form-check form-check-inline"
                                >
                                    <label class="checkbox mr-2 mb-3"
                                    ><input
                                        type="checkbox"
                                        v-model="selectedAccessLists"
                                        :value="accessList.id"
                                    /><span class="checkmark"></span
                                    >{{ accessList.access_name }}
                                    </label>
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="control-label m-0 mr-3"
                                >Status:</label
                                >
                                <label class="radio mr-2"
                                ><input
                                    type="radio"
                                    value="active"
                                    v-model="formData.status"
                                    required
                                /><span class="radio-mark"></span>Active
                                </label>
                                <label class="radio">
                                    <input
                                        type="radio"
                                        value="inactive"
                                        v-model="formData.status"
                                        required
                                    /><span class="radio-mark"></span>Inactive
                                </label>
                            </div>
                            <button class="btn btn-site" type="submit">
                                Create
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
            formData: {
                group_id: null,
                status: "active",
            },
            groups: [],
            accessLists: [],
            selectedAccessLists: [], // Track selected access lists
        };
    },
    mounted() {
        this.fetchGroups();
        this.fetchAccessLists();
    },
    methods: {
        async fetchGroups() {
            try {
                const response = await axios.get("/groups");
                this.groups = response.data;
            } catch (error) {
                console.error("Error fetching groups:", error);
            }
        },
        async fetchAccessLists() {
            this.isLoading = true;
            try {
                const response = await axios.get("/access-lists");
                this.accessLists = response.data;
            } catch (error) {
                console.error("Error fetching access lists:", error);
            } finally {
                this.isLoading = false;
            }
        },
        async handleSubmit() {
            try {
                const payload = {
                    group_id: this.formData.group_id,
                    status: this.formData.status,
                    access_list_ids: this.selectedAccessLists, // Use selected access lists
                };
                const response = await axios.post("/group-configs", payload);
                console.log(
                    "Group configuration saved successfully:",
                    response.data
                );
                // Navigate to the groups list route
                this.$router.push({name: "group-configs"});
                // Optionally, navigate to the list view or reset the form
            } catch (error) {
                console.error("Error saving group configuration:", error);
            }
        },
    },
};
</script>
