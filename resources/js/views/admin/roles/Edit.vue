<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                to="/admin/roles"
            ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Edit Role</h1>
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
                                <label class="control-label">Role Name</label>
                                <input
                                    class="form-control"
                                    v-model="formData.name"
                                    type="text"
                                    required
                                />
                            </div>

                            <button class="btn btn-site" type="submit">
                                Update
                            </button>
                        </form>
                        <ul class="list-group" v-if="formErrors.length">
                            <li
                                class="list-group-item list-group-item-danger"
                                v-for="error in formErrors"
                                :key="error"
                            >
                                {{ error }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from 'axios';

export default {
    data() {
        return {
            isLoading: false,
            formData: {
                name: '',
            },
            id: this.$route.params.id,
            formErrors: []
        };
    },
    methods: {
        async handleSubmit() {
            try {
                this.formErrors = [];
                if (!this.formData.name) this.formErrors.push('Role Name is required.');
                if (this.formErrors.length > 0) return;

                const response = await axios.put(`/role/update/${this.id}`, this.formData);

                this.formData = {
                    name: '',
                };

                this.$router.push({name: 'roles-index'});
            } catch (error) {
                console.error('Error updating group:', error);
                if (error.response && error.response.data.errors) {
                    this.formErrors = Object.values(error.response.data.errors).flat();
                } else {
                    this.formErrors.push('Failed to update role. Please try again later.');
                }
            }
        },
        async fetchRoleData() {
            try {
                const response = await axios.get(`/role/${this.id}`);
                this.formData = {
                    name: response.data.role.name,
                };
            } catch (error) {
                console.error('Error fetching role data:', error);
            }
        }
    },
    mounted() {
        this.fetchRoleData()
    }
};
</script>
