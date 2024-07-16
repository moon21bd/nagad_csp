<template>
    <div>
        <form>
            <div class="form-group">
                <label for="name">Call Type</label>
                <select v-model="requiredFields.call_type_id" @change="fetchCategories" required>
                    <option :value="null" disabled>Select Call Type</option>
                    <option v-for="type in callTypes" :key="type.id" :value="type.id">
                        {{ type.call_type_name }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="name">Call Category</label>
                <select v-model="requiredFields.call_category_id" @change="fetchSubCategory" required>
                    <option :value="null" disabled>Select Call Category</option>
                    <option v-for="category in callCategories" :key="category.id" :value="category.id">
                        {{ category.call_category_name }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="name">Call Sub Category</label>
                <select v-model="requiredFields.call_sub_category_id" @change="fetchRequiredFields" required>
                    <option :value="null" disabled>Select Call Sub Category</option>
                    <option v-for="subCategory in callSubCategories" :key="subCategory.id" :value="subCategory.id">
                        {{ subCategory.call_sub_category_name }}
                    </option>
                </select>
            </div>

            <ValidationObserver v-slot="{ invalid }">
                <div v-if="selectedField">
                    <label :for="selectedField.input_field_name">{{ selectedField.input_field_name }}</label>

                    <ValidationProvider
                        :rules="generateValidationRules(selectedField.input_validation)" v-slot="{ errors }">
                        <component :is="inputComponent" :field="selectedField"></component>
                        <span v-show="errors.length" class="text-danger">{{ errors[0] }}</span>
                    </ValidationProvider>
                </div>
                <button type="submit" class="btn btn-primary" :disabled="invalid">Save</button>
            </ValidationObserver>
        </form>
    </div>
</template>

<script>

/*import {required, max} from 'vee-validate/dist/rules';
import {extend, ValidationObserver, ValidationProvider} from 'vee-validate';

extend('required', {
    ...required,
    message: 'This field is required'
});

extend('max', {
    ...max,
    message: 'This field must be {length} characters or less'
});*/

// import { Validator } from 'vee-validate';
import {Validator, ValidationObserver, ValidationProvider} from 'vee-validate';

// Extend rules as shown above
Validator.extend('required', {
    getMessage: field => `${field} is required.`,
    validate: value => !!value
});

Validator.extend('max', {
    getMessage: (field, [length]) => `${field} may not be greater than ${length} characters.`,
    validate: (value, [length]) => value.length <= length
});

export default {
    name: 'ticketsRequiredFieldsAdd',
    components: {
        ValidationObserver,
        ValidationProvider,
        SelectInput: {
            props: ['field'],
            template: `
                <select :name="field.input_field_name" v-validate="field.input_validation">
                    <option v-for="option in field.input_value.split(',')" :key="option" :value="option">{{ option }}
                    </option>
                </select>
            `
        },
        NumberInput: {
            props: ['field'],
            template: `<input type="number" :name="field.input_field_name" v-validate="field.input_validation"/>`
        },
        TextInput: {
            props: ['field'],
            template: `<input type="text" :name="field.input_field_name" v-validate="field.input_validation"/>`
        },
        TextareaInput: {
            props: ['field'],
            template: `<textarea :name="field.input_field_name" v-validate="field.input_validation"></textarea>`
        },
        DateTimeInput: {
            props: ['field'],
            template: `<input type="datetime-local" :name="field.input_field_name"
                              v-validate="field.input_validation"/>`
        }
    },
    data() {
        return {
            callTypes: [],
            callCategories: [],
            callSubCategories: [],
            requiredFields: {
                call_type_id: null,
                call_category_id: null,
                call_sub_category_id: null,
            },
            // selectedField: [],
            requiredFieldsInfo: [],
            selectedSubCategoryId: null,
            // errors: {}
        }
    },
    mounted() {
        this.fetchCallTypes();
    },
    computed: {
        selectedField() {
            console.log('selectedField', this.requiredFieldsInfo.find(field => field.call_sub_category_id === this.selectedSubCategoryId))
            return this.requiredFieldsInfo.find(field => field.call_sub_category_id === this.selectedSubCategoryId);
        },
        inputComponent() {
            if (!this.selectedField) return null;
            const inputType = this.selectedField.input_type;
            switch (inputType) {
                case 'select':
                    return 'SelectInput';
                case 'number':
                    return 'NumberInput';
                case 'string':
                    return 'TextInput';
                case 'text':
                    return 'TextareaInput';
                case 'datetime':
                    return 'DateTimeInput';
                default:
                    return null;
            }
        }
    },
    methods: {

        async fetchCallTypes() {
            try {
                const response = await axios.get("/call-types");
                this.callTypes = response.data;
            } catch (error) {
                console.error("Error fetching call types:", error);
            }
        },
        async fetchCategories() {
            try {
                const response = await axios.get(`/get-category/${this.requiredFields.call_type_id}`);
                this.callCategories = response.data;
            } catch (error) {
                console.error('Error fetching call categories:', error);
            }
        },
        async fetchSubCategory() {
            try {
                const response = await axios.get(`/call-sub-by-call-cat-id/${this.requiredFields.call_type_id}/${this.requiredFields.call_category_id}`);
                this.callSubCategories = response.data;
            } catch (error) {
                console.error('Error fetching call sub categories:', error);
            }
        },
        async fetchRequiredFields() {
            try {
                const response = await axios.get(`/get-required-fields/${this.requiredFields.call_type_id}/${this.requiredFields.call_category_id}/${this.requiredFields.call_sub_category_id}`);
                this.requiredFieldsInfo = response.data;
                this.selectedSubCategoryId = this.requiredFields.call_sub_category_id;
            } catch (error) {
                console.error('Error fetching required fields:', error);
            }
        },
        /*async fetchRequiredFields() {
            try {
                const response = await axios.get(`/get-required-fields/${this.requiredFields.call_type_id}/${this.requiredFields.call_category_id}/${this.requiredFields.call_sub_category_id}`);
                this.requiredFieldsInfo = response.data;
                this.selectedField = this.requiredFieldsInfo[0]; // assuming you have a list of fields
            } catch (error) {
                console.error('Error fetching required fields:', error);
            }
        },*/
        generateValidationRules(validationString) {
            if (!validationString) return {};
            const rulesArray = validationString.split(',');
            const rules = {};
            rulesArray.forEach(rule => {
                const [ruleName, ruleValue] = rule.split(':');
                rules[ruleName] = ruleValue || true;
            });
            return rules;
        },
        async submitForm() {
            // Form submission logic
        }
    }
}
</script>
