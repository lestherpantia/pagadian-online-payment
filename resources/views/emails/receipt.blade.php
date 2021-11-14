@component('mail::message')

<style>

    @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;1,300&display=swap');

    table {
        color: #000;
        font-family: 'Roboto', sans-serif;
    }

    .header-table tr th {
        text-align: left;
        font-weight: normal;
    }

    .rpt_table {
        margin: 20px 0;
        width: 100%;
        text-align: left;
        border-collapse: collapse
    }

    .rpt_table thead tr {
        border-top: 1px solid #95a5a6;
        border-bottom: 1px solid #95a5a6;
        color: #000;
        font-weight: bold;
    }

    .rpt_table tbody tr td, .rpt_table thead tr th {
        font-size: 12px;
        vertical-align: bottom;
        font-weight: normal;
    }

    .rpt_table thead tr th {
        padding: 10px 5px 2px 5px;
    }

    .rpt_table tbody tr td {
        padding: 10px;
        border-bottom: 1px solid #95a5a6;
    }


    .rpt_table tfoot tr th {
        font-size: 12px;
        font-weight: bold;
    }

    .rpt_table tbody tr:nth-child(odd) {
        background: whitesmoke;
    }

</style>

<div style="width: 100%;">
    <img style="width: 100%; height: 200px;" src="{{ asset('image/email-header.png') }}">
</div>

<div style="padding: 50px;">

<span style="font-size: 20px;">Invoice to: MARK LESTHER PANTIA</span>
<table class="header-table" style="width: 100%; font-size: 13px">
    <tr>
        <th>No: 12345</th>
    </tr>
    <tr>
        <th>Date | Time: {{ $date }}</th>
    </tr>
    <tr>
        <th>Payment Type: {{ strtoupper($trans_data->payment_method) }}</th>
    </tr>
</table>

<table class="rpt_table">
    <thead>
        <tr>
            <th>BILL NO.</th>
            <th>PIN</th>
            <th>ARP</th>
            <th style="text-align: right;">TOTAL</th>
        </tr>
    </thead>
    <tbody>
    @foreach($trans_data->rpt as $data)
        @if($data['amount_to_pay'] != 0)
            <tr>
                <td>{{ $data['bill_num'] }}</td>
                <td>{{ $data['pin'] }}</td>
                <td>{{ $data['arp'] }}</td>
                <td style="text-align: right;">₱ {{ number_format($data['amount_to_pay'], 2) }}</td>
            </tr>
        @endif
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" style="text-align: right">TRANSACTION FEE:</th>
            <th style="text-align: right;">₱ 50.00</th>
        </tr>
        <tr>
            <th colspan="3" style="text-align: right">TOTAL:</th>
            <th style="text-align: right;">₱ {{ number_format($totals, 2) }}</th>
        </tr>
    </tfoot>
</table>

Thanks,<br>
{{ config('app.name') }}

</div>



{{--@component('mail::button', ['url' => ''])--}}
{{--Button Text--}}
{{--@endcomponent--}}


@endcomponent
