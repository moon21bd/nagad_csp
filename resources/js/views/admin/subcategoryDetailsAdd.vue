<template>
    <div>
        <form>
            <div class="form-group">
                <label for="name">Call Type Id</label>
                <select v-model="callTypeId" @change="fetchCategories" required>
                    <option :value="null" disabled>Select Call Type</option>
                    <option v-for="type in callTypes" :key="type.id" :value="type.id">
                        {{ type.call_type_name }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="name">Call Category Id</label>
                <select v-model="callCategoryId" @change="fetchSubCategory" required>
                    <option :value="null" disabled>Select Call Category</option>
                    <option v-for="category in callCategories"
                            :key="category.id"
                            :value="category.id">
                        {{ category.call_category_name }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="name">Call sub Category Id</label>
                <select v-model="callSubCategoryId" required>
                    <option :value="null" disabled>Select Call Sub Category</option>
                    <option v-for="subCategory in callSubCategories"
                            :key="subCategory.id"
                            :value="subCategory.id" @click.prevent="submitReportId">
                        {{ subCategory.call_sub_category_name }}
                    </option>
                </select>
            </div>
          <div v-for="(data,index) in reportData">
              <div class="form-group" v-if="data.input_type === 'select'">
                  <label for="exampleFormControlSelect1">{{data.input_field_name}}</label>
                  <select class="form-control"
                          id="" >
                      <option v-for="option in selectOptionValue[index].input_value" :value="option">{{option}}</option>
                  </select>
              </div>
              <div class="form-group" v-else-if = "data.input_type === 'datetime'">
                  <label for="exampleFormControlSelect1">{{data.input_field_name}}</label>
                  <datetime class="form-control" format="YYYY-MM-DD h:i:s" v-model="date"></datetime>

              </div>
              <div class="form-group" v-else>
                  <label for="name">{{data.input_field_name}}</label>
                  <input type="text" class="form-control" id=""
                         placeholder="Enter Call Type Name" >
              </div>
          </div>

            <button type="submit" @click="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</template>

<script>
import datetime from 'vuejs-datetimepicker';
export default {
    name: 'subCategoryDetailsAdd',
    components: { datetime },
    data() {
        return {
            name: '',
            statusValue: '',
            actionUrl: 'get-nc-filed_config-data/',
            apiUrl: '',
            callTypeId: null,
            callCategoryId: null,
            callSubCategoryId: null,
            inputValue: '',
            inputType: '',
            inputFiledName: '',
            inputValidation: 'required,',
            subCategoryReportId: null,
            reportData:{},
            selectOptionValue:{},
            myDate: new Date('2011-04-11T10:20:30Z'),
            callTypes:{},
            callCategories:{},
            callSubCategories:{},
            date:''
        }
    },
    mounted() {
        this.init()
    },
    methods: {
        async init() {
           await this.fetchCallTypes()
        },
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
                const response = await axios.get(`/get-category/${this.callTypeId}`);
                this.callCategories = response.data;
                console.log('call category',this.callCategories)
            } catch (error) {
                console.error('Error fetching call categories:', error);
            }
        },
        async fetchSubCategory() {
            try {
                const response = await axios.get(`/get-sub-category/${this.callCategoryId}`);
                this.callSubCategories = response.data;
            } catch (error) {
                console.error('Error fetching call sub categories:', error);
            }
        },
        async fetchInputFields() {
            try {
                const response = await axios.get(`/get-sub-category/${this.callCategoryId}`);
                this.callSubCategories = response.data;
            } catch (error) {
                console.error('Error fetching call sub categories:', error);
            }
        },
        async submit() {
                // this.inputFiledName = ''
                // this.callTypeId = null
                // this.callCategoryId = null
                // this.callSubCategoryId = null
                // this.inputValue = ''
                // this.inputType = ''
                // this.inputValidation = 'required,'
                // this.statusValue = ''
        },
        fetchSubcategoryReportField() {
            return new Promise((resolve, reject) => {
                axios
                    .get(this.actionUrl+this.callSubCategoryId)
                    .then((response) => {
                        resolve(response)
                    })
                    .catch((error) => {
                        reject(error)
                    })
                    .finally(() => {
                    })
            })
        },
        async submitReportId() {
            console.log('action url',this.actionUrl)
            console.log('report id',this.subCategoryReportId)
            await this.fetchSubcategoryReportField().then(response => {
                this.reportData = response.data
                this.setSelectOptionValue(response.data)
                console.log(this.reportData)
                console.log(typeof this.reportData[0].input_value,'input value type')

            })
        },
        setSelectOptionValue(value)
        {
            this.selectOptionValue = value
             for ( let i = 0 ; i< value.length; i++)
             {
                 if (value[i].input_type === 'select')
                 {
                     this.selectOptionValue[i].input_value = value[i].input_value.split(",")
                 }
             }
        },
        getDateClean(currDate) {
            // need to convert to UTC to get working input filter
            console.log(currDate);
            let month = currDate.getUTCMonth() + 1;
            if (month < 10) month = "0" + month;
            let day = currDate.getUTCDate();
            if (day < 10) day = "0" + day;
            const dateStr =
                currDate.getUTCFullYear() + "-" + month + "-" + day + "T00:00:00";
            console.log(dateStr);
            const d = new Date(dateStr);
            console.log(d);
            return d;
        }
    }
}
</script>
