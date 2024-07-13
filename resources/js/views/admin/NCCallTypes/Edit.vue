<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                to="/admin/call-types"
            ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Edit Call Type</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt=""/>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <form @submit.prevent="updateCallType">
                            <div class="form-group">
                                <label class="control-label"
                                >Call Type Name</label
                                >
                                <input
                                    class="form-control"
                                    v-model="callType.call_type_name"
                                    type="text"
                                    required
                                />
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="control-label m-0 mr-3"
                                >Status:</label
                                >
                                <label class="radio mr-2"
                                ><input
                                    type="radio"
                                    value="active"
                                    v-model="callType.status"
                                    required
                                /><span class="radio-mark"></span>Active
                                </label>
                                <label class="radio">
                                    <input
                                        type="radio"
                                        value="inactive"
                                        v-model="callType.status"
                                        required
                                    /><span class="radio-mark"></span>Inactive
                                </label>
                            </div>
                            <button class="btn btn-site" type="submit">
                                Update
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
            callType: {
                call_type_name: '',
                status: 'active',
                created_by: '',
                updated_by: '',
                last_updated_by: '',
            },
            id: this.$route.params.id
        };
    },
    async created() {
        await this.fetchCallType();
    },
    methods: {
        async fetchCallType() {
            try {
                const response = await axios.get('/call-types/' + this.id);
                this.callType = response.data
                console.log('response', response.data)
            } catch (error) {
                console.error('Error fetching call type:', error);
            }
        },
        async updateCallType() {
            try {
                await axios.put(`/call-types/${this.id}`, this.callType);
                this.$router.push({name: "call-types-index"})
            } catch (error) {
                console.error('Error updating call type:', error);
            }
        }
    },
    mounted() {
        this.fetchCallType()
    }
};
</script>
