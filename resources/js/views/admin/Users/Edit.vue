<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                :to="{ name: 'user-index' }"
                ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Update User ({{ user.name }})</h1>
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
                                            v-validate="'image'"
                                            @change="handleFileUpload"
                                            accept="image/x-png,image/jpg,image/jpeg"
                                        />

                                        <img
                                            v-if="temp_avatar"
                                            v-bind:src="temp_avatar"
                                            alt="avatar"
                                        />
                                        <img
                                            v-else
                                            src="/images/user-avatar.png"
                                            alt="avatar"
                                        />

                                        <i class="icon-camera"></i>
                                    </label>

                                    <div
                                        class="text-danger"
                                        v-show="errors.has('avatar')"
                                    >
                                        {{ errors.first("avatar") }}
                                    </div>
                                </div>

                                <div class="col-md-12 form-group">
                                    <label class="control-label"
                                        >User Level<sup>*</sup></label
                                    >

                                    <el-select
                                        class="d-block w-100"
                                        v-model="user.level"
                                        name="level"
                                        v-validate="'required'"
                                        filterable
                                        placeholder="Select Level"
                                    >
                                        <el-option
                                            v-for="level in userLevels"
                                            :key="level.id"
                                            :label="level.label"
                                            :value="level.value"
                                        >
                                        </el-option>
                                    </el-select>
                                </div>

                                <div
                                    class="col-md-12 form-group"
                                    v-if="user.level !== 1"
                                >
                                    <label class="control-label"
                                        >Parent User</label
                                    >
                                    <el-select
                                        class="d-block w-100"
                                        v-model="user.parent_id"
                                        name="parent_id"
                                        placeholder="Select Parent User"
                                    >
                                        <el-option
                                            :key="0"
                                            :label="'No Parent'"
                                            :value="0"
                                        ></el-option>
                                        <el-option
                                            v-for="user in users"
                                            :key="user.id"
                                            :label="user.name"
                                            :value="user.id"
                                        >
                                        </el-option>
                                    </el-select>
                                    <small
                                        class="text-danger"
                                        v-show="errors.has('parent_id')"
                                    >
                                        {{ errors.first("parent_id") }}
                                    </small>
                                </div>

                                <div
                                    class="col-md-12 form-group"
                                    v-if="user.level !== 1"
                                >
                                    <label class="control-label"
                                        >Select Group</label
                                    >
                                    <el-select
                                        class="d-block w-100"
                                        v-model="user.group_id"
                                        name="group"
                                        v-validate="'required'"
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
                                    <div
                                        class="text-danger"
                                        v-show="errors.has('group')"
                                    >
                                        {{ errors.first("group") }}
                                    </div>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Employee Name</label
                                    >
                                    <input
                                        class="form-control"
                                        type="text"
                                        name="employee_name"
                                        v-model="
                                            user.user_details.employee_name
                                        "
                                        v-validate="'required'"
                                        disabled
                                    />
                                    <span
                                        v-if="errors.name"
                                        class="text-danger"
                                        >{{ errors.name[0] }}</span
                                    >

                                    <div
                                        class="text-danger"
                                        v-show="errors.has('employee_name')"
                                    >
                                        {{ errors.first("employee_name") }}
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Employee ID</label
                                    >
                                    <input
                                        class="form-control"
                                        type="text"
                                        name="employee_id"
                                        v-model="user.user_details.employee_id"
                                        v-validate="'required'"
                                        disabled
                                    />
                                    <div
                                        class="text-danger"
                                        v-show="errors.has('employee_id')"
                                    >
                                        {{ errors.first("employee_id") }}
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label">User ID</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        name="employee_user_id"
                                        v-model="
                                            user.user_details.employee_user_id
                                        "
                                        v-validate="'required'"
                                        disabled
                                    />
                                    <div
                                        class="text-danger"
                                        v-show="errors.has('employee_user_id')"
                                    >
                                        {{ errors.first("employee_user_id") }}
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >NID Card No
                                    </label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        name="nid_card_no"
                                        v-model="user.user_details.nid_card_no"
                                        v-validate="
                                            'required|numeric|min:10|max:15'
                                        "
                                        disabled
                                    />
                                    <div
                                        class="text-danger"
                                        v-show="errors.has('nid_card_no')"
                                    >
                                        {{ errors.first("nid_card_no") }}
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Date of Birth
                                    </label>
                                    <el-date-picker
                                        class="d-block w-100"
                                        v-model="user.user_details.birth_date"
                                        type="date"
                                        name="birth_date"
                                        placeholder="Select date and time"
                                        v-validate="'required'"
                                        disabled
                                    >
                                    </el-date-picker>
                                    <div
                                        class="text-danger"
                                        v-show="errors.has('birth_date')"
                                    >
                                        {{ errors.first("birth_date") }}
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Phone Number</label
                                    >
                                    <input
                                        class="form-control"
                                        type="text"
                                        name="mobile_no"
                                        v-model="user.mobile_no"
                                        v-validate="
                                            'required|numeric|min:10|max:14'
                                        "
                                        disabled
                                    />
                                    <div
                                        class="text-danger"
                                        v-show="errors.has('mobile_no')"
                                    >
                                        {{ errors.first("mobile_no") }}
                                    </div>
                                </div>

                                <div class="col-md-12 form-group">
                                    <label class="control-label"
                                        >Email Address</label
                                    >
                                    <input
                                        class="form-control"
                                        type="text"
                                        name="email"
                                        v-model="user.email"
                                        v-validate="'required|email'"
                                        disabled
                                    />
                                    <div
                                        class="text-danger"
                                        v-show="errors.has('email')"
                                    >
                                        {{ errors.first("email") }}
                                    </div>
                                </div>

                                <div class="col-md-12 form-group">
                                    <label class="control-label">Address</label>
                                    <textarea
                                        class="form-control"
                                        name="address"
                                        v-model="user.user_details.address"
                                        v-validate="'required'"
                                        disabled
                                    ></textarea>
                                    <div
                                        class="text-danger"
                                        v-show="errors.has('address')"
                                    >
                                        {{ errors.first("address") }}
                                    </div>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >User Type<sup>*</sup></label
                                    >

                                    <el-select
                                        v-model="user.user_type"
                                        placeholder="Select Type"
                                        name="userType"
                                        filterable
                                        v-validate="'required'"
                                    >
                                        <el-option
                                            v-for="option in typeOptions"
                                            :key="option.value"
                                            :label="option.label"
                                            :value="option.value"
                                        >
                                        </el-option>
                                    </el-select>

                                    <div
                                        class="text-danger"
                                        v-show="errors.has('userType')"
                                    >
                                        {{ errors.first("userType") }}
                                    </div>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Gender<sup>*</sup></label
                                    >
                                    <div class="d-flex">
                                        <label class="radio mr-2"
                                            ><input
                                                type="radio"
                                                value="Male"
                                                name="gender"
                                                v-model="
                                                    user.user_details.gender
                                                "
                                                v-validate="'required'"
                                            /><span class="radio-mark"></span
                                            >Male
                                        </label>
                                        <label class="radio">
                                            <input
                                                type="radio"
                                                value="Female"
                                                name="gender"
                                                v-model="
                                                    user.user_details.gender
                                                "
                                                v-validate="'required'"
                                            /><span class="radio-mark"></span
                                            >Female
                                        </label>
                                    </div>
                                    <div
                                        class="text-danger"
                                        v-show="errors.has('gender')"
                                    >
                                        {{ errors.first("gender") }}
                                    </div>
                                </div>
                                <div></div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Status<sup>*</sup></label
                                    >
                                    <div class="d-flex">
                                        <label class="radio mr-2"
                                            ><input
                                                type="radio"
                                                value="Active"
                                                v-model="user.status"
                                                required
                                            /><span class="radio-mark"></span
                                            >Active
                                        </label>
                                        <label class="radio mr-2">
                                            <input
                                                type="radio"
                                                value="Inactive"
                                                v-model="user.status"
                                                required
                                            /><span class="radio-mark"></span
                                            >Inactive
                                        </label>
                                        <label class="radio">
                                            <input
                                                type="radio"
                                                value="Pending"
                                                v-model="user.status"
                                                required
                                            /><span class="radio-mark"></span
                                            >Pending
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <small
                                v-if="formErrors.message"
                                class="text-danger"
                                >{{ formErrors.message }}</small
                            >

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
            showPassword: false,
            confirmPassword: false,
            groups: [],
            formErrors: [],
            temp_avatar: null,
            userLevels: [],
            selectedType: "",
            typeOptions: [
                { value: "internal", label: "Internal" },
                { value: "external", label: "External" },
            ],
            user: {
                group_id: null,
                status: "Pending",
                user_details: {
                    employee_name: "",
                    employee_id: "",
                    employee_user_id: "",
                    nid_card_no: "",
                    birth_date: "",
                    address: "",
                    gender: "",
                },
                mobile_no: "",
                email: "",
            },
            users: [],
            id: null,
        };
    },
    created() {
        this.id = this.$route.params.id;
        this.fetchGroups();
        this.fetchUser();
        this.fetchAllUsers();
    },
    methods: {
        togglePassword() {
            this.showPassword = !this.showPassword;
        },
        toggleConfirmPassword() {
            this.confirmPassword = !this.confirmPassword;
        },
        handleFileUpload(event) {
            // `files` is always an array because the file input may be in multiple mode
            let reader = new FileReader();
            reader.readAsDataURL(event.target.files[0]);
            reader.onload = () => {
                this.user.avatar = reader.result;
                this.temp_avatar = reader.result;
            };
        },
        async fetchGroups() {
            try {
                const response = await axios.get("/groups");
                this.groups = response.data;
            } catch (error) {
                console.error("Error fetching groups:", error);
                this.groups = [];
            }
        },
        setUserAvatar(user) {
            this.temp_avatar = user.avatar_url;
        },
        async fetchAllUsers() {
            try {
                const response = await axios.get("/users");
                this.users = response.data.data;
            } catch (error) {
                console.error("Error fetching users:", error);
                this.users = [];
            }
        },
        async fetchUser() {
            try {
                const response = await axios.get(`/user/${this.id}`);
                const data = response.data.data;
                this.user = data;
                this.userLevels = data.levels;
                this.setUserAvatar(data);
            } catch (error) {
                console.error("Error fetching user:", error);
                this.user = {};
            }
        },
        async handleSubmit() {
            const _this = this;
            // console.log("_this", _this.user);
            // return false;
            _this.$validator.validateAll().then(async (result) => {
                if (result) {
                    axios({
                        method: "PUT",
                        url: `/user/${_this.id}`,
                        data: _this.user,
                        headers: { "Content-Type": "application/json" },
                    })
                        .then(function (response) {
                            _this.isLoading = false;
                            _this.user = {};
                            Vue.prototype.$showToast(response.data.message, {
                                type: response.data.type,
                            });
                            _this.$router.push({ name: "user-index" });
                        })
                        .catch((errors) => {
                            if (
                                errors.response &&
                                errors.response.data.errors
                            ) {
                                _this.formErrors = errors.response.data.errors;
                            } else {
                                _this.formErrors.push(
                                    "Failed to create user. Please try again later."
                                );
                            }
                        })
                        .finally(() => {
                            _this.isLoading = false;
                        });
                }
            });
        },
    },
};
</script>
