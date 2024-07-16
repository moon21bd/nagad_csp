<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <h1 class="title m-0">Permissions Assign</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div class="permissions-assign">
                    <div class="permissions-assign-box">
                        <h4>
                            <label class="checkbox"
                                ><input
                                    type="checkbox"
                                    name="permissions"
                                /><span class="checkmark"></span>User</label
                            >
                        </h4>
                        <ul>
                            <li>
                                <label class="checkbox">
                                    <input type="checkbox" name="permissions" />
                                    <span class="checkmark"></span>User
                                    Create</label
                                >
                            </li>
                            <li>
                                <label class="checkbox">
                                    <input type="checkbox" name="permissions" />
                                    <span class="checkmark"></span>User
                                    Update</label
                                >
                            </li>
                            <li>
                                <label class="checkbox">
                                    <input type="checkbox" name="permissions" />
                                    <span class="checkmark"></span>User
                                    Delete</label
                                >
                            </li>
                            <li>
                                <label class="checkbox">
                                    <input type="checkbox" name="permissions" />
                                    <span class="checkmark"></span>User Status
                                    Change</label
                                >
                            </li>
                        </ul>
                    </div>
                    <div class="permissions-assign-box">
                        <h4>
                            <label class="checkbox"
                                ><input
                                    type="checkbox"
                                    name="permissions"
                                /><span class="checkmark"></span>User</label
                            >
                        </h4>
                        <ul>
                            <li>
                                <label class="checkbox">
                                    <input type="checkbox" name="permissions" />
                                    <span class="checkmark"></span>User
                                    Create</label
                                >
                            </li>
                            <li>
                                <label class="checkbox">
                                    <input type="checkbox" name="permissions" />
                                    <span class="checkmark"></span>User
                                    Update</label
                                >
                            </li>
                            <li>
                                <label class="checkbox">
                                    <input type="checkbox" name="permissions" />
                                    <span class="checkmark"></span>User
                                    Delete</label
                                >
                            </li>
                            <li>
                                <label class="checkbox">
                                    <input type="checkbox" name="permissions" />
                                    <span class="checkmark"></span>User Status
                                    Change</label
                                >
                            </li>
                        </ul>
                    </div>
                    <div class="permissions-assign-box">
                        <h4>
                            <label class="checkbox"
                                ><input
                                    type="checkbox"
                                    name="permissions"
                                /><span class="checkmark"></span>User</label
                            >
                        </h4>
                        <ul>
                            <li>
                                <label class="checkbox">
                                    <input type="checkbox" name="permissions" />
                                    <span class="checkmark"></span>User
                                    Create</label
                                >
                            </li>
                            <li>
                                <label class="checkbox">
                                    <input type="checkbox" name="permissions" />
                                    <span class="checkmark"></span>User
                                    Update</label
                                >
                            </li>
                            <li>
                                <label class="checkbox">
                                    <input type="checkbox" name="permissions" />
                                    <span class="checkmark"></span>User
                                    Delete</label
                                >
                            </li>
                            <li>
                                <label class="checkbox">
                                    <input type="checkbox" name="permissions" />
                                    <span class="checkmark"></span>User Status
                                    Change</label
                                >
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "PermissionsAssign",
    data() {
        return {
            isLoading: false,
        };
    },
    methods: {},
};
</script>
