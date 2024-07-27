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

                                    <div
                                        class="text-danger"
                                        v-show="errors.has('avatar')"
                                    >
                                        {{ errors.first("avatar") }}
                                    </div>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label class="control-label"
                                        >Select Group</label
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
                                        v-model="formData.employee_name"
                                        v-validate="'required'"
                                    />
                                    <span
                                        v-if="errors.employee_name"
                                        class="text-danger"
                                        >{{ errors.employee_name[0] }}</span
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
                                        v-model="formData.employee_id"
                                        v-validate="'required'"
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
                                        v-model="formData.employee_user_id"
                                        v-validate="'required'"
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
                                        v-model="formData.nid_card_no"
                                        v-validate="
                                            'required|numeric|min:10|max:15'
                                        "
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
                                        v-model="formData.birth_date"
                                        type="date"
                                        name="birth_date"
                                        placeholder="Select date and time"
                                        v-validate="'required'"
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
                                        v-model="formData.mobile_no"
                                        v-validate="
                                            'required|numeric|min:10|max:14'
                                        "
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
                                        v-model="formData.email"
                                        v-validate="'required|email'"
                                    />
                                    <div
                                        class="text-danger"
                                        v-show="errors.has('email')"
                                    >
                                        {{ errors.first("email") }}
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Password</label
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
                                        <div
                                            class="text-danger"
                                            v-show="errors.has('password')"
                                        >
                                            {{ errors.first("password") }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Confirm Password</label
                                    >
                                    <div class="password">
                                        <input
                                            autocomplete="off"
                                            name="confirmPassword"
                                            v-model="formData.confirmPassword"
                                            class="form-control"
                                            placeholder="confirm password"
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
                                    <div
                                        class="text-danger"
                                        v-show="errors.has('confirmPassword')"
                                    >
                                        {{ errors.first("confirmPassword") }}
                                    </div>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label class="control-label">Address</label>
                                    <textarea
                                        class="form-control"
                                        name="address"
                                        v-model="formData.address"
                                        v-validate="'required'"
                                    ></textarea>
                                    <div
                                        class="text-danger"
                                        v-show="errors.has('address')"
                                    >
                                        {{ errors.first("address") }}
                                    </div>
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
                                            name="gender"
                                            v-model="formData.gender"
                                            v-validate="'required'"
                                        /><span class="radio-mark"></span>Male
                                    </label>
                                    <label class="radio">
                                        <input
                                            type="radio"
                                            value="female"
                                            name="gender"
                                            v-model="formData.gender"
                                            v-validate="'required'"
                                        /><span class="radio-mark"></span>Female
                                    </label>
                                    <div
                                        class="text-danger"
                                        v-show="errors.has('gender')"
                                    >
                                        {{ errors.first("gender") }}
                                    </div>
                                </div>
                                <input
                                    type="hidden"
                                    value="inactive"
                                    v-model="formData.status"
                                />
                            </div>

                            <span
                                v-if="formErrors.message"
                                class="text-danger"
                                >{{ formErrors.message }}</span
                            >

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
            },
        };
    },
    created() {
        this.fetchGroups();
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
            _this.$validator.validateAll().then(async (result) => {
                if (result) {
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
                                title: response.data.message,
                                toaster: `b-toaster-top-right`,
                                variant: "success",
                            });
                            _this.$router.push({ name: "user-profile" });
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
