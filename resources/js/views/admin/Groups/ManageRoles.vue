<template>
    <div>
        <div class="common-heading mb-3">
            <h1 class="title m-0">Manage Roles for {{ group.name }}</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Roles</label>
                            <select
                                id="roles"
                                v-model="selectedRoles"
                                multiple
                                class="form-control"
                            >
                                <option
                                    class="px-2 py-2 rounded"
                                    v-for="role in roles"
                                    :key="role.id"
                                    :value="role.name"
                                >
                                    {{ role.name }}
                                </option>
                            </select>
                        </div>
                        <button class="btn btn-site mb-3" @click="assignRoles">
                            Assign Roles
                        </button>

                        <div class="pop-msg">
                            <div
                                v-for="role in group.roles"
                                :key="role.id"
                                class="d-flex justify-content-between align-items-center rounded p-2 border mb-3"
                            >
                                <span>{{ role.name }}</span>
                                <button
                                    type="button"
                                    class="btn btn-danger btn-sm"
                                    @click="removeRole(role.id)"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
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
            id: this.$route.params.id,
            group: {
                roles: [],
            },
            roles: [],
            selectedRoles: [],
        };
    },
    methods: {
        fetchGroup() {
            axios.get(`/groups/${this.id}`).then((response) => {
                this.group = response.data;
                this.selectedRoles = this.group.roles.map((role) => role.name);
            });
        },
        fetchRoles() {
            axios.get("/roles").then((response) => {
                this.roles = response.data;
            });
        },
        assignRoles() {
            axios
                .post(`/groups/${this.id}/roles`, { roles: this.selectedRoles })
                .then(() => {
                    this.fetchGroup();
                    this.selectedRoles = [];
                    this.fetchRoles();
                })
                .catch((error) => {
                    console.error("Error assigning roles:", error);
                });
        },
        removeRole(roleId) {
            axios
                .delete(`/groups/${this.id}/roles/${roleId}`)
                .then(() => {
                    this.fetchGroup();
                })
                .catch((error) => {
                    console.error("Error removing role:", error);
                });
        },
    },
    created() {
        this.fetchGroup();
        this.fetchRoles();
    },
};
</script>
<style>
option {
    font-size: 14px;
    background-color: #fff;
    margin: 5px 0;
    width: 95%;
}

option:before {
    content: "\e906";
    font-family: "nagad" !important;
    font-size: 16px;
    display: inline-block;
    padding-right: 10px;
    color: #333;
}

option:checked:before {
    color: #fff;
    content: "\e92f";
}
select[multiple]:focus option:checked {
    background: #ff6060 linear-gradient(0deg, #ff6060 0%, #ff6060 100%);
}
option:checked {
    background: #ff6060 linear-gradient(0deg, #ff6060 0%, #ff6060 100%);
    color: #fff;
}
</style>
