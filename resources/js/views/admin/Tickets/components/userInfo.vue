<template>
    <div v-if="isRegistered" class="customer-info">
        <h4 class="sub-title mb-2 registered-customer">
            <i class="icon-user-check"></i> Registered
        </h4>
        <div class="form-group">
            <label class="control-label">Customer Name</label>
            <div class="form-control-plaintext">{{ users.customerName }}</div>
        </div>
        <div class="form-group">
            <label class="control-label">NID Card No</label>
            <div class="form-control-plaintext">{{ users.nidCard }}</div>
        </div>
        <div class="form-group">
            <label class="control-label">Father’s Name</label>
            <div class="form-control-plaintext">{{ users.fathersName }}</div>
        </div>
        <div class="form-group">
            <label class="control-label">Mother’s Name</label>
            <div class="form-control-plaintext">{{ users.mothersName }}</div>
        </div>
        <div class="form-group">
            <label class="control-label">Date of Birth</label>
            <div class="form-control-plaintext">{{ users.birthDate }}</div>
        </div>
        <div class="form-group">
            <label class="control-label">Address</label>
            <div class="form-control-plaintext">{{ users.address }}</div>
        </div>
        <div class="form-group m-0">
            <label class="control-label">Transaction Details</label>
            <div class="transaction-list mt-2">
                <ul>
                    <li
                        v-for="(transaction, index) in users.transactionDetails"
                        :key="index"
                        :class="
                            transaction.includes('In') ? 'cash-in' : 'cash-out'
                        "
                    >
                        {{ transaction }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div v-else class="no-date">
        <img src="/images/no-data.svg" alt="No Data Found" />
        <h3>No data found</h3>
    </div>
</template>

<script>
export default {
    name: "userInfo",
    data: () => ({
        isRegistered: true,
        users: {
            customerName: "kazi Sakib",
            nidCard: "123456789",
            fathersName: "Kazi Aiub",
            transactionDetails: [
                "Tk. 1500 Cash Out",
                "Tk. 2700 Cash In",
                "Tk. 3500 Cash In",
                "Tk. 500 Cash out",
                "Tk. 1500 Cash Out",
                "Tk. 2700 Cash In",
                "Tk. 3500 Cash In",
                "Tk. 500 Cash out",
            ],
            mothersName: "kazi Ratna",
            birthDate: "20/11/2024",
            address: "Demra, Dhaka, Bangladesh",
        },
    }),
};
</script>
