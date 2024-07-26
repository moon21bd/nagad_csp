<template>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="sub-title mb-2">
                        <i class="icon-sliders text-danger"></i> Service Type
                        Configurations
                    </h4>
                    <form
                        ref="serviceTypeConfigForm"
                        @submit.prevent="handleSubmit"
                    >
                        <div class="form-row">
                            <div class="col-md-4 form-group">
                                <label class="control-label"
                                    >Service Type<sup>*</sup></label
                                >
                                <div class="custom-style">
                                    <el-select
                                        class="d-block w-100"
                                        v-model="configurationInfos.callTypeId"
                                        @change="fetchCategories"
                                        required
                                        filterable
                                        placeholder="Select Type"
                                    >
                                        <el-option
                                            v-for="type in callTypes"
                                            :key="type.id"
                                            :label="type.call_type_name"
                                            :value="type.id"
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
                                    v-model="configurationInfos.callCategoryId"
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

                        <div class="form-row">
                            <!-- Display required fields -->
                            <div v-if="requiredFields.length > 0">
                                <p>Required Fields:</p>
                                <div
                                    v-for="(field, index) in requiredFields"
                                    :key="index"
                                    class="d-flex justify-content-between align-items-center mb-2"
                                >
                                    <span>{{ field.input_field_name }}</span>
                                    <button
                                        type="button"
                                        @click="
                                            confirmRemoveRequiredField(index)
                                        "
                                        class="btn btn-danger btn-sm"
                                    >
                                        Remove
                                    </button>
                                </div>

                                <router-link
                                    class="btn btn-site btn-sm mr-2 py-1 px-2 router-link-active"
                                    :to="{
                                        name: 'required-fields-config-add',
                                        params: {
                                            cti: this.configurationInfos
                                                .callTypeId,
                                            cci: this.configurationInfos
                                                .callCategoryId,
                                            csci: this.configurationInfos
                                                .callSubCategoryId,
                                        },
                                    }"
                                >
                                    <i class="icon-plus"></i> Add New
                                </router-link>
                            </div>
                        </div>

                        <!-- Group wise TATHours Section -->
                        <div class="form-row">
                            <p>Group wise TAT Hours</p>

                            <div class="left-side-mal">
                                <ul>
                                    <li v-for="group in groups" :key="group.id">
                                        <input
                                            type="checkbox"
                                            :value="group"
                                            v-model="group.isChecked"
                                            @change="handleGroupChange(group)"
                                        />
                                        {{ group.name }}
                                    </li>
                                </ul>
                            </div>
                            <div class="right-side-mal">
                                <div
                                    v-for="group in selectedGroups"
                                    :key="group.id"
                                    class="selected-group"
                                >
                                    <span>{{ group.name }}</span>
                                    <input
                                        type="number"
                                        v-model.number="group.tatHours"
                                        @input="checkNegative(group)"
                                        placeholder="Enter TAT hours"
                                    />
                                    <span>hours</span>
                                    <button
                                        type="button"
                                        @click="incrementTatHours(group)"
                                    >
                                        +
                                    </button>
                                    <button
                                        type="button"
                                        @click="decrementTatHours(group)"
                                    >
                                        -
                                    </button>
                                    <button
                                        type="button"
                                        @click="removeGroup(group.id)"
                                    >
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Popup message Section -->
                        <div class="form-row">
                            <div
                                class="col-md-6 form-group d-flex align-items-center"
                            >
                                <label class="control-label m-0 mr-3"
                                    >Popup Message</label
                                >
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
                                <div
                                    class="text-danger"
                                    v-show="errors.has('gender')"
                                >
                                    {{ errors.first("gender") }}
                                </div>
                            </div>

                            <!-- Additional Popup Messages -->
                            <div
                                v-if="
                                    configurationInfos.is_show_popup_msg ===
                                    'yes'
                                "
                            >
                                <button
                                    type="button"
                                    @click="showMessageBox = true"
                                    :disabled="
                                        configurationInfos.popupMessages
                                            .length >= maxMessages
                                    "
                                >
                                    Add Popup Message
                                </button>
                                <span
                                    v-if="
                                        configurationInfos.popupMessages
                                            .length >= maxMessages
                                    "
                                >
                                    (Maximum of {{ maxMessages }} messages
                                    allowed)
                                </span>

                                <!-- Message Box -->
                                <div v-if="showMessageBox" class="message-box">
                                    <textarea
                                        v-model="newPopupMessage"
                                        placeholder="Enter additional popup message"
                                        class="form-control"
                                    ></textarea>
                                    <button
                                        type="button"
                                        @click="addPopupMessage"
                                    >
                                        Add Message
                                    </button>
                                    <button
                                        type="button"
                                        @click="showMessageBox = false"
                                    >
                                        Cancel
                                    </button>
                                </div>

                                <!-- Display additional popup messages -->
                                <ul
                                    v-if="
                                        configurationInfos.popupMessages.length
                                    "
                                >
                                    <li
                                        v-for="(
                                            message, index
                                        ) in configurationInfos.popupMessages"
                                        :key="index"
                                    >
                                        {{ message }}
                                        <button
                                            type="button"
                                            @click="removePopupMessage(index)"
                                        >
                                            Remove
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Notification Channel Section -->
                        <div class="form-row">
                            <div class="col-md-6 form-group">
                                <label class="control-label"
                                    >Notification Channels</label
                                >
                                <div>
                                    <label>
                                        <input
                                            type="checkbox"
                                            value="sms"
                                            v-model="
                                                selectedNotificationChannels
                                            "
                                            @change="
                                                handleNotificationChannelChange
                                            "
                                        />
                                        SMS
                                    </label>
                                    <label>
                                        <input
                                            type="checkbox"
                                            value="email"
                                            v-model="
                                                selectedNotificationChannels
                                            "
                                            @change="
                                                handleNotificationChannelChange
                                            "
                                        />
                                        Email
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="selectedNotificationChannels.includes('sms')"
                        >
                            <div class="form-row">
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >SMS Config ID</label
                                    >
                                    <el-select
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
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="
                                selectedNotificationChannels.includes('email')
                            "
                        >
                            <div class="form-row">
                                <div class="col-md-6 form-group">
                                    <label class="control-label"
                                        >Email Config ID</label
                                    >
                                    <el-select
                                        v-model="
                                            configurationInfos.email_config_id
                                        "
                                        filterable
                                        placeholder="Select Email Config"
                                    >
                                        <el-option
                                            v-for="email in emailConfigs"
                                            :key="email.id"
                                            :label="email.name"
                                            :value="email.id"
                                        ></el-option>
                                    </el-select>
                                </div>
                            </div>
                        </div>

                        <!-- Escalation Section -->
                        <div class="form-row">
                            <div
                                class="col-md-6 form-group d-flex align-items-center"
                            >
                                <label class="control-label m-0 mr-3"
                                    >Escalation</label
                                >
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

                        <!-- Show Attachment Section -->
                        <div class="form-row">
                            <div
                                class="col-md-6 form-group d-flex align-items-center"
                            >
                                <label class="control-label m-0 mr-3"
                                    >Show Attachment</label
                                >
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

                        <!-- Verification Check Section -->
                        <div class="form-row">
                            <div
                                class="col-md-6 form-group d-flex align-items-center"
                            >
                                <label class="control-label m-0 mr-3"
                                    >Verification Check</label
                                >
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

                        <!-- Customer Behavior Check Section -->
                        <div class="form-row">
                            <div
                                class="col-md-6 form-group d-flex align-items-center"
                            >
                                <label class="control-label m-0 mr-3"
                                    >Customer Behavior Check</label
                                >
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

                        <!-- Bulk Ticket Close Permissions Section -->
                        <div class="form-row">
                            <div
                                class="col-md-6 form-group d-flex align-items-center"
                            >
                                <label class="control-label m-0 mr-3"
                                    >Bulk Ticket Close Permissions</label
                                >
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

                        <button class="btn btn-site" type="submit">
                            Submit
                        </button>
                    </form>
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
        },
        fieldIndexToRemove: null,
    }),
    mounted() {
        this.fetchGroups();
        this.fetchCallTypes();
        this.fetchSmsConfigs();
        this.fetchEmailConfigs();
    },
    methods: {
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
            console.log("handleSubmit Called");
            try {
                // Adding selected groups with TAT hours to configurationInfos
                this.configurationInfos.selectedGroups =
                    this.selectedGroups.map((group) => ({
                        id: group.id,
                        tatHours: group.tatHours,
                    }));

                // Extract only the IDs from requiredFields and convert to a comma-separated string
                const requiredFieldIds = this.requiredFields
                    .map((field) => field.id)
                    .join(",");

                // Prepare the payload for the API call
                const payload = {
                    ...this.configurationInfos,
                    requiredFieldIds, // Add the comma-separated string to the payload
                };
                const response = await axios.post(
                    "/service-type-config",
                    payload
                );
                console.log(
                    "Form submitted successfully, resp: ",
                    response.data
                );
                this.$refs.serviceTypeConfigForm.reset();
                this.resetForm();
            } catch (error) {
                console.error("There was an error submitting the form:", error);
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
                this.smsConfigs = [{ id: 1, name: "NAGAD" }];
            } catch (error) {
                console.error("Error fetching SMS configs:", error);
            }
        },
        async fetchEmailConfigs() {
            try {
                // const response = await axios.get("/email-configs");
                // this.emailConfigs = response.data;
                this.emailConfigs = [{ id: 1, name: "noreply@nagad.com.bd" }];
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
