<template>
    <div>
        <div class="card">
            <div class="card-body p-4">

                <loading
                    :active.sync="isLoading"
                    :is-full-page="fullPage">
                </loading>

                <div class="button-container">
                    <div class="row">
                        <div class="col-12 pt-3 pb-3">
                            <span style="font-size: 18px; font-weight: 600">Checkout</span>
                        </div>
<!--                        <div class="col-6 pb-2">-->
<!--                            <button id="return" v-on:click="returnProfile"><i class="fas fa-chevron-left mr-2"></i> Return to Profile</button>-->
<!--                        </div>-->
<!--                        <div class="col-6 pb-2 text-right">-->
<!--                            <button id="checkout" v-on:click="checkOut">Proceed to Checkout</button>-->
<!--                        </div>-->
                    </div>
                </div>

                <div class="checkout-box border-bottom pb-2">
                    <div class="row">
                        <div class="col-6">
                            Select Payment Method <br>
                            <span style="font-size: 12px">All transaction are secured.</span>
                        </div>
                        <div class="col-6 d-flex justify-content-end align-items-center">
                            <div class="p-2 m-1 d-inline-flex align-items-center">
                                <label for="paymaya" style="margin: 0 10px 0 0"><img src="public/image/paymaya_btn.png"></label>
                                <input name="payment_type" type="radio" id="paymaya" value="paymaya" v-model="paymentMethod" v-on:change="checkoutServiceFees">
                            </div>
