<template>
    <div>
        <div class="card">
            <div class="card-body p-0">

                <loading
                    :active.sync="isLoading"
                    :is-full-page="fullPage">
                </loading>

                <div class="button-container pl-3 pr-3">
                    <div class="row">
                        <div class="col-12 pt-3 pb-3">
                            <span style="font-size: 18px; font-weight: 600">RPT Payment</span>
                        </div>
                        <div class="col-6 pb-2">
                            <button id="return" v-on:click="returnProfile"><i class="fas fa-chevron-left mr-2"></i> Return to Profile</button>
                        </div>
                        <div class="col-6 pb-2 text-right">
                            <button id="checkout" v-on:click="checkOut">Proceed to Checkout</button>
                        </div>
                    </div>
                </div>


                <table id="rpt-table">
                    <thead>
                        <tr>
                            <th style="width: 15%">PIN</th>
                            <th style="width: 15%">ARP</th>
                            <th>BILL NUM</th>
                            <th style="width: 20%" class="text-center">BALANCE</th>
                            <th style="width: 20%" class="text-center">PAYMENT</th>
                            <th style="width: 8%" class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in rptTableData">
                            <td>{{ item.pin }}</td>
                            <td>{{ item.arp }}</td>
                            <td>{{ item.bill_num }}</td>
                            <td class="text-right">{{ formatPrice(item.total) }}</td>
                            <td class="text-right">
                                <input type="number" id="amount-to-pay" @keyup="validateAmountToPay($event, index, item.total)" :value="item.amount_to_pay" class="form-control text-right" disabled="disabled">
                                <span class="err_amount"></span>
                            </td>
                            <td class="text-center"><input id="checkbox" type="checkbox" v-on:change="checkPayment(index, $event)"></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5">Total Amount</th>
                            <th style="text-align: right">{{ formatPrice(totalAmount) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card mb-3 payment-details">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-6">
                        Select Payment Method
                    </div>
                    <div class="col-6">
                        <select id="payment-method" v-model="paymentMethod" class="form-control">
                            <option value="card">Debit/Credit Card</option>
                            <option value="grab_pay">Grab Pay</option>
                            <option value="gcash">Gcash</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-body" v-if="paymentMethod !== ''">
                <div class="row" v-if="paymentMethod === 'card' || paymentMethod === 'gcash'">
                    <div class="col-sm-12 col-md-6">
                        <div class="payment-container mb-1">

                            <div class="alert-msg">
                                <ul v-for="err in err_msg">
                                    <li>{{ err }}</li>
                                </ul>
                            </div>

                            <div class="personal-info">
                                <div class="row">
                                    <div class="card-info col-12">
                                        <label>Card Holder</label>
                                        <input id="fullname" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.fullname">
                                        <i class="icon fas fa-user"></i>
                                        <i class="err fas fa-exclamation-circle"></i>
                                    </div>

                                    <div class="card-info col-12">
                                        <label>Billing Address</label>
                                        <input id="address" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.address">
                                        <i class="icon fas fa-home"></i>
                                        <i class="err fas fa-exclamation-circle"></i>
                                    </div>

                                    <div class="card-info col-4">
                                        <label>City</label>
                                        <input id="city" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.city">
                                        <i class="icon fas fa-home"></i>
                                        <i class="err fas fa-exclamation-circle"></i>
                                    </div>

                                    <div class="card-info col-4">
                                        <label>State</label>
                                        <input id="state" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.state">
                                        <i class="icon fas fa-home"></i>
                                        <i class="err fas fa-exclamation-circle"></i>
                                    </div>

                                    <div class="card-info col-4">
                                        <label>Postal Code</label>
                                        <input id="postal" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.postal_code">
                                        <i class="icon fas fa-home"></i>
                                        <i class="err fas fa-exclamation-circle"></i>
                                    </div>

                                    <div class="line"></div>

                                    <!--                   card details                     -->
                                    <div class="card-info col-6">
                                        <label>Card Number</label>
                                        <the-mask :mask="['#### #### #### ####']" id="card-no" type="text" class="form-control" placeholder="xxxx xxxx xxxx xxxx" v-model="payment.card_no"/>
                                        <i class="icon fa fa-credit-card"></i>
                                        <i class="err fas fa-exclamation-circle"></i>
                                    </div>

                                    <div class="card-info col-3">
                                        <label>Expiry Date</label>
                                        <the-mask :mask="['##/##']" id="exp" type="text" class="form-control" placeholder="00/00" v-model="payment.exp_date"/>
                                        <i class="icon far fa-calendar-alt"></i>
                                        <i class="err fas fa-exclamation-circle"></i>
                                    </div>
                                    <div class="card-info col-3">
                                        <label>CVC/CVV</label>
                                        <the-mask :mask="['###']" type="text" id="cvc" class="form-control" maxlength="3" v-model="payment.cvc"/>
                                        <i class="icon fas fa-lock"></i>
                                        <i class="err fas fa-exclamation-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <div class="p-3 mb-2 mt-3 rounded" style="border: 2px dashed #dfe6e9; background: whitesmoke">
                            <span>Payment Breakdown</span>
                                <table class="rpt-breakdown" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>PIN</th>
                                            <th>ARP</th>
                                            <th>BILL#</th>
                                            <th style="text-align: right">PAYMENT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="data in rptTableData">
                                            <td v-if="data.amount_to_pay !== 0">{{ data.pin }}</td>
                                            <td v-if="data.amount_to_pay !== 0">{{ data.arp }}</td>
                                            <td v-if="data.amount_to_pay !== 0">{{ data.bill_num }}</td>
                                            <td style="text-align: right" v-if="data.amount_to_pay !== 0">{{ formatPrice(data.amount_to_pay) }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3">TOTAL:</th>
                                            <th style="text-align: right">{{ formatPrice(totalAmount) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <button id="proceed-payment" v-if="paymentMethod !== ''" v-on:click="confirmDetails" class="form-control"><i class="fas fa-check mr-2"></i>Confirm Payment</button>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <button id="cancel" v-if="paymentMethod !== ''" v-on:click="cancelPayment" class="form-control"><i class="fas fa-times mr-2"></i>Cancel Payment</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card-footer">

            </div>
        </div>

        <!-- card detail modal -->
<!--        <div class="modal" tabindex="-1" role="dialog" id="checkout-modal">-->
<!--            <div class="modal-dialog" role="document">-->
<!--                <div class="modal-content">-->
<!--                    <div class="modal-header">-->
<!--                        <img src="public/image/paymongo.png">-->
<!--                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">-->
<!--                            <span aria-hidden="true">&times;</span>-->
<!--                        </button>-->
<!--                    </div>-->
<!--                    <div class="modal-body">-->

<!--&lt;!&ndash;                        <div class="payment-container">&ndash;&gt;-->

<!--&lt;!&ndash;                            <div class="alert-msg">&ndash;&gt;-->
<!--&lt;!&ndash;                                <ul v-for="err in err_msg">&ndash;&gt;-->
<!--&lt;!&ndash;                                    <li>{{ err }}</li>&ndash;&gt;-->
<!--&lt;!&ndash;                                </ul>&ndash;&gt;-->
<!--&lt;!&ndash;                            </div>&ndash;&gt;-->

<!--&lt;!&ndash;                            <label for="payment-method">Payment method:</label>&ndash;&gt;-->
<!--&lt;!&ndash;                            <select id="payment-method" v-model="paymentMethod" class="form-control">&ndash;&gt;-->
<!--&lt;!&ndash;                                <option value="card">Debit/Credit Card</option>&ndash;&gt;-->
<!--&lt;!&ndash;                                <option value="grab_pay">Grab Pay</option>&ndash;&gt;-->
<!--&lt;!&ndash;                                <option value="gcash">Gcash</option>&ndash;&gt;-->
<!--&lt;!&ndash;                            </select>&ndash;&gt;-->

<!--&lt;!&ndash;                            <div class="personal-info" v-if="paymentMethod !== ''">&ndash;&gt;-->
<!--&lt;!&ndash;                                <div v-if="paymentMethod === 'card' || paymentMethod === 'gcash'">&ndash;&gt;-->
<!--&lt;!&ndash;                                    <div class="row">&ndash;&gt;-->
<!--&lt;!&ndash;                                        <div class="card-info col-12">&ndash;&gt;-->
<!--&lt;!&ndash;                                            <label>Card Holder</label>&ndash;&gt;-->
<!--&lt;!&ndash;                                            <input id="fullname" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.fullname">&ndash;&gt;-->
<!--&lt;!&ndash;                                            <i class="icon fas fa-user"></i>&ndash;&gt;-->
<!--&lt;!&ndash;                                            <i class="err fas fa-exclamation-circle"></i>&ndash;&gt;-->
<!--&lt;!&ndash;                                        </div>&ndash;&gt;-->

<!--&lt;!&ndash;                                        <div class="card-info col-12">&ndash;&gt;-->
<!--&lt;!&ndash;                                            <label>Billing Address</label>&ndash;&gt;-->
<!--&lt;!&ndash;                                            <input id="address" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.address">&ndash;&gt;-->
<!--&lt;!&ndash;                                            <i class="icon fas fa-home"></i>&ndash;&gt;-->
<!--&lt;!&ndash;                                            <i class="err fas fa-exclamation-circle"></i>&ndash;&gt;-->
<!--&lt;!&ndash;                                        </div>&ndash;&gt;-->

<!--&lt;!&ndash;                                        <div class="card-info col-4">&ndash;&gt;-->
<!--&lt;!&ndash;                                            <label>City</label>&ndash;&gt;-->
<!--&lt;!&ndash;                                            <input id="city" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.city">&ndash;&gt;-->
<!--&lt;!&ndash;                                            <i class="icon fas fa-home"></i>&ndash;&gt;-->
<!--&lt;!&ndash;                                            <i class="err fas fa-exclamation-circle"></i>&ndash;&gt;-->
<!--&lt;!&ndash;                                        </div>&ndash;&gt;-->

<!--&lt;!&ndash;                                        <div class="card-info col-4">&ndash;&gt;-->
<!--&lt;!&ndash;                                            <label>State</label>&ndash;&gt;-->
<!--&lt;!&ndash;                                            <input id="state" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.state">&ndash;&gt;-->
<!--&lt;!&ndash;                                            <i class="icon fas fa-home"></i>&ndash;&gt;-->
<!--&lt;!&ndash;                                            <i class="err fas fa-exclamation-circle"></i>&ndash;&gt;-->
<!--&lt;!&ndash;                                        </div>&ndash;&gt;-->

<!--&lt;!&ndash;                                        <div class="card-info col-4">&ndash;&gt;-->
<!--&lt;!&ndash;                                            <label>Postal Code</label>&ndash;&gt;-->
<!--&lt;!&ndash;                                            <input id="postal" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.postal_code">&ndash;&gt;-->
<!--&lt;!&ndash;                                            <i class="icon fas fa-home"></i>&ndash;&gt;-->
<!--&lt;!&ndash;                                            <i class="err fas fa-exclamation-circle"></i>&ndash;&gt;-->
<!--&lt;!&ndash;                                        </div>&ndash;&gt;-->

<!--&lt;!&ndash;                                        <div class="line"></div>&ndash;&gt;-->

<!--&lt;!&ndash;                                        &lt;!&ndash;                   card details                     &ndash;&gt;&ndash;&gt;-->
<!--&lt;!&ndash;                                        <div class="card-info col-6">&ndash;&gt;-->
<!--&lt;!&ndash;                                            <label>Card Number</label>&ndash;&gt;-->
<!--&lt;!&ndash;                                            <the-mask :mask="['#### #### #### ####']" id="card-no" type="text" class="form-control" placeholder="xxxx xxxx xxxx xxxx" v-model="payment.card_no"/>&ndash;&gt;-->
<!--&lt;!&ndash;                                            <i class="icon fa fa-credit-card"></i>&ndash;&gt;-->
<!--&lt;!&ndash;                                            <i class="err fas fa-exclamation-circle"></i>&ndash;&gt;-->
<!--&lt;!&ndash;                                        </div>&ndash;&gt;-->

<!--&lt;!&ndash;                                        <div class="card-info col-3">&ndash;&gt;-->
<!--&lt;!&ndash;                                            <label>Expiry Date</label>&ndash;&gt;-->
<!--&lt;!&ndash;                                            <the-mask :mask="['##/##']" id="exp" type="text" class="form-control" placeholder="00/00" v-model="payment.exp_date"/>&ndash;&gt;-->
<!--&lt;!&ndash;                                            <i class="icon far fa-calendar-alt"></i>&ndash;&gt;-->
<!--&lt;!&ndash;                                            <i class="err fas fa-exclamation-circle"></i>&ndash;&gt;-->
<!--&lt;!&ndash;                                        </div>&ndash;&gt;-->
<!--&lt;!&ndash;                                        <div class="card-info col-3">&ndash;&gt;-->
<!--&lt;!&ndash;                                            <label>CVC/CVV</label>&ndash;&gt;-->
<!--&lt;!&ndash;                                            <the-mask :mask="['###']" type="text" id="cvc" class="form-control" maxlength="3" v-model="payment.cvc"/>&ndash;&gt;-->
<!--&lt;!&ndash;                                            <i class="icon fas fa-lock"></i>&ndash;&gt;-->
<!--&lt;!&ndash;                                            <i class="err fas fa-exclamation-circle"></i>&ndash;&gt;-->
<!--&lt;!&ndash;                                        </div>&ndash;&gt;-->
<!--&lt;!&ndash;                                    </div>&ndash;&gt;-->
<!--&lt;!&ndash;                                </div>&ndash;&gt;-->
<!--&lt;!&ndash;                                &lt;!&ndash;                                <label for="amount">Amount</label>&ndash;&gt;&ndash;&gt;-->
<!--&lt;!&ndash;                                &lt;!&ndash;                                <input class="form-control" id="amount" v-bind:value="formatPrice(totalAmount)" readonly="readonly">&ndash;&gt;&ndash;&gt;-->
<!--&lt;!&ndash;                            </div>&ndash;&gt;-->
<!--&lt;!&ndash;                        </div>&ndash;&gt;-->

<!--                    </div>-->
<!--                    <div class="modal-footer">-->
<!--                        <button v-if="paymentMethod !== ''" v-on:click="confirmDetails" class="btn btn-info form-control"><i class="fas fa-check mr-2"></i> Pay now!</button>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="modal" tabindex="-1" role="dialog" id="card-info-modal">-->
<!--            <div class="modal-dialog" role="document">-->
<!--                <div class="modal-content">-->
<!--                    <div class="modal-header">-->
<!--                        <img src="public/image/paymongo.png">-->
<!--                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">-->
<!--                            <span aria-hidden="true">&times;</span>-->
<!--                        </button>-->
<!--                    </div>-->
<!--                    <div class="modal-body">-->
<!--                        <div class="payment-preview">-->

<!--                            <table class="payment-details" style="width: 100%">-->
<!--                                <tr>-->
<!--                                    <th style="width: 30%">Card Holder</th>-->
<!--                                    <td>: {{ payment.fullname }}</td>-->
<!--                                </tr>-->
<!--                                <tr>-->
<!--                                    <th style="width: 30%">Billing Address</th>-->
<!--                                    <td>: {{ payment.address }}</td>-->
<!--                                </tr>-->
<!--                                <tr>-->
<!--                                    <th style="width: 30%">City</th>-->
<!--                                    <td>: {{ payment.city }}</td>-->
<!--                                </tr>-->
<!--                                <tr>-->
<!--                                    <th style="width: 30%">State</th>-->
<!--                                    <td>: {{ payment.state }}</td>-->
<!--                                </tr>-->
<!--                                <tr>-->
<!--                                    <th style="width: 30%">Postal Code</th>-->
<!--                                    <td>: {{ payment.postal_code }}</td>-->
<!--                                </tr>-->
<!--                                <tr>-->
<!--                                    <th style="width: 30%">Payment Method</th>-->
<!--                                    <td>: {{ paymentMethod }}</td>-->
<!--                                </tr>-->
<!--                                <tr>-->
<!--                                    <th style="width: 30%">Card Number</th>-->
<!--                                    <td>: {{ payment.card_no }}</td>-->
<!--                                </tr>-->
<!--                            </table>-->
<!--                            <table class="rpt-breakdown" style="width: 100%">-->
<!--                                <thead>-->
<!--                                <tr>-->
<!--                                    <th>PIN</th>-->
<!--                                    <th>ARP</th>-->
<!--                                    <th>BILL#</th>-->
<!--                                    <th style="text-align: right">TO PAY</th>-->
<!--                                </tr>-->
<!--                                </thead>-->
<!--                                <tbody>-->
<!--                                <tr v-for="data in rptTableData">-->
<!--                                    <td v-if="data.amount_to_pay !== 0">{{ data.pin }}</td>-->
<!--                                    <td v-if="data.amount_to_pay !== 0">{{ data.arp }}</td>-->
<!--                                    <td v-if="data.amount_to_pay !== 0">{{ data.bill_num }}</td>-->
<!--                                    <td style="text-align: right" v-if="data.amount_to_pay !== 0">{{ formatPrice(data.amount_to_pay) }}</td>-->
<!--                                </tr>-->
<!--                                </tbody>-->
<!--                                <tfoot>-->
<!--                                <tr>-->
<!--                                    <th colspan="3">TOTAL:</th>-->
<!--                                    <th style="text-align: right">{{ formatPrice(totalAmount) }}</th>-->
<!--                                </tr>-->
<!--                                </tfoot>-->
<!--                            </table>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="modal-footer">-->
<!--                        <button class="btn btn-info form-control" style="font-size: 12px;" v-on:click="confirmPayment">Confirm Payment</button>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
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
            paymentMethod: '',
            rptTableData: [],
            payment: [],
            expMonth: [],
            err_msg: [],
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
            axios.get('payment/initial_data').then(response => {
                this.rptTableData = response.data.data;
            }).finally(() => this.isLoading = false);
        },

        messageBox(type, title, text, button) {
            Swal.fire({
                type: type,
                title: title,
                text: text,
                confirmButtonText: button
            })
        },

        returnProfile()  {
            window.location.href = 'profile';
        },

        checkOut() {
            let message = '';

            $('#rpt-table tbody tr td').find('input[type=number]').each(function() {
                if($(this).hasClass('invalid'))
                {
                    message = 'Please check invalid amount to pay!';
                }
            });

            if(this.totalAmount === 0)
            {
                message = 'Total Amount Must be Greater than zero';
            }

            if(message !== '')
            {
                this.messageBox('error', 'Invalid Amount', message, 'Okay');
                return;
            }

            $('.payment-details').show();


        },

        confirmDetails()
        {
            /* validate inputs */
            let no_input = false;
            let message = [];
            this.err_msg = [];

            $('.alert-msg').hide();

            $('.personal-info').find('input').each(function() {
                $(this).removeClass('invalid');
                $(this).siblings('.err').hide();

                if($(this).val() === '')
                {
                    $(this).addClass('invalid');
                    $(this).siblings('.err').show();
                    no_input = true;
                }
                else
                {
                    if($(this).attr('id') === 'card-no' && $(this).val().length < 16)
                    {
                        $(this).addClass('invalid');
                        $(this).siblings('.err').show();
                        message.push('Invalid card number format, Must be 16 digits');
                    }

                    if($(this).attr('id') === 'exp' && $(this).val().length < 4)
                    {
                        $(this).addClass('invalid');
                        $(this).siblings('.err').show();
                        message.push('Invalid card expiry date');
                    }

                    if($(this).attr('id') === 'cvc' && $(this).val().length < 3)
                    {
                        $(this).addClass('invalid');
                        $(this).siblings('.err').show();
                        message.push('Invalid CVC/CVV');
                    }
                }
            });

            if(no_input)
            {
                message.push('Input is required!');
            }

            if(this.payment.exp_date !== null)
            {
                const exp = this.payment.exp_date.match(/.{1,2}/g);

                if(exp[0] > 12)
                {
                    $('#exp').addClass('invalid');
                    $('#exp').siblings('.err').show();
                    message.push('Invalid Month Format!');
                }
            }

            if(message.length !== 0)
            {
                $('.alert-msg').show();
                this.err_msg = message;
                return;
            }

            this.confirmPayment();
        },

        checkPayment(index, e)
        {
            let checked = e.target.checked;
            let amount_to_pay = !checked ? 0 : this.rptTableData[index]['total'];
            $('#rpt-table tbody tr:eq(' + index + ')').find('input[type=number]').attr('disabled', checked ? false : true);
            $('#rpt-table tbody tr:eq(' + index + ')').find('input[type=number]').removeClass('invalid').siblings('.err_amount').text('');
            this.rptTableData[index]['for_payment'] = checked;
            this.rptTableData[index]['amount_to_pay'] = amount_to_pay;
            this.reComputeTotalAmount();
        },

        validateAmountToPay(e, index, amount)
        {
            if(e.target.value < 100)
            {
                $('#rpt-table tbody tr:eq(' + index + ') #' + e.target.id).addClass('invalid').siblings('.err_amount').text('Must be greater than 100');
            }
            else if(e.target.value > parseFloat(amount))
            {
                $('#rpt-table tbody tr:eq(' + index + ') #' + e.target.id).addClass('invalid').siblings('.err_amount').text('Amount to pay is greater than billed amount');
            }
            else
            {
                $('#rpt-table tbody tr:eq(' + index + ') #' + e.target.id).removeClass('invalid').siblings('.err_amount').text('');
            }

            this.rptTableData[index]['amount_to_pay'] = e.target.value === '' ? 0 : e.target.value;
            this.reComputeTotalAmount();
        },

        reComputeTotalAmount()
        {
            this.totalAmount = 0
            this.rptTableData.forEach((item) => { this.totalAmount += parseFloat(item.amount_to_pay); });
        },

        confirmPayment() {

            this.isLoading = true;

            if(this.paymentMethod === 'card')
            {
                axios.post('card_payment', {
                    rpt: this.rptTableData,
                    payment_method: this.paymentMethod,
                    fullname: this.payment.fullname,
                    address: this.payment.address,
                    city: this.payment.city,
                    state: this.payment.state,
                    postal_code: this.payment.postal_code,
                    total: this.totalAmount,
                    card_no: this.payment.card_no,
                    exp_date: this.payment.exp_date,
                    cvc: this.payment.cvc
                }).then(response => {
                    this.messageBox('success', 'Transaction Success', response.data.message, 'Finish');
                    setTimeout(function () { window.location.href = 'home'; }, 1500);
                }).catch(error => {
                    this.messageBox('error', 'Failed', error.response.data.errors, 'Okay');
                }).finally(() => {
                    this.isLoading = false;
                });
            }
        },

        cancelPayment() {
            this.payment = [];
            this.paymentMethod = '';
            $('.payment-details').hide();
        }
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
        /*text-align: center;*/
    }

    #rpt-table thead tr {
        border: 1px solid #dcdde1;
    }

    #rpt-table thead tr th {
        text-align: center;
        font-size: 15px;
        padding: 10px 5px;
        color: #636e72;
    }

    #rpt-table tbody tr:nth-child(odd) {
        background: whitesmoke;
    }

    #rpt-table tbody tr td {
        text-align: center;
        font-size: 12px;
        padding: 5px 10px;
        border-bottom: 1px solid #dcdde1;
    }

    #rpt-table tfoot tr th {
        border-top: 3px dashed #dfe6e9;
        padding: 10px;
        font-size: 18px;
        font-weight: bold;
        color:#636e72;
    }

    #return, #checkout {
        padding: 10px;
        font-size: 13px;
        border: none;
        border-radius: 5px;
        background: #01AA4F;
        color: #fff;
        font-weight: 600;
    }

    #return:hover, #checkout:hover {
        background: #00D161;
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

    .payment-container {
        background: white;
        border-radius: 4px;
        padding: 5px;
    }

    /*.logo {*/
    /*    width: 100%;*/
    /*}*/

    .modal-header img {
        width: 200px;
    }

    label, select, input {
        font-size: 12px;
    }

    input {
        text-transform: uppercase;
    }

    .modal-footer button {
        font-size: 15px;
    }

    #card-no, #exp, #cvc {
        text-align: center;
    }

    .card-info {
        position: relative;
    }

    .card-info .icon, .err {
        position: absolute;
        top: 42px;
    }

    .card-info .icon {
        left: 20px;
        color: #636e72;
    }

    .card-info .err {
        display: none;
        right: 20px;
        color: red;
        opacity: 0.5;
    }


    .line {
        width: 100%;
        background: #b2bec3;
        padding: 1px;
        margin: 20px 0 10px 0;
    }

    .transact-detail-view label {
        line-height: 1;
        font-size: 15px;
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

    .err_amount {
        font-size: 10px;
        color: red;
    }

    .payment-preview {
        /*background: #f1f2f6;*/
        padding: 20px;
        border: 1px solid #ced6e0;
        border-radius: 5px;
        font-size: 13px;
    }


    /*.payment-details tr th {*/
    /*    font-weight: normal;*/
    /*}*/

    /*.payment-details tr td {*/
    /*    text-transform: uppercase;*/
    /*}*/

    /*.payment-details tr th, td {*/
    /*    padding: 5px 0;*/
    /*}*/

    .payment-details {
        display: none;
    }

    .rpt-breakdown {
        margin-top: 10px;
        width: 100%;
        border-radius: 4px;
        font-size: 12px;
    }

    .rpt-breakdown thead {
        /*background: #dfe4ea;*/
        color: #000;
    }

    .rpt-breakdown thead tr th {
        padding: 5px 10px;
    }

    .rpt-breakdown tbody tr td {
        /*border-bottom: 1px solid #000;*/
        font-size: 12px;
        padding: 5px;
    }

    .rpt-breakdown tfoot tr th {
        border-top: 1px solid #000;
        padding: 5px;
    }

    #proceed-payment, #cancel {
        font-size: 13px;
        font-weight: bold;
    }

    #proceed-payment {
        color: #fff;
        background: dodgerblue;
    }

    #cancel {
        color: #fff;
        background: #d63031;
    }

</style>
