<template>
    <div class="card">
        <div class="card-body p-0">

            <loading
                :active.sync="isLoading"
                :is-full-page="fullPage">
            </loading>


<!--            <div class="alert-msg">-->
<!--                {{ message }}-->
<!--            </div>-->


            <div class="button-container pl-3 pr-3">
                <div class="row">
                    <div class="col-12 pt-3 pb-3">
                        <span style="font-size: 18px; font-weight: 600">RPT Information</span>
                    </div>
                    <div class="col-6 pb-2">
                        <button id="add-new" v-on:click="openModal"><i class="fas fa-plus mr-2"></i> Add new Real Property</button>
                    </div>
                    <div class="col-6 pb-2 text-right">
                        <button id="pay-now" class="btn btn-link" v-on:click="proceedToPayment">Proceed to Payment</button>
                    </div>
                </div>
            </div>


            <table id="rpt-table">
                <thead>
                    <tr>
                        <th style="width: 25%">PIN</th>
                        <th style="width: 25%">ARP</th>
                        <th style="width: 25%">BARANGAY</th>
                        <th style="width: 25%"></th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="(item, index) in rptTableData">
                        <td>{{ item.pin }}</td>
                        <td>{{ item.arp }}</td>
                        <td>{{ item.brgy_desc }}</td>
                        <td><button id="warning" class="btn p-0" v-on:click="deleteRecord(item.id)"><i class="fas fa-trash-alt mr-2"></i></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
        </div>

        <!-- add modal -->
        <div class="modal" tabindex="-1" role="dialog" id="add-rpt">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add RPT Information</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body rpt-input">
                        <div class="alert-msg">
                            <ul v-for="err in err_msg">
                                <li>{{ err }}</li>
                            </ul>
                        </div>
                        <div>
                            <div class="group col-12">
                                <label>Barangay</label>
                                <select id="barangay" v-model="barangay_value" class="form-control">
                                    <option v-for="data in barangay" v-bind:value="data.brgy_code">{{ data.brgy_desc }}</option>
                                </select>
                                <i class="icon fas fa-user"></i>
                                <i class="err fas fa-exclamation-circle"></i>
                            </div>
                            <div class="group col-12">
                                <label>PIN</label>
                                <input id="pin" type="text" v-model="pin" class="form-control mb-1">
                                <i class="icon fas fa-user"></i>
                                <i class="err fas fa-exclamation-circle"></i>
                            </div>
                            <div class="group col-12">
                                <label>ARP</label>
                                <input id="arp" type="text" v-model="arp" class="form-control mb-1">
                                <i class="icon fas fa-user"></i>
                                <i class="err fas fa-exclamation-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="add-details" v-on:click="store"><i class="fas fa-plus mr-2"></i> Save Real Property</button>
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

    // prop: [ 'message' ],

    data() {
        return {
            pin: '',
            arp: '',
            username: '',
            barangay_value: '',
            fullPage: true,
            isLoading: false,
            totalAmount: 0,
            // message: this.message,

            rptTableData: [],
            barangay: [],
            err_msg: [],
        }
    },

    components: {
        Loading
    },

    methods: {

        errorHandler(errors){
            let error_handler = [];
            $.each(errors, function(key, value) {
                error_handler.push(value);
            });

            error_handler.join();

            return error_handler;
        },

        formatPrice(value) {
            let val = (value/1).toFixed(2).replace(',', '.')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        },

        intialData() {
            this.isLoading = true;
            axios.get('profile/initial_data').then(response => {
                this.rptTableData = response.data.data;
                this.barangay = response.data.barangay;
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

        store() {
            let message = [];
            let no_input = false;
            this.err_msg = [];


            $('.rpt-input').find('input').each(function () {

                $(this).removeClass('invalid');

                if($(this).val() === '')
                {
                    $(this).addClass('invalid');
                    $(this).siblings('.err').show();
                    no_input = true;
                }
            });

            $('#barangay').removeClass('invalid');
            if($('#barangay').find(":selected").text() === '') {
                $('#barangay').addClass('invalid');
                $('#barangay').siblings('.err').show();
                message.push('Barangay is required!')
            }

            if(no_input)
            {
                message.push('Input is required!');
            }

            $('.alert-msg').hide();

            if(message.length !== 0)
            {
                $('.alert-msg').show();
                this.err_msg = message;
                return;
            }

            this.isLoading = true;

            axios.post('profile/store', {
                pin: this.pin,
                arp: this.arp,
                barangay: this.barangay_value,
            })
            .then(response => {

                if(response.status === 200)
                {
                    this.pin = '';
                    this.arp = '';
                    this.intialData();
                    $('#add-rpt').modal('hide');
                    this.messageBox('success', 'Success', response.data.message, 'Okay');
                }

            }).catch(error => {
                let err_msg = error.response.data.errors;
                this.messageBox('error', 'Error', error.response.data.errors, 'Okay');
            })
            .finally(() => this.isLoading = false);
        },


        deleteRecord(id)
        {
            this.isLoading = true;
            axios.get('profile/destroy/' + id).then(response => {
                if(response.status === 200) {
                    this.intialData();
                    this.messageBox('success', 'Success', response.data.message, 'Okay');
                }
            }).catch(error => {
                let err_msg = error.response.data.errors;
                this.messageBox('error', 'Error', this.errorHandler(err_msg), 'Okay');
            })
            .finally(() => this.isLoading = false);
        },

        proceedToPayment() {
            window.location.href = 'payment';
        }
    },

    mounted() {
        this.intialData();
    }
}
</script>

<style scoped>


    #rpt-table {
        margin-top: 10px;
        width: 100%;
        font-size: 15px;
    }

    #rpt-table thead {
        text-align: center;
    }

    #rpt-table thead tr {
        border: 1px solid #dcdde1;

    }

    #rpt-table thead tr th {
        font-size: 15px;
        padding: 10px 5px;
        color: #636e72;
    }

    #rpt-table tbody tr td {
        text-align: center;
        padding: 5px 0;
        font-size: 12px;
        border-bottom: 1px solid #dcdde1;
    }

    #rpt-table tbody tr:nth-child(odd) {
        background: whitesmoke;
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

    #add-new, #add-details, #pay-now {
        padding: 10px;
        font-size: 13px;
        border: none;
        border-radius: 5px;
        background: #01AA4F;
        color: #fff;
        font-weight: 600;
    }

    #add-new:hover, #pay-now:hover {
        background: #00D161;
    }


    #warning {
        font-size: 18px;
        color: #636e72;
        text-align: center;
    }

    #pin, #arp {
        font-weight: bold;
        text-align: center;
        text-transform: uppercase;
    }

    .modal input, select, label {
        font-size: 12px;
    }


    .alert-msg {
        width: 100%;
        padding: 10px 10px;
        border-radius: 5px;
        background: #fab1a0;
        border: 1px solid #fab1a0;
        color: #d63031;
        font-size: 12px;
        display: none;
    }

    .alert-msg ul {
        margin: 0;
        padding-left: 20px;
    }

    .invalid {
        border: 1px solid #ff7675;
    }

    .group {
        position: relative;
    }

    .group .icon, .err {
        position: absolute;
        top: 42px;
    }

    .group .icon {
        display: none;
        left: 20px;
        color: #636e72;
    }

    .group .err {
        display: none;
        right: 30px;
        color: red;
        opacity: 0.5;
    }






</style>
