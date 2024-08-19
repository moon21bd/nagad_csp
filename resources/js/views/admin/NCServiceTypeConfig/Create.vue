<template>
    <div>
        <div class="common-heading d-flex align-items-center mb-3">
            <router-link
                class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                :to="{ name: 'service-type-config-index' }"
                ><i class="icon-left"></i>
            </router-link>
            <h1 class="title m-0">Service Type Configurations</h1>
        </div>

        <div class="card mb-4">
            <div class="overlay" v-if="isLoading">
                <img src="/images/loader.gif" alt="" />
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-10">
                        <form
                            ref="serviceTypeConfigForm"
                            @submit.prevent="handleSubmit"
                            novalidate
                        >
                            <!-- Service Types (Wrap up) -->
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="control-label"
                                        >Service Type<sup>*</sup></label
                                    >
                                    <div class="custom-style">
                                        <el-select
                                            class="d-block w-100"
                                            v-model="
                                                configurationInfos.callTypeId
                                            "
                                            @change="fetchCategories"
                                            required
                                            filterable
                                            placeholder="Select Type"
                                        >
                                            <el-option
                                                v-for="types in callTypes"
                                                :key="types.id"
                                                :label="types.call_type_name"
                                                :value="types.id"
                                            >
                                            </el-option>
                                        </el-select>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="control-label"
                                        >Service Category<sup>*</sup></label
                                    >

                                    <el-select
                                        class="d-block w-150"
                                        v-model="
                                            configurationInfos.callCategoryId
                                        "
                                        @change="fetchSubCategory"
                                        required
                                        filterable
                                        placeholder="Select Category"
                                    >
                                        <el-option
                                            v-for="category in callCategories"
                                            :key="category.id"
                                            :label="category.call_category_name"
                                            :value="category.id"
                                        >
                                        </el-option>
                                    </el-select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="control-label"
                                        >Service Sub Category<sup>*</sup></label
                                    >

                                    <el-select
                                        class="d-block w-150"
                                        v-model="
                                            configurationInfos.callSubCategoryId
                                        "
                                        @change="fetchRequiredFields"
                                        required
                                        filterable
                                        placeholder="Select Sub Category"
                                    >
                                        <el-option
                                            v-for="subCategory in callSubCategories"
                                            :key="subCategory.id"
                                            :label="
                                                subCategory.call_sub_category_name
                                            "
                                            :value="subCategory.id"
                                        >
                                        </el-option>
                                    </el-select>
                                </div>
                            </div>

                            <div>
                                <!-- Display required fields -->
                                <div
                                    class="form-group"
                                    v-if="requiredFields.length > 0"
                                >
                                    <label class="control-label"
                                        >Required Fields</label
                                    >
                                    <div class="form-row pop-msg">
                                        <div
                                            class="col-md-6"
                                            v-for="(
                                                field, index
                                            ) in requiredFields"
                                            :key="index"
                                        >
                                            <div
                                                class="d-flex justify-content-between align-items-center rounded p-2 border mb-2"
                                            >
                                                <span>{{
                                                    field.input_field_name
                                                }}</span>
                                                <button
                                                    type="button"
                                                    @click="
                                                        confirmRemoveRequiredField(
                                                            index
                                                        )
                                                    "
                                                    class="btn btn-danger btn-sm"
                                                >
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <button
                                            type="button"
                                            class="btn btn-site mt-2"
                                            @click="openRequiredFieldsInNewTab"
                                        >
                                            <i class="icon-plus"></i> Add New
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Group wise TATHours Section -->
                            <div class="form-group">
                                <label class="control-label"
                                    >Group Wise TAT Hours<sup>*</sup></label
                                >
                                <div class="group-tat-list">
                                    <ul class="d-flex p-0">
                                        <li
                                            v-for="item in groups"
                                            :key="item.id"
                                        >
                                            <label class="group-tat">
                                                <input
                                                    type="checkbox"
                                                    :value="item"
                                                    v-model="item.isChecked"
                                                    @change="
                                                        handleGroupChange(item)
                                                    "
                                                />
                                                <div class="group-tat-label">
                                                    {{ item.name }}
                                                </div>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                                <div class="selected-group">
                                    <div
                                        v-for="groupItem in selectedGroups"
                                        :key="groupItem.id"
                                        class="selected-group-item"
                                    >
                                        <span>{{ groupItem.name }}</span>
                                        <div class="tat-hour">
                                            <button
                                                class="btn icon-minus"
                                                type="button"
                                                @click="
                                                    decrementTatHours(groupItem)
                                                "
                                            ></button>
                                            <input
                                                type="number"
                                                v-model.number="
                                                    groupItem.tatHours
                                                "
                                                @input="
                                                    checkNegative(groupItem)
                                                "
                                            />hr
                                            <button
                                                class="btn icon-plus"
                                                type="button"
                                                @click="
                                                    incrementTatHours(groupItem)
                                                "
                                            ></button>
                                        </div>

                                        <!-- Next Group Select -->
                                        <div class="next-group-select">
                                            <el-select
                                                class="d-block w-100"
                                                v-model="
                                                    groupItem.next_group_id
                                                "
                                                required
                                                filterable
                                                placeholder="Select Next Group"
                                            >
                                                <el-option
                                                    v-for="group in availableNextGroups(
                                                        groupItem.id
                                                    )"
                                                    :key="group.id"
                                                    :label="group.name"
                                                    :value="group.id"
                                                >
                                                </el-option>
                                            </el-select>
                                        </div>

                                        <button
                                            class="btn btn-site ml-auto"
                                            type="button"
                                            @click="removeGroup(groupItem.id)"
                                        >
                                            <i class="icon-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label"
                                    >Popup Message</label
                                >
                                <div class="d-flex mb-3">
                                    <label class="radio mr-2"
                                        ><input
                                            type="radio"
                                            value="yes"
                                            name="is_show_popup_msg"
                                            v-model="
                                                configurationInfos.is_show_popup_msg
                                            "
                                            v-validate="'required'"
                                        /><span class="radio-mark"></span>Yes
                                    </label>
                                    <label class="radio">
                                        <input
                                            type="radio"
                                            value="no"
                                            name="is_show_popup_msg"
                                            v-model="
                                                configurationInfos.is_show_popup_msg
                                            "
                                            v-validate="'required'"
                                        /><span class="radio-mark"></span>No
                                    </label>
                                </div>

                                <!-- Additional Popup Messages -->
                                <div
                                    v-if="
                                        configurationInfos.is_show_popup_msg ===
                                        'yes'
                                    "
                                >
                                    <div
                                        class="pop-msg"
                                        v-if="
                                            configurationInfos.popupMessages
                                                .length
                                        "
                                    >
                                        <div
                                            v-for="(
                                                message, index
                                            ) in configurationInfos.popupMessages"
                                            :key="index"
                                            class="d-flex justify-content-between align-items-center rounded p-2 border mb-3"
                                        >
                                            <span>{{ message }}</span>
                                            <button
                                                type="button"
                                                @click="
                                                    removePopupMessage(index)
                                                "
                                                class="btn btn-danger btn-sm"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Message Box -->
                                    <div
                                        v-if="showMessageBox"
                                        class="my-3 border-bottom pb-3"
                                    >
                                        <textarea
                                            v-model="newPopupMessage"
                                            placeholder="Enter additional popup message"
                                            class="form-control mb-3"
                                        ></textarea>
                                        <button
                                            class="btn btn-site mr-2"
                                            type="button"
                                            @click="showMessageBox = false"
                                        >
                                            <i class="icon-trash"></i> Cancel
                                        </button>
                                        <button
                                            class="btn btn-site"
                                            style="background: #000"
                                            type="button"
                                            @click="addPopupMessage"
                                        >
                                            <i class="icon-plus"></i> Add
                                        </button>
                                    </div>

                                    <div>
                                        <button
                                            class="btn btn-site"
                                            type="button"
                                            @click="showMessageBox = true"
                                            :disabled="
                                                configurationInfos.popupMessages
                                                    .length >= maxMessages
                                            "
                                        >
                                            <i class="icon-plus"></i> New
                                        </button>
                                        <span
                                            v-if="
                                                configurationInfos.popupMessages
                                                    .length >= maxMessages
                                            "
                                        >
                                            (Maximum of
                                            {{ maxMessages }} messages allowed)
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Notification Channels<sup
                                            >*</sup
                                        ></label
                                    >
                                    <div class="d-flex">
                                        <!-- <label class="checkbox mr-3">
                                            <input
                                                type="checkbox"
                                                value="sms"
                                                v-model="
                                                    selectedNotificationChannels
                                                "
                                                @change="
                                                    handleNotificationChannelChange
                                                "
                                            /><span class="checkmark"></span
                                            >SMS</label
                                        > -->

                                        <label class="checkbox">
                                            <input
                                                type="checkbox"
                                                value="email"
                                                v-model="
                                                    selectedNotificationChannels
                                                "
                                                @change="
                                                    handleNotificationChannelChange
                                                "
                                            /><span class="checkmark"></span
                                            >Email</label
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <!-- <div
                                    class="col-md-6 form-group"
                                    v-if="
                                        selectedNotificationChannels.includes(
                                            'sms'
                                        )
                                    "
                                >
                                    <label class="control-label"
                                        >SMS Config</label
                                    >
                                    <el-select
                                        class="w-100"
                                        v-model="
                                            configurationInfos.sms_config_id
                                        "
                                        filterable
                                        placeholder="Select SMS Config"
                                    >
                                        <el-option
                                            v-for="sms in smsConfigs"
                                            :key="sms.id"
                                            :label="sms.name"
                                            :value="sms.id"
                                        ></el-option>
                                    </el-select>
                                </div> -->
                                <div
                                    class="col-md-6 form-group"
                                    v-if="
                                        selectedNotificationChannels.includes(
                                            'email'
                                        )
                                    "
                                >
                                    <label class="control-label"
                                        >Email Config</label
                                    >
                                    <el-select
                                        class="w-100"
                                        v-model="
                                            configurationInfos.email_config_id
                                        "
                                        filterable
                                        placeholder="Select Email Config"
                                    >
                                        <el-option
                                            v-for="email in emailConfigs"
                                            :key="email.id"
                                            :label="email.username"
                                            :value="email.id"
                                        ></el-option>
                                    </el-select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-4 form-group">
                                    <label class="control-label m-0 mr-3"
                                        >Escalation</label
                                    >
                                    <div class="d-flex">
                                        <label class="radio mr-2">
                                            <input
                                                type="radio"
                                                value="yes"
                                                name="is_escalation"
                                                v-model="
                                                    configurationInfos.is_escalation
                                                "
                                            />
                                            <span class="radio-mark"></span>Yes
                                        </label>
                                        <label class="radio">
                                            <input
                                                type="radio"
                                                value="no"
                                                name="is_escalation"
                                                v-model="
                                                    configurationInfos.is_escalation
                                                "
                                            />
                                            <span class="radio-mark"></span>No
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="control-label m-0 mr-3"
                                        >Show Attachment</label
                                    >
                                    <div class="d-flex">
                                        <label class="radio mr-2">
                                            <input
                                                type="radio"
                                                value="yes"
                                                name="is_show_attachment"
                                                v-model="
                                                    configurationInfos.is_show_attachment
                                                "
                                            />
                                            <span class="radio-mark"></span>Yes
                                        </label>
                                        <label class="radio">
                                            <input
                                                type="radio"
                                                value="no"
                                                name="is_show_attachment"
                                                v-model="
                                                    configurationInfos.is_show_attachment
                                                "
                                            />
                                            <span class="radio-mark"></span>No
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="control-label m-0 mr-3"
                                        >Verification Check</label
                                    >
                                    <div class="d-flex">
                                        <label class="radio mr-2">
                                            <input
                                                type="radio"
                                                value="yes"
                                                name="is_verification_check"
                                                v-model="
                                                    configurationInfos.is_verification_check
                                                "
                                            />
                                            <span class="radio-mark"></span>Yes
                                        </label>
                                        <label class="radio">
                                            <input
                                                type="radio"
                                                value="no"
                                                name="is_verification_check"
                                                v-model="
                                                    configurationInfos.is_verification_check
                                                "
                                            />
                                            <span class="radio-mark"></span>No
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="control-label m-0 mr-3"
                                        >Customer Behavior Check</label
                                    >
                                    <div class="d-flex">
                                        <label class="radio mr-2">
                                            <input
                                                type="radio"
                                                value="yes"
                                                name="customer_behavior_check"
                                                v-model="
                                                    configurationInfos.customer_behavior_check
                                                "
                                            />
                                            <span class="radio-mark"></span>Yes
                                        </label>
                                        <label class="radio">
                                            <input
                                                type="radio"
                                                value="no"
                                                name="customer_behavior_check"
                                                v-model="
                                                    configurationInfos.customer_behavior_check
                                                "
                                            />
                                            <span class="radio-mark"></span>No
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-4 form-group">
                                    <label class="control-label m-0 mr-3"
                                        >Bulk Ticket Close Permissions</label
                                    >
                                    <div class="d-flex">
                                        <label class="radio mr-2">
                                            <input
                                                type="radio"
                                                value="yes"
                                                name="bulk_ticket_close_perms"
                                                v-model="
                                                    configurationInfos.bulk_ticket_close_perms
                                                "
                                            />
                                            <span class="radio-mark"></span>Yes
                                        </label>
                                        <label class="radio">
                                            <input
                                                type="radio"
                                                value="no"
                                                name="bulk_ticket_close_perms"
                                                v-model="
                                                    configurationInfos.bulk_ticket_close_perms
                                                "
                                            />
                                            <span class="radio-mark"></span>No
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-4 form-group">
                                    <label class="control-label m-0 mr-3"
                                        >Is Group Lead Notified</label
                                    >
                                    <div class="d-flex">
                                        <label class="radio mr-2">
                                            <input
                                                type="radio"
                                                value="yes"
                                                name="is_group_lead_notified"
                                                v-model="
                                                    configurationInfos.is_group_lead_notified
                                                "
                                            />
                                            <span class="radio-mark"></span>Yes
                                        </label>
                                        <label class="radio">
                                            <input
                                                type="radio"
                                                value="no"
                                                name="is_group_lead_notified"
                                                v-model="
                                                    configurationInfos.is_group_lead_notified
                                                "
                                            />
                                            <span class="radio-mark"></span>No
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-4 form-group">
                                    <label class="control-label m-0 mr-3"
                                        >Is User Notified By SMS</label
                                    >
                                    <div class="d-flex">
                                        <label class="radio mr-2">
                                            <input
                                                type="radio"
                                                value="yes"
                                                name="is_user_notified"
                                                v-model="
                                                    configurationInfos.is_user_notified
                                                "
                                            />
                                            <span class="radio-mark"></span>Yes
                                        </label>
                                        <label class="radio">
                                            <input
                                                type="radio"
                                                value="no"
                                                name="is_user_notified"
                                                v-model="
                                                    configurationInfos.is_user_notified
                                                "
                                            />
                                            <span class="radio-mark"></span>No
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div
                                v-if="errors.length > 0"
                                class="error-messages"
                                role="alert"
                            >
                                <ul>
                                    <li
                                        v-for="(messages, field) in errors"
                                        :key="field"
                                    >
                                        <strong>{{ field }}:</strong>
                                        <ul>
                                            <li
                                                v-for="message in messages"
                                                :key="message"
                                            >
                                                {{ message }}
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>

                            <button class="btn btn-site" type="submit">
                                Submit
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
    name: "ServiceTypeConfig",
    data: () => ({
        isLoading: false,
        groups: [],
        requiredFields: [],
        callTypes: [],
        callCategories: [],
        callSubCategories: [],
        selectedGroups: [],
        smsConfigs: [], // Array of SMS config options
        emailConfigs: [], // Array of Email config options
        selectedNotificationChannels: [], // Array to manage selected notification channels
        showMessageBox: false, // Controls the visibility of the message box
        newPopupMessage: "", // Holds the new popup message input
        maxMessages: 3, // Maximum number of popup messages allowed
        configurationInfos: {
            callTypeId: null,
            callCategoryId: null,
            callSubCategoryId: null,
            selectedGroups: [],
            sms_config_id: null,
            email_config_id: null,
            selectedNotificationChannels: [],
            is_show_popup_msg: "no",
            popupMessages: [], // Array to hold additional messages
            is_escalation: "no",
            is_show_attachment: "no",
            is_verification_check: "no",
            customer_behavior_check: "no",
            bulk_ticket_close_perms: "no",
            is_group_lead_notified: "no",
            is_user_notified: "no",
        },
        fieldIndexToRemove: null,
        // errors: {}, // Object to store validation errors
        errors: {
            callTypeId: null,
            callCategoryId: null,
            callSubCategoryId: null,
            selectedGroups: null,
        },
    }),
    mounted() {
        this.fetchGroups();
        this.fetchCallTypes();
        // WILL BE OPEN LATER
        // this.fetchSmsConfigs();
        this.fetchEmailConfigs();
    },
    methods: {
        availableNextGroups(currentGroupId) {
            return this.groups.filter((group) => group.id !== currentGroupId);
        },
        openRequiredFieldsInNewTab() {
            const routeData = this.$router.resolve({
                name: "required-fields-config-add",
                params: {
                    cti: this.configurationInfos.callTypeId,
                    cci: this.configurationInfos.callCategoryId,
                    csci: this.configurationInfos.callSubCategoryId,
                },
            });
            window.open(routeData.href, "_blank");
        },

        checkNegative(group) {
            if (group.tatHours < 0) {
                group.tatHours = 0;
            }
        },
        incrementTatHours(group) {
            // Ensure that group is a valid object
            if (group) {
                group.tatHours = (group.tatHours || 0) + 1;
            }
        },
        decrementTatHours(group) {
            // Ensure that group is a valid object and tatHours is positive
            if (group && (group.tatHours || 0) > 0) {
                group.tatHours = (group.tatHours || 0) - 1;
            }
        },
        handleGroupChange(group) {
            if (group.isChecked) {
                this.addGroup(group);
            } else {
                this.removeGroup(group.id);
            }
        },
        addGroup(group) {
            if (!this.selectedGroups.some((g) => g.id === group.id)) {
                this.selectedGroups.push({ ...group, tatHours: 0 });
            }
        },
        removeGroup(id) {
            this.selectedGroups = this.selectedGroups.filter(
                (group) => group.id !== id
            );
            this.groups = this.groups.map((group) => {
                if (group.id === id) {
                    group.isChecked = false;
                }
                return group;
            });
        },
        async fetchGroups() {
            try {
                const response = await axios.get("/groups");
                console.log("response", response);
                this.groups = response.data;
            } catch (error) {
                console.error("Error fetching groups:", error);
            }
        },
        async fetchCallTypes() {
            try {
                const response = await axios.get("/get-service-types");
                this.requiredFields = [];
                this.callSubCategories = [];
                this.configurationInfos.callSubCategoryId = null;
                this.callTypes = response.data;
            } catch (error) {
                console.error("Error fetching call types:", error);
            }
        },
        async fetchCategories() {
            try {
                const response = await axios.get(
                    `/get-category/${this.configurationInfos.callTypeId}`
                );
                this.requiredFields = [];
                this.callSubCategories = [];
                this.configurationInfos.callSubCategoryId = null;
                this.callCategories = response.data;
            } catch (error) {
                console.error("Error fetching categories:", error);
            }
        },
        async fetchSubCategory() {
            try {
                const response = await axios.get(
                    `/call-sub-by-call-cat-id/${this.configurationInfos.callTypeId}/${this.configurationInfos.callCategoryId}`
                );

                this.callSubCategories = response.data;
            } catch (error) {
                console.error("Error fetching sub categories:", error);
            }
        },
        fetchRequiredFieldsByCategory() {
            return new Promise((resolve, reject) => {
                axios
                    .get(
                        `get-required-field-by-sub-cat-id/${this.configurationInfos.callSubCategoryId}`
                    )
                    .then((response) => {
                        resolve(response);
                    })
                    .catch((error) => {
                        reject(error);
                    })
                    .finally(() => {});
            });
        },
        async fetchRequiredFields() {
            await this.fetchRequiredFieldsByCategory().then((response) => {
                this.requiredFields = response.data.map((field) => ({
                    id: field.id,
                    input_field_name: field.input_field_name,
                }));
            });
        },
        async handleSubmit() {
            // Validate the form before proceeding
            if (!this.validateForm()) {
                return; // Stop submission if validation fails
            }

            try {
                // Adding selected groups with TAT hours to configurationInfos
                this.configurationInfos.selectedGroups =
                    this.selectedGroups.map((group) => ({
                        id: group.id,
                        name: group.name,
                        tatHours: group.tatHours,
                        nextGroupId: group.next_group_id,
                    }));

                // Extract only the IDs from requiredFields and convert to a comma-separated string
                const requiredFieldIds = this.requiredFields
                    .map((field) => field.id)
                    .join(",");

                // Prepare the payload for the API call
                const payload = {
                    ...this.configurationInfos,
                    requiredFieldIds,
                };

                // console.log("payload", payload);
                // return false;

                // API call
                const response = await axios.post(
                    "/service-type-config",
                    payload
                );

                // Use the global toast function
                this.$showToast("Form submitted successfully", {
                    type: "success",
                });

                // Reset form and navigate
                this.$refs.serviceTypeConfigForm.reset();
                this.resetForm();
                this.$router.push({ name: "service-type-config-index" });
            } catch (error) {
                if (error.response && error.response.data.errors) {
                    // Extract and format error messages
                    this.errors = error.response.data.errors;
                } else {
                    // Generic error message if response does not include validation errors
                    this.$showToast("There was an error submitting the form", {
                        type: "error",
                    });
                }
            }
        },

        addPopupMessage() {
            if (
                this.newPopupMessage &&
                this.configurationInfos.popupMessages.length < this.maxMessages
            ) {
                this.configurationInfos.popupMessages.push(
                    this.newPopupMessage
                );
                this.newPopupMessage = ""; // Clear input field
                this.showMessageBox = false; // Hide message box
            }
        },
        removePopupMessage(index) {
            this.configurationInfos.popupMessages.splice(index, 1);
        },
        async fetchSmsConfigs() {
            try {
                // const response = await axios.get("/sms-configs");
                // this.smsConfigs = response.data;
                this.smsConfigs = [
                    { id: 1, name: "NAGAD" },
                    { id: 2, name: "DIGITAL" },
                ];
            } catch (error) {
                console.error("Error fetching SMS configs:", error);
            }
        },
        async fetchEmailConfigs() {
            try {
                const response = await axios.get("/email-configs");
                this.emailConfigs = response.data;
            } catch (error) {
                console.error("Error fetching Email configs:", error);
            }
        },
        handleNotificationChannelChange() {
            this.configurationInfos.selectedNotificationChannels = [
                ...this.selectedNotificationChannels,
            ];

            // Update configurationInfos based on selected notification channels
            // Reset config IDs if channels are deselected
            if (!this.selectedNotificationChannels.includes("sms")) {
                this.configurationInfos.sms_config_id = null;
            }
            if (!this.selectedNotificationChannels.includes("email")) {
                this.configurationInfos.email_config_id = null;
            }
        },
        confirmRemoveRequiredField(index) {
            const isConfirmed = window.confirm(
                "Are you sure you want to remove this field?"
            );
            if (isConfirmed) {
                this.removeRequiredField(index);
            }
        },
        async removeRequiredField(index) {
            try {
                // Check if index is valid
                if (index < 0 || index >= this.requiredFields.length) {
                    console.error("Index out of bounds");
                    return;
                }

                const field = this.requiredFields[index];
                if (!field || !field.id) {
                    console.error("Field or field.id is undefined");
                    return;
                }

                // Make DELETE request to remove field from server
                await axios.delete(`/required-fields-configs/${field.id}`);

                // Remove field from the array
                this.requiredFields.splice(index, 1);

                // Ensure reactivity
                this.requiredFields = [...this.requiredFields];

                // Update configurationInfos.requiredFields
                this.configurationInfos.requiredFields =
                    this.requiredFields.map((field) => ({
                        id: field.id,
                        input_field_name: field.input_field_name,
                    }));
            } catch (error) {
                console.error("Error removing required field:", error);
            }
        },

        validateForm() {
            // Check for required fields
            if (!this.configurationInfos.callTypeId) {
                this.$showToast("Service Type is required.", { type: "error" });
                return false;
            }

            if (!this.configurationInfos.callCategoryId) {
                this.$showToast("Service Category is required.", {
                    type: "error",
                });
                return false;
            }

            if (!this.configurationInfos.callSubCategoryId) {
                this.$showToast("Service Sub-Category is required.", {
                    type: "error",
                });
                return false;
            }

            if (this.selectedGroups.length === 0) {
                this.$showToast("Please select at least one group.", {
                    type: "error",
                });
                return false;
            }

            if (
                this.configurationInfos.selectedNotificationChannels.length ===
                0
            ) {
                this.$showToast(
                    "Please select at least one notification channel.",
                    { type: "error" }
                );
                return false;
            }

            /* if (this.configurationInfos.sms_config_id === null) {
                this.$showToast("SMS Config is required.", { type: "error" });
                return false;
            } */

            if (this.configurationInfos.email_config_id === null) {
                this.$showToast("Email Config is required.", { type: "error" });
                return false;
            }

            if (
                this.selectedGroups.some(
                    (group) =>
                        !group.id ||
                        !group.name ||
                        group.tatHours === undefined ||
                        group.tatHours === null
                )
            ) {
                this.$showToast(
                    "Each selected group must have an ID, name, and TAT hours.",
                    { type: "error" }
                );
                return false;
            }

            // If all checks pass
            return true;
        },

        resetForm() {
            this.configurationInfos = {
                callTypeId: null,
                callCategoryId: null,
                callSubCategoryId: null,
                is_show_popup_msg: "no",
                popupMessages: [],
                is_escalation: "no",
                is_show_attachment: "no",
                is_verification_check: "no",
                customer_behavior_check: "no",
                bulk_ticket_close_perms: "no",
                is_group_lead_notified: "no",
                is_user_notified: "no",
                sms_config_id: null,
                email_config_id: null,
                selectedNotificationChannels: [],
            };
            this.selectedGroups = [];
            this.selectedNotificationChannels = [];
            this.groups = this.groups.map((group) => ({
                ...group,
                isChecked: false,
            }));
        },
    },
};
</script>
