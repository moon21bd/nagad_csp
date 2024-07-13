<template>
    <div>
        <form>
            <div class="form-group">
                <label for="name">Call Type Id</label>
                <input type="text" v-model="callTypeId" class="form-control" id="name"
                       placeholder="Enter Call Type Name">
            </div>
            <div class="form-group">
                <label for="name">Call Category Id</label>
                <input type="text" v-model="callCategoryId" class="form-control" id=""
                       placeholder="Enter Call Type Name">
            </div>
            <div class="form-group">
                <label for="exampleFormControlSelect1">Input Filed Name</label>
                <select class="form-control"  v-model="subCategoryReportId"
                        id="">
                    <option value="1" @click.prevent="submitReportId">Fraud Gd</option>
                    <option value="2" @click.prevent="submitReportId">Fraus Activity</option>
                </select>
            </div>
          <div v-for="(data,index) in reportData">
              <div class="form-group" v-if="data.input_type !== 'select'">
                  <label for="name">{{data.input_field_name}}</label>
                  <input type="text" class="form-control" id=""
                         placeholder="Enter Call Type Name">
              </div>
              <div class="form-group" v-else-if = "data.input_type === 'datetime'">
                  <label for="exampleFormControlSelect1">{{data.input_field_name}}</label>
                  <input type="datetime-local" :value="myDate && myDate.toISOString().split('T')[0]"
                         @input="myDate = getDateClean($event.target.valueAsDate)">
              </div>
              <div class="form-group" v-else>
                  <label for="exampleFormControlSelect1">{{data.input_field_name}}</label>
                  <select class="form-control"
                          id="" >
                      <option v-for="option in selectOptionValue[index].input_value" :value="option">{{option}}</option>
                  </select>
              </div>
          </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</template>

<script>
export default {
    name: 'configAdd',
    data() {
        return {
            name: '',
            statusValue: '',
            actionUrl: 'store_config/',
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
            myDate: new Date('2011-04-11T10:20:30Z')
        }
    },
    mounted() {
        // if (this.requestedPage === 'callType') {
        //     this.actionUrl += 'store_call_type'
        // }
    },
    methods: {
        async init() {

        },
        storeData() {
            return new Promise((resolve, reject) => {
                axios
                    .post(this.actionUrl, {
                        inputFiledName: this.inputFiledName,
                        callTypeId: this.callTypeId,
                        callCategoryId: this.callCategoryId,
                        callSubCategoryId: this.callSubCategoryId,
                        inputValue: this.inputValue,
                        inputType: this.inputType,
                        inputValidation: this.inputValidation,
                        statusValue: this.statusValue
                    })
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
        async submit() {
            await this.storeData().then(response => {
                this.inputFiledName = ''
                this.callTypeId = null
                this.callCategoryId = null
                this.callSubCategoryId = null
                this.inputValue = ''
                this.inputType = ''
                this.inputValidation = 'required,'
                this.statusValue = ''
            })
        },
        fetchSubcategoryReportField() {
            return new Promise((resolve, reject) => {
                axios
                    .get(this.actionUrl+this.subCategoryReportId)
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