<!--                            <div class="p-2 d-inline-flex align-items-center">-->
<!--                                <label for="gcash" style="margin: 0 10px 0 0"><img src="public/image/gcash_btn.png"></label>-->
<!--                                <input name="payment_type" type="radio" id="gcash" value="gcash" v-model="paymentMethod" v-on:change="checkoutServiceFees">-->
<!--                            </div>-->
                        </div>
                    </div>
                </div>


                <div class="checkout-details">
                    <div class="row">
                        <div class="col-12 col-sm-6 p-0">
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
                                        </div>
                                        <div class="card-info col-4">
                                            <label>Last name</label>
                                            <input id="last_name" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.last_name">
                                            <i class="icon fas fa-user"></i>
                                            <i class="err fas fa-exclamation-circle"></i>
                                        </div>

                                        <div class="card-info col-4">
                                            <label>First name</label>
                                            <input id="first_name" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.first_name">
                                            <i class="icon fas fa-user"></i>
                                            <i class="err fas fa-exclamation-circle"></i>
                                        </div>

                                        <div class="card-info col-4">
                                            <label>Middle name</label>
                                            <input id="middle_name" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.middle_name">
                                            <i class="icon fas fa-user"></i>
                                            <i class="err fas fa-exclamation-circle"></i>
                                        </div>

                                        <div class="card-info col-12">
                                            <label>Mobile Number / GCash Number</label>
                                            <input id="mobile" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.mobile">
                                            <i class="icon fas fa-phone-alt"></i>
                                            <i class="err fas fa-exclamation-circle"></i>
                                        </div>

                                        <div class="card-info col-12">
                                            <label>Billing Address</label>
                                            <input id="address" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.address">
                                            <i class="icon fas fa-home"></i>
                                            <i class="err fas fa-exclamation-circle"></i>
                                        </div>

                                        <div class="card-info col-12">
                                            <label>City</label>
                                            <input id="city" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.city">
                                            <i class="icon fas fa-home"></i>
                                            <i class="err fas fa-exclamation-circle"></i>
                                        </div>

                                        <div class="card-info col-12">
                                            <label>State</label>
                                            <input id="state" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.state">
                                            <i class="icon fas fa-home"></i>
                                            <i class="err fas fa-exclamation-circle"></i>
                                        </div>

                                        <div class="card-info col-12">
                                            <label>Postal Code</label>
                                            <input id="postal" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.postal_code">
                                            <i class="icon fas fa-home"></i>
                                            <i class="err fas fa-exclamation-circle"></i>
                                        </div>

                                        <!--                                    <div class="line"></div>-->

                                        <!--                                    &lt;!&ndash;                   card details                     &ndash;&gt;-->
                                        <!--                                    <div class="card-info col-6">-->
                                        <!--                                        <label>Card Number</label>-->
                                        <!--                                        <the-mask :mask="['#### #### #### ####']" id="card-no" type="text" class="form-control" placeholder="xxxx xxxx xxxx xxxx" v-model="payment.card_no"/>-->
                                        <!--                                        <i class="icon fa fa-credit-card"></i>-->
                                        <!--                                        <i class="err fas fa-exclamation-circle"></i>-->
                                        <!--                                    </div>-->

                                        <!--                                    <div class="card-info col-3">-->
                                        <!--                                        <label>Expiry Date</label>-->
                                        <!--                                        <the-mask :mask="['##/##']" id="exp" type="text" class="form-control" placeholder="00/00" v-model="payment.exp_date"/>-->
                                        <!--                                        <i class="icon far fa-calendar-alt"></i>-->
                                        <!--                                        <i class="err fas fa-exclamation-circle"></i>-->
                                        <!--                                    </div>-->
                                        <!--                                    <div class="card-info col-3">-->
                                        <!--                                        <label>CVC/CVV</label>-->
                                        <!--                                        <the-mask :mask="['###']" type="text" id="cvc" class="form-control" maxlength="3" v-model="payment.cvc"/>-->
                                        <!--                                        <i class="icon fas fa-lock"></i>-->
                                        <!--                                        <i class="err fas fa-exclamation-circle"></i>-->
                                        <!--                                    </div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 p-0">
                            <div class="payment-breakdown p-3 m-1 rounded" style="background: whitesmoke; height: 100%;">
                                <span>Payment Breakdown</span>
                                <table class="mt-2" style="width: 100%; font-size: 12px">
                                    <thead class="border-bottom">
                                        <tr>
                                            <th class="text-left p-2">Bill Number</th>
                                            <th class="text-right p-2">Payment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in this.rptTableData">
                                            <td class="text-left p-2">{{ item.bill_num }}</td>
                                            <td class="text-right p-2">₱ {{ formatPrice(item.amount) }}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td class="p-2">Subtotal</td>
                                            <td class="text-right p-2">₱ {{ formatPrice(subTotal) }}</td>
                                        </tr>
                                        <tr class="">
                                            <td class="p-2">Transfer fee</td>
                                            <td class="text-right p-2">₱ {{ formatPrice(serviceFee) }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr style="border-top: 1px solid #7f8c8d">
                                            <th class="p-2">AMOUNT TOTAL:</th>
                                            <th class="text-right p-2">₱ {{ formatPrice(grandTotal) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>

                                <div class="row mt-2">
                                    <div class="col-sm-12 col-md-6">
                                        <button id="proceed-payment" v-on:click="confirmDetails" class="form-control"><i class="fas fa-check mr-2"></i>Confirm Payment</button>
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <button id="cancel" v-on:click="cancelPayment" class="form-control"><i class="fas fa-chevron-left mr-2"></i>Back</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!--                        <tr v-for="(item, index) in rptTableData">-->

        <!--                            <td style="width: 15%; vertical-align: top" class="text-left p-1">-->
        <!--                                {{ index }}-->
        <!--                            </td>-->

        <!--                            <td colspan="5" class="p-1">-->
        <!--                                <table style="width: 100%">-->
        <!--                                    <tr v-for="data in rptTableData[index]">-->
        <!--                                        <td style="width: 15%" class="text-left pt-1">{{ data.trnx_date }}</td>-->
        <!--                                        <td style="width: 15%" class="text-left pt-1">{{ data.pin }}</td>-->
        <!--                                        <td style="width: 15%" class="text-left pt-1">{{ data.arp }}</td>-->
        <!--                                        <td style="width: 15%" class="text-left pt-1">{{ data.yr1 }}</td>-->
        <!--                                        <td style="width: 20%" class="text-left pt-1">{{ data.ln_amnt }}</td>-->
        <!--                                    </tr>-->
        <!--                                </table>-->
        <!--                            </td>-->

        <!--                            <td style="width: 8%" class="text-left p-1">-->

        <!--                            </td>-->
        <!--                        </tr>-->



        <!--                            <td>{{ item.bill_num }}</td>-->
        <!--                            <td>{{ item.pin }}</td>-->
        <!--                            <td>{{ item.arp }}</td>-->
        <!--                            <td class="text-right">{{ formatPrice(item.total) }}</td>-->
        <!--&lt;!&ndash;                            <td class="text-right">&ndash;&gt;-->
        <!--&lt;!&ndash;                                <input type="number" id="amount-to-pay" @keyup="validateAmountToPay($event, index, item.total)" :value="item.amount" class="form-control text-right" disabled="disabled">&ndash;&gt;-->
        <!--&lt;!&ndash;&lt;!&ndash;                                <input v-else type="number" id="amount-to-pay" @keyup="validateAmountToPay($event, index, item.total)" :value="item.amount" class="form-control text-right" disabled="disabled">&ndash;&gt;&ndash;&gt;-->
        <!--&lt;!&ndash;                                <span class="err_amount"></span>&ndash;&gt;-->
        <!--&lt;!&ndash;                            </td>&ndash;&gt;-->
        <!--                            <td class="text-center">-->
        <!--                                <input id="checkbox" type="checkbox" v-on:change="checkPayment(index, $event)">-->
        <!--&lt;!&ndash;                                <input v-else id="checkbox" type="checkbox" v-on:change="checkPayment(index, $event)">&ndash;&gt;-->
        <!--                            </td>-->

<!--        <div class="card mb-3 payment-details">-->
<!--            <div class="card-header">-->
<!--                <div class="row align-items-center">-->
<!--                    <div class="col-6">-->
<!--                        Select Payment Method-->
<!--                    </div>-->
<!--                    <div class="col-6">-->
<!--                        <select id="payment-method" v-model="paymentMethod" class="form-control">-->
<!--                            <option value="card">Debit/Credit Card</option>-->
<!--                            <option value="grab_pay">Grab Pay</option>-->
<!--                            <option value="gcash">Gcash</option>-->
<!--                        </select>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->

<!--            <div class="card-body" v-if="paymentMethod !== ''">-->
<!--                <div class="row" v-if="paymentMethod === 'card' || paymentMethod === 'gcash'">-->
<!--                    <div class="col-sm-12 col-md-6">-->
<!--                        <div class="payment-container mb-1">-->

<!--                            <div class="alert-msg">-->
<!--                                <ul v-for="err in err_msg">-->
<!--                                    <li>{{ err }}</li>-->
<!--                                </ul>-->
<!--                            </div>-->

<!--                            <div class="personal-info">-->
<!--                                <div class="row">-->
<!--                                    <div class="card-info col-12">-->
<!--                                        <label>Card Holder</label>-->
<!--                                        <input id="fullname" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.fullname">-->
<!--                                        <i class="icon fas fa-user"></i>-->
<!--                                        <i class="err fas fa-exclamation-circle"></i>-->
<!--                                    </div>-->

<!--                                    <div class="card-info col-12">-->
<!--                                        <label>Billing Address</label>-->
<!--                                        <input id="address" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.address">-->
<!--                                        <i class="icon fas fa-home"></i>-->
<!--                                        <i class="err fas fa-exclamation-circle"></i>-->
<!--                                    </div>-->

<!--                                    <div class="card-info col-4">-->
<!--                                        <label>City</label>-->
<!--                                        <input id="city" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.city">-->
<!--                                        <i class="icon fas fa-home"></i>-->
<!--                                        <i class="err fas fa-exclamation-circle"></i>-->
<!--                                    </div>-->

<!--                                    <div class="card-info col-4">-->
<!--                                        <label>State</label>-->
<!--                                        <input id="state" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.state">-->
<!--                                        <i class="icon fas fa-home"></i>-->
<!--                                        <i class="err fas fa-exclamation-circle"></i>-->
<!--                                    </div>-->

<!--                                    <div class="card-info col-4">-->
<!--                                        <label>Postal Code</label>-->
<!--                                        <input id="postal" type="text" class="form-control" style="padding-left: 40px;" v-model="payment.postal_code">-->
<!--                                        <i class="icon fas fa-home"></i>-->
<!--                                        <i class="err fas fa-exclamation-circle"></i>-->
<!--                                    </div>-->

<!--                                    &lt;!&ndash;                                    <div class="line"></div>&ndash;&gt;-->

<!--                                    &lt;!&ndash;                                    &lt;!&ndash;                   card details                     &ndash;&gt;&ndash;&gt;-->
<!--                                    &lt;!&ndash;                                    <div class="card-info col-6">&ndash;&gt;-->
<!--                                    &lt;!&ndash;                                        <label>Card Number</label>&ndash;&gt;-->
<!--                                    &lt;!&ndash;                                        <the-mask :mask="['#### #### #### ####']" id="card-no" type="text" class="form-control" placeholder="xxxx xxxx xxxx xxxx" v-model="payment.card_no"/>&ndash;&gt;-->
<!--                                    &lt;!&ndash;                                        <i class="icon fa fa-credit-card"></i>&ndash;&gt;-->
<!--                                    &lt;!&ndash;                                        <i class="err fas fa-exclamation-circle"></i>&ndash;&gt;-->
<!--                                    &lt;!&ndash;                                    </div>&ndash;&gt;-->

<!--                                    &lt;!&ndash;                                    <div class="card-info col-3">&ndash;&gt;-->
<!--                                    &lt;!&ndash;                                        <label>Expiry Date</label>&ndash;&gt;-->
<!--                                    &lt;!&ndash;                                        <the-mask :mask="['##/##']" id="exp" type="text" class="form-control" placeholder="00/00" v-model="payment.exp_date"/>&ndash;&gt;-->
<!--                                    &lt;!&ndash;                                        <i class="icon far fa-calendar-alt"></i>&ndash;&gt;-->
<!--                                    &lt;!&ndash;                                        <i class="err fas fa-exclamation-circle"></i>&ndash;&gt;-->
<!--                                    &lt;!&ndash;                                    </div>&ndash;&gt;-->
<!--                                    &lt;!&ndash;                                    <div class="card-info col-3">&ndash;&gt;-->
<!--                                    &lt;!&ndash;                                        <label>CVC/CVV</label>&ndash;&gt;-->
<!--                                    &lt;!&ndash;                                        <the-mask :mask="['###']" type="text" id="cvc" class="form-control" maxlength="3" v-model="payment.cvc"/>&ndash;&gt;-->
<!--                                    &lt;!&ndash;                                        <i class="icon fas fa-lock"></i>&ndash;&gt;-->
<!--                                    &lt;!&ndash;                                        <i class="err fas fa-exclamation-circle"></i>&ndash;&gt;-->
<!--                                    &lt;!&ndash;                                    </div>&ndash;&gt;-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->

<!--                    <div class="col-sm-12 col-md-6">-->
<!--                        <div class="p-3 mb-2 mt-3 rounded" style="border: 2px dashed #dfe6e9; background: whitesmoke">-->
<!--                            <span>Payment Breakdown</span>-->
<!--                            <table class="rpt-breakdown" style="width: 100%">-->
<!--                                <thead>-->
<!--                                <tr>-->
<!--                                    <th>PIN</th>-->
<!--                                    <th>ARP</th>-->
<!--                                    <th>BILL#</th>-->
<!--                                    <th style="text-align: right">PAYMENT</th>-->
<!--                                </tr>-->
<!--                                </thead>-->
<!--                                <tbody>-->
<!--                                <tr v-for="data in rptTableData">-->
<!--                                    <td v-if="data.amount !== 0">{{ data.pin }}</td>-->
<!--                                    <td v-if="data.amount !== 0">{{ data.arp }}</td>-->
<!--                                    <td v-if="data.amount !== 0">{{ data.bill_num }}</td>-->
<!--                                    <td style="text-align: right" v-if="data.amount !== 0">{{ formatPrice(data.amount) }}</td>-->
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

<!--                        <div class="row">-->
<!--                            <div class="col-sm-12 col-md-6">-->
<!--                                <button id="proceed-payment" v-if="paymentMethod !== ''" v-on:click="confirmDetails" class="form-control"><i class="fas fa-check mr-2"></i>Confirm Payment</button>-->
<!--                            </div>-->
<!--                            <div class="col-sm-12 col-md-6">-->
<!--                                <button id="cancel" v-if="paymentMethod !== ''" v-on:click="cancelPayment" class="form-control"><i class="fas fa-times mr-2"></i>Cancel Payment</button>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->

<!--            <div class="card-footer">-->

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
            subTotal: 0,
            serviceFee: 0,
            grandTotal: 0,
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

        checkoutServiceFees()
        {
            if(this.paymentMethod === 'paymaya')
            {
                /* 3% + 10pesos service charge in paymaya */
                this.serviceFee = this.subTotal * 0.03;
                this.serviceFee = this.serviceFee + 10;
            }

            if(this.paymentMethod === 'gcash')
            {
                /* 2.5% service charge in gcash */
                this.serviceFee = this.subTotal  * 0.025 ;
            }

            this.grandTotal = this.subTotal + this.serviceFee;
        },

        computeTotals(index) {
            let amountArr = [];
            Object.entries(this.rptTableData[index]).forEach(([key, val]) => { amountArr.push(val.ln_amnt); });
            const total = amountArr.reduce(function(total, number) { return total + parseFloat(number); }, 0);
            return total;
        },

        intialData() {
            this.isLoading = true;
            axios.get('checkout/initial_data').then(response => {
                this.rptTableData = response.data.checkout;
                this.subTotal = response.data.sub_total;
                this.payment.mobile = response.data.mobile;
                this.payment.first_name = response.data.first_name;
                this.payment.last_name = response.data.last_name;
                this.payment.middle_name = response.data.middle_name;
                // this.serviceFee = response.data.service_charge;
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

            // $('.payment-details').show();

            window.location.href = 'checkout';
        },

        confirmDetails() {
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
                // else
                // {
                //     // if($(this).attr('id') === 'card-no' && $(this).val().length < 16)
                //     // {
                //     //     $(this).addClass('invalid');
                //     //     $(this).siblings('.err').show();
                //     //     message.push('Invalid card number format, Must be 16 digits');
                //     // }
                //     //
                //     // if($(this).attr('id') === 'exp' && $(this).val().length < 4)
                //     // {
                //     //     $(this).addClass('invalid');
                //     //     $(this).siblings('.err').show();
                //     //     message.push('Invalid card expiry date');
                //     // }
                //     //
                //     // if($(this).attr('id') === 'cvc' && $(this).val().length < 3)
                //     // {
                //     //     $(this).addClass('invalid');
                //     //     $(this).siblings('.err').show();
                //     //     message.push('Invalid CVC/CVV');
                //     // }
                // }
            });

            if(no_input)
            {
                message.push('Input is required!');
            }

            if(this.paymentMethod === '')
            {
                message.push('Payment method is required');
            }

            // if(this.payment.exp_date !== null)
            // {
            //     // const exp = this.payment.exp_date.match(/.{1,2}/g);
            //     //
            //     // if(exp[0] > 12)
            //     // {
            //     //     $('#exp').addClass('invalid');
            //     //     $('#exp').siblings('.err').show();
            //     //     message.push('Invalid Month Format!');
            //     // }
            // }

            if(message.length !== 0)
            {
                $('.alert-msg').show();
                this.err_msg = message;
                return;
            }

            this.confirmPayment();
        },

        checkPayment(index, e) {

            let total = this.computeTotals(index);

            if(e.target.checked)
            {
                this.totalAmount += total;
            }
            else
            {
                this.totalAmount -= total;
            }

            this.isLoading = true;

            axios.post('payment/add_checkout', {
                bill_num: index,
                amount: e.target.checked ? total : 0
            })
            .then(response => {
                console.log(response.status);
            })
            .finally(() => {
                this.isLoading = false
            });

        },

        validateAmountToPay(e, index, amount) {
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

        reComputeTotalAmount() {
            this.totalAmount = 0
            this.rptTableData.forEach((item) => { this.totalAmount += parseFloat(item.amount); });
        },

        confirmPayment() {
            this.isLoading = true;
            let route = this.paymentMethod === 'paymaya' ? 'paymaya_checkout' : 'gcash';

            axios.post(route, {
                rpt: this.rptTableData,
                // payment_method: this.paymentMethod,
                first_name: this.payment.first_name,
                last_name: this.payment.last_name,
                middle_name: this.payment.middle_name,
                address: this.payment.address,
                city: this.payment.city,
                state: this.payment.state,
                postal_code: this.payment.postal_code,
                subtotal: this.subTotal,
                servicefee: this.serviceFee,
                grandtotal: this.grandTotal,
                mobile: this.payment.mobile,
            }).then(response => {
                window.location.href = response.data.checkout;
            }).catch(error => {
                this.messageBox('error', 'Failed', error.response.data.errors, 'Okay');
            }).finally(() => {
                this.isLoading = false;
            });
        },

        cancelPayment() {
            this.payment = [];
            this.paymentMethod = '';

            let urlArr = window.location.href.split('/');
            urlArr[4] = '';
            window.location.href = urlArr.join('/');
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

#rpt-table tbody tr:nth-child(odd) {
    background: whitesmoke;
}

/*#rpt-table thead {*/
/*    !*text-align: center;*!*/
/*}*/

/*#rpt-table thead tr {*/
/*    border: 1px solid #dcdde1;*/
/*}*/

/*#rpt-table thead tr th {*/
/*    text-align: center;*/
/*    font-size: 15px;*/
/*    padding: 10px 5px;*/
/*    color: #636e72;*/
/*}*/

/*#rpt-table tbody tr:nth-child(odd) {*/
/*    background: whitesmoke;*/
/*}*/

/*#rpt-table tbody tr td {*/
/*    text-align: center;*/
/*    font-size: 12px;*/
/*    padding: 5px 10px;*/
/*    border-bottom: 1px solid #dcdde1;*/
/*}*/

/*#rpt-table tfoot tr th {*/
/*    border-top: 3px dashed #dfe6e9;*/
/*    padding: 10px;*/
/*    font-size: 18px;*/
/*    font-weight: bold;*/
/*    color:#636e72;*/
/*}*/

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
    color: #2d3436;
    background: #b2bec3;
}

/*.payment-breakdown {*/

/*}*/


/*.payment-breakdown table {*/
/*    position: relative;*/
/*    margin: 0;*/
/*    overflow: auto;*/
/*}*/

/*.payment-breakdown table {*/
/*    border-collapse: collapse;*/
/*}*/

/*.payment-breakdown table{*/
/*    position: sticky;*/
/*    background: whitesmoke;*/
/*    top: 0;*/
/*    z-index: 1;*/
/*}*/

/*.payment-breakdown table tbody tr td {*/

/*}*/

/*.breakdown-table table tbody tr td {*/
/*    padding: 5px ;*/
/*}*/


</style>
