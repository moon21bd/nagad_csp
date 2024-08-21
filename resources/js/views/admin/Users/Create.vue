<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                :to="{ name: 'user-index' }"
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
                                            v-validate="'required|image'"
                                            id="avatar"
                                            @change="handleFileUpload"
                                            accept="image/x-png,image/jpg,image/jpeg"
                                        />

                                        <img
                                            v-if="temp_avatar"
                                            v-bind:src="temp_avatar"
                                            alt="avatar"
                                        />
                                        <i class="icon-camera"></i>
                                    </label>

                                    <small
                                        class="text-danger"
                                        v-show="errors.has('avatar')"
                                    >
                                        {{ errors.first("avatar") }}
                                    </small>
                                </div>

                                <div class="col-md-12 form-group">
                                    <label class="control-label"
                                        >User Level<sup>*</sup></label
                                    >
                                    <el-select
                                        class="d-block w-100"
                                        v-model="formData.level"
                                        name="level"
                                        v-validate="'required'"
                                        placeholder="Select User Level"
                                    >
                                        <el-option
                                            label="Super Admin"
                                            :value="1"
                                        ></el-option>
                                        <el-option
                                            label="Admin"
                                            :value="2"
                                        ></el-option>
                                        <el-option
                                            label="Group Owner"
                                            :value="3"
                                        ></el-option>
                                        <el-option
                                            label="User"
                                            :value="4"
                                        ></el-option>
                                    </el-select>
                                    <small
                                        class="text-danger"
                                        v-show="errors.has('level')"
                                    >
                                        {{ errors.first("level") }}
                                    </small>
                                </div>

                                <div
                                    class="col-md-12 form-group"
                                    v-if="formData.level !== 1"
                                >
                                    <label class="control-label"
                                        >Parent User</label
                                    >
                                    <el-select
                                        class="d-block w-100"
                                        v-model="formData.parent_id"
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
                                    v-if="formData.level !== 1"
                                >
                                    <label class="control-label"
                                        >Select Group<sup>*</sup></label
                                    >
                                    <el-select
                                        class="d-block w-100"
                                        v-model="formData.group_id"
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
                                    <small
                                        class="text-danger"
                                        v-show="errors.has('group')"
                                    >
                                        {{ errors.first("group") }}
                                    </small>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Employee Name<sup>*</sup></label
                                    >
                                    <input
                                        class="form-control"
                                        type="text"
                                        name="employee_name"
                                        v-model="formData.employee_name"
                                        v-validate="'required'"
                                    />
                                    <span
                                        v-if="errors.employee_name"
                                        class="text-danger"
                                        >{{ errors.employee_name[0] }}</span
                                    >

                                    <small
                                        class="text-danger"
                                        v-show="errors.has('employee_name')"
                                    >
                                        {{ errors.first("employee_name") }}
                                    </small>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Employee ID<sup>*</sup></label
                                    >
                                    <input
                                        class="form-control"
                                        type="text"
                                        name="employee_id"
                                        v-model="formData.employee_id"
                                        v-validate="'required'"
                                    />
                                    <small
                                        class="text-danger"
                                        v-show="errors.has('employee_id')"
                                    >
                                        {{ errors.first("employee_id") }}
                                    </small>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >User ID<sup>*</sup></label
                                    >
                                    <input
                                        class="form-control"
                                        type="text"
                                        name="employee_user_id"
                                        v-model="formData.employee_user_id"
                                        v-validate="'required'"
                                    />
                                    <small
                                        class="text-danger"
                                        v-show="errors.has('employee_user_id')"
                                    >
                                        {{ errors.first("employee_user_id") }}
                                    </small>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >NID Card No<sup>*</sup>
                                    </label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        name="nid_card_no"
                                        v-model="formData.nid_card_no"
                                        v-validate="
                                            'required|numeric|min:10|max:15'
                                        "
                                    />
                                    <small
                                        class="text-danger"
                                        v-show="errors.has('nid_card_no')"
                                    >
                                        {{ errors.first("nid_card_no") }}
                                    </small>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Date of Birth<sup>*</sup>
                                    </label>
                                    <el-date-picker
                                        class="d-block w-100"
                                        v-model="formData.birth_date"
                                        type="date"
                                        name="birth_date"
                                        placeholder="Select date and time"
                                        v-validate="'required'"
                                    >
                                    </el-date-picker>
                                    <small
                                        class="text-danger"
                                        v-show="errors.has('birth_date')"
                                    >
                                        {{ errors.first("birth_date") }}
                                    </small>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Phone Number<sup>*</sup></label
                                    >
                                    <input
                                        class="form-control"
                                        type="text"
                                        name="mobile_no"
                                        v-model="formData.mobile_no"
                                        v-validate="
                                            'required|numeric|min:10|max:14'
                                        "
                                    />
                                    <small
                                        class="text-danger"
                                        v-show="errors.has('mobile_no')"
                                    >
                                        {{ errors.first("mobile_no") }}
                                    </small>
                                </div>

                                <div class="col-md-12 form-group">
                                    <label class="control-label"
                                        >Email Address<sup>*</sup></label
                                    >
                                    <input
                                        class="form-control"
                                        type="text"
                                        name="email"
                                        v-model="formData.email"
                                        v-validate="'required|email'"
                                    />
                                    <small
                                        class="text-danger"
                                        v-show="errors.has('email')"
                                    >
                                        {{ errors.first("email") }}
                                    </small>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Password<sup>*</sup></label
                                    >

                                    <div class="password">
                                        <input
                                            id="password"
                                            name="password"
                                            v-model="formData.password"
                                            class="form-control"
                                            placeholder="Password"
                                            ref="password"
                                            :type="
                                                showPassword
                                                    ? 'text'
                                                    : 'password'
                                            "
                                            v-validate="
                                                'required|min:8|max:25|alpha_num'
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
                                        <small
                                            class="text-danger"
                                            v-show="errors.has('password')"
                                        >
                                            {{ errors.first("password") }}
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Confirm Password<sup>*</sup></label
                                    >
                                    <div class="password">
                                        <input
                                            autocomplete="off"
                                            name="confirmPassword"
                                            v-model="formData.confirmPassword"
                                            class="form-control"
                                            placeholder="Confirm password"
                                            :type="
                                                confirmPassword
                                                    ? 'text'
                                                    : 'password'
                                            "
                                            v-validate="
                                                'required|confirmed:password|min:8|max:25'
                                            "
                                            data-vv-as="password"
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
                                    <small
                                        class="text-danger"
                                        v-show="errors.has('confirmPassword')"
                                    >
                                        {{ errors.first("confirmPassword") }}
                                    </small>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label class="control-label"
                                        >Address<sup>*</sup></label
                                    >
                                    <textarea
                                        class="form-control"
                                        name="address"
                                        v-model="formData.address"
                                        v-validate="'required'"
                                    ></textarea>
                                    <small
                                        class="text-danger"
                                        v-show="errors.has('address')"
                                    >
                                        {{ errors.first("address") }}
                                    </small>
                                </div>

                                <div class="col-md-12 form-group">
                                    <div class="d-flex align-items-center">
                                        <label class="control-label m-0 mr-3"
                                            >Gender<sup>*</sup></label
                                        >
                                        <label class="radio mr-2"
                                            ><input
                                                type="radio"
                                                value="male"
                                                name="gender"
                                                v-model="formData.gender"
                                                v-validate="'required'"
                                            /><span class="radio-mark"></span
                                            >Male
                                        </label>
                                        <label class="radio">
                                            <input
                                                type="radio"
                                                value="female"
                                                name="gender"
                                                v-model="formData.gender"
                                                v-validate="'required'"
                                            /><span class="radio-mark"></span
                                            >Female
                                        </label>
                                    </div>
                                    <small
                                        class="text-danger"
                                        v-show="errors.has('gender')"
                                    >
                                        {{ errors.first("gender") }}
                                    </small>
                                </div>

                                <input
                                    type="hidden"
                                    value="inactive"
                                    v-model="formData.status"
                                />
                            </div>

                            <div v-if="formErrors.message" class="mb-3">
                                <small class="text-danger">{{
                                    formErrors.message
                                }}</small>
                            </div>

                            <div>
                                <button class="btn btn-site" type="submit">
                                    Create
                                </button>
                            </div>
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
            temp_avatar: "/images/user-avatar.png",
            groups: [],
            formErrors: [],
            formData: {
                group_id: null,
                status: "Pending",
                employee_name: "",
                employee_id: "",
                employee_user_id: "",
                nid_card_no: "",
                birth_date: "",
                mobile_no: "",
                address: "",
                email: "",
                password: "",
                confirmPassword: "",
                gender: "",
                level: 4, // Default to 'User'
                parent_id: null,
            },
            users: [], // Store parent users
        };
    },
    created() {
        this.fetchGroups();
        this.fetchUsers();
    },
    computed: {
        groupValidationRule() {
            return this.formData.level !== 1 ? "required" : "";
        },
    },
    methods: {
        async fetchUsers() {
            try {
                const response = await axios.get("/users");
                this.users = response.data.data;
            } catch (error) {
                console.error("Error fetching users:", error);
                this.users = [];
            }
        },
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
                this.formData.avatar = reader.result;
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
        async handleSubmit() {
            const _this = this;
            _this.$validator.validateAll().then(async (validated) => {
                if (validated) {
                    // console.log("formData", _this.formData);
                    // return;
                    axios({
                        method: "POST",
                        url: "/user/register",
                        data: _this.formData,
                        headers: { "Content-Type": "application/json" },
                    })
                        .then(function (response) {
                            _this.isLoading = false;
                            _this.formData = {};
                            Vue.prototype.$showToast(response.data.message, {
                                type: "success",
                            });
                            _this.$router.push({ name: "user-index" });
                        })
                        .catch((errors) => {
                            console.log(
                                "err.response",
                                errors.response.data.errors
                            );
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
