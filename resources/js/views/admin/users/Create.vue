<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                to="/admin/group-configs"
                ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Create User</h1>
        </div>
        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <form @submit.prevent="handleSubmit">
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label class="avatar">
                                        <input
                                            type="file"
                                            name="avatar"
                                            id="avatar"
                                            @change="handleFileUpload"
                                            accept="image/x-png,image/jpg,image/jpeg"
                                        />

                                        <img
                                            src="/images/user-avatar.png"
                                            alt=""
                                        />
                                        <i class="icon-camera"></i>
                                    </label>
                                </div>
                                <div class="col-md-12 form-group">
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

                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Employee Name</label
                                    >
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="formData.employeeName"
                                        required
                                    />
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Employee ID</label
                                    >
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="formData.employeeID"
                                        required
                                    />
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label">User ID</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="formData.userID"
                                    />
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >NID Card No
                                    </label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="formData.nidCardNo"
                                        required
                                    />
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Date of Birth
                                    </label>
                                    <el-date-picker
                                        class="d-block w-100"
                                        v-model="formData.birthDate"
                                        type="date"
                                        placeholder="Select date and time"
                                    >
                                    </el-date-picker>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Phone Number</label
                                    >
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="formData.phoneNumber"
                                        required
                                    />
                                </div>

                                <div class="col-md-12 form-group">
                                    <label class="control-label"
                                        >Email Address</label
                                    >
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="formData.emailAddress"
                                        required
                                    />
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Password</label
                                    >
                                    <div class="password">
                                        <input
                                            autocomplete="off"
                                            id="password"
                                            name="password"
                                            v-model="formData.password"
                                            class="form-control"
                                            placeholder="Password"
                                            :type="
                                                showPassword
                                                    ? 'text'
                                                    : 'password'
                                            "
                                        />
                                        <span
                                            class="password-toggle"
                                            @click="togglePassword"
                                        >
                                            <i
                                                :class="{
                                                    'icon-eye-off':
                                                        showPassword,
                                                    'icon-eye': !showPassword,
                                                }"
                                            ></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Confirm Password</label
                                    >
                                    <div class="password">
                                        <input
                                            autocomplete="off"
                                            name="password_confirmation"
                                            v-model="formData.confirmPassword"
                                            class="form-control"
                                            placeholder="confirm password"
                                            :type="
                                                confirmPassword
                                                    ? 'text'
                                                    : 'password'
                                            "
                                        />
                                        <span
                                            class="password-toggle"
                                            @click="toggleConfirmPassword"
                                        >
                                            <i
                                                :class="{
                                                    'icon-eye-off':
                                                        confirmPassword,
                                                    'icon-eye':
                                                        !confirmPassword,
                                                }"
                                            ></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label class="control-label">Address</label>
                                    <textarea
                                        class="form-control"
                                        v-model="formData.address"
                                    ></textarea>
                                </div>

                                <div
                                    class="col-md-6 form-group d-flex align-items-center"
                                >
                                    <label class="control-label m-0 mr-3"
                                        >Gender</label
                                    >
                                    <label class="radio mr-2"
                                        ><input
                                            type="radio"
                                            value="male"
                                            v-model="formData.gender"
                                            required
                                        /><span class="radio-mark"></span>Male
                                    </label>
                                    <label class="radio">
                                        <input
                                            type="radio"
                                            value="female"
                                            v-model="formData.gender"
                                            required
                                        /><span class="radio-mark"></span>Female
                                    </label>
                                </div>
                                <div
                                    class="col-md-6 form-group d-flex align-items-center"
                                >
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
                                        /><span class="radio-mark"></span
                                        >Inactive
                                    </label>
                                </div>
                            </div>
                            <div>
                                <h4>languages</h4>
                                <input
                                    type="checkbox"
                                    @click="checkAll()"
                                    v-model="isCheckAll"
                                />
                                Check All
                                <ul>
                                    <li
                                        v-for="(lang, index) in langsdata"
                                        :key="index"
                                    >
                                        <input
                                            type="checkbox"
                                            :value="lang"
                                            v-model="checkedItems"
                                        />{{ lang }}
                                    </li>
                                </ul>
                            </div>

                            <div>
                                <h4>permission</h4>
                                <input
                                    type="checkbox"
                                    @click="checkAll()"
                                    v-model="isCheckAll"
                                />
                                Check All
                                <ul>
                                    <li
                                        v-for="(
                                            permission, index
                                        ) in permissions"
                                        :key="index"
                                    >
                                        <input
                                            type="checkbox"
                                            :value="permission"
                                            v-model="checkedItems"
                                        />{{ permission }}
                                    </li>
                                </ul>
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
import axios from "axios";

export default {
    data() {
        return {
            isCheckAll: false,
            langsdata: ["JavaScript", "Python", "HTML", "CSS"],
            permissions: ["Read", "Write", "Delete"],
            checkedItems: [],
            isLoading: false,
            showPassword: false,
            confirmPassword: false,
            formData: {
                group_id: null,
                status: "active",
                userImage: "",
                employeeName: "",
                employeeID: "",
                userID: "",
                nidCardNo: "",
                birthDate: "",
                phoneNumber: "",
                address: "",
                emailAddress: "",
                password: "",
                confirmPassword: "",
                gender: "",
            },
            groups: [],
            accessLists: [],
            selectedAccessLists: [], // Track selected access lists
        };
    },
    created() {
        this.fetchGroups();
        this.fetchAccessLists();
    },
    methods: {
        checkAll() {
            this.isCheckAll = !this.isCheckAll;
            if (this.isCheckAll) {
                this.checkedItems = [...this.langsdata, ...this.permissions];
            } else {
                this.checkedItems = [];
            }
        },
        togglePassword() {
            this.showPassword = !this.showPassword;
        },

        toggleConfirmPassword() {
            this.confirmPassword = !this.confirmPassword;
        },
        handleFileUpload(event) {
            this.userImage.profileImage = event.target.files[0];
        },
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
                this.$router.push({ name: "group-configs" });
                // Optionally, navigate to the list view or reset the form
            } catch (error) {
                console.error("Error saving group configuration:", error);
            }
        },
    },
};
</script>
