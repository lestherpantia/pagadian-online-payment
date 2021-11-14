<template>
    <div>
        <loading :active.sync="isLoading"
                 :is-full-page="fullPage"></loading>

        <div class="button-container">
            <button id="add-new" v-on:click="openModal"><i class="fas fa-plus mr-2"></i> Add new RPT</button>
            <button id="payment-button" v-on:click="openModal"><i class="fas fa-shopping-cart mr-2"></i> Proceed to Payment</button>
        </div>


        <table id="rpt-table">
            <thead>
                <tr>
                    <th>PIN</th>
                    <th>ARP</th>
                    <th style="width: 8%">For Payment?</th>
                    <th style="width: 8%">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in rptTableData">
                    <td>{{ item.pin }}</td>
                    <td>{{ item.arp }}</td>
                    <td><input id="checkbox" type="checkbox" v-on:change="checkPayment(index, item.amount, $event)"></td>
                    <td><button id="warning" class="btn p-0" v-on:click="deleteRecord(index, item.amount)"><i class="fas fa-trash-alt mr-2"></i></button></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total Amount</th>
                    <th style="text-align: right">{{ formatPrice(totalAmount) }}</th>
                </tr>
            </tfoot>
        </table>

        <!-- add modal -->
        <div class="modal" tabindex="-1" role="dialog" id="add-rpt">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Find PIN & ARP</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <label>PIN</label>
                            <input id="pin" type="text" v-model="pin" class="form-control mb-1">
                            <label>ARP</label>
                            <input id="arp" type="text" v-model="arp" class="form-control mb-1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="add-details" v-on:click="addDetails"><i class="fas fa-plus mr-2"></i> Add Details</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

// Import component
import Loading from 'vue-loading-overlay';
import Swal from 'sweetalert2/dist/sweetalert2.js'
import 'vue-loading-overlay/dist/vue-loading.css';
import 'sweetalert2/src/sweetalert2.scss';

export default {

    data() {
        return {
            pin: '',
            arp: '',
            username: '',
            fullPage: true,
            isLoading: false,
            totalAmount: 0,

            rptTableData: [],
        }
    },

    components: {
        Loading
    },

    methods: {

        formatPrice(value) {
            let val = (value/1).toFixed(2).replace(',', '.')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        },

        intialData() {
            this.isLoading = true;
            axios.get('home/profile').then(response => {
                this.username = response.data.username;
            }).finally(() => this.isLoading = false);
        },

        openModal() {
            $('#add-rpt').modal('show');
        },

        messageBox(type, title, text, button) {
            Swal.fire({
                type: type,
                title: title,
                text: text,
                confirmButtonText: button
            })
        },

        addProperty() {
            this.isLoading = true;
        },

        addDetails() {

            if(this.pin === '' || this.arp === '') {
                this.messageBox('error', 'Error', 'PIN and ARP is Required!', 'Okay');
                return;
            }

            this.isLoading = true;

            axios.get('home/search/' + this.pin + '/' + this.arp)
                .then(response => {

                    let id = response.data.details.id;
                    let duplicate = false;

                    this.rptTableData.forEach(function(item)
                    {
                        if(item.id === id)
                        {
                            duplicate = true;
                        }
                    });

                    if(duplicate === false)
                    {
                        this.rptTableData.push(response.data.details);
                        this.pin = '';
                        this.arp = '';
                        $('#add-rpt').modal('hide');
                    }
                    else
                    {
                        this.messageBox('error', 'Error', 'Already Exist!', 'Okay');
                    }

                }).catch(error => {
                let err_msg = error.response.data.error;
                this.messageBox('error', 'Error', err_msg, 'Okay');
            })
                .finally(() => this.isLoading = false);
        },

        checkPayment(index, amount, e)
        {
            if(e.target.checked === true)
            {
                this.rptTableData[index]['for_payment'] = true;
                this.totalAmount += parseFloat(amount);
            }
            else
            {
                this.rptTableData[index]['for_payment'] = false;
                this.totalAmount -= parseFloat(amount);
            }
        },


        deleteRecord(index, amount)
        {
            if(this.rptTableData[index]['for_payment'] === true)
            {
                this.totalAmount -= amount;
            }

            this.rptTableData.splice(index,1);
        },




    },

    mounted() {
        this.intialData();
    }
}
</script>

<style scoped>
#main-container {
    height: 100vh;
}

.card {
    /*max-height: 600px;*/
}

.card-header .project-title {
    font-weight: bold;
}

.card-header .username {
    font-size: 15px;
}


#rpt-table {
    margin-top: 10px;
    width: 100%;
    font-size: 15px;
}

#rpt-table thead {
    text-align: center;
}

#rpt-table thead tr th {
    border: 1px solid #dcdde1;
    font-size: 13px;
    padding: 5px;
}

#rpt-table tbody tr td {
    text-align: center;
    font-weight: bold;
    font-size: 12px;
    border-bottom: 1px solid #dcdde1;
}


#rpt-table tfoot tr {
    padding-top: 5px;
}

#rpt-table tfoot tr th {
    border-top: 3px dashed #b2bec3;
    padding: 5px;
    font-size: 18px;
    font-weight: bold;
    color:#636e72;
}

#add-new, #add-details, #payment-button {
    padding: 10px;
    font-size: 13px;
    border: none;
    border-radius: 5px;
    background: #00b894;
    color: #fff;
    font-weight: 600;
}

#payment-button {
    background: #1e90ff;
}

#warning {
    font-size: 18px;
    color: red;
    text-align: center;
}

#checkbox {
    width: 20px;
    height: 20px;
}

#pin, #arp {
    font-weight: bold;
    text-align: center;
    text-transform: uppercase;
}


</style>
