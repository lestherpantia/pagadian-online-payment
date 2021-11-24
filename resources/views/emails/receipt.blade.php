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

<?php $transaction_fee = $data['transaction_fee'] ?>
<?php $total = $data['total'] ?>

<div style="padding: 50px;">
<span style="font-size: 20px;">Received From: {{ $data['name'] }}</span>
<table class="header-table" style="width: 100%; font-size: 13px">
<tr>
<th>No: {{ $data['invoice'] }}</th>
</tr>
<tr>
<th>Date | Time: {{ date('m/d/y H:i') }}</th>
</tr>
<tr>
<th>Form of Payment: {{ strtoupper($data['method']) }}</th>
</tr>
</table>

<table class="rpt_table">
<tbody>
@foreach($data['items'] as $data)
<tr>
<td>{{ $data['bill_num'] }}</td>
<td style="text-align: right;">₱ {{ number_format($data['amount'], 2) }}</td>
</tr>
@endforeach
</tbody>
<tfoot>
<tr>
<th style="text-align: right">TRANSACTION FEE:</th>
<th style="text-align: right;">₱ {{ number_format($transaction_fee, 2) }}</th>
</tr>
<tr>
<th style="text-align: right">TOTAL:</th>
<th style="text-align: right;">₱ {{ number_format($total, 2) }}</th>
</tr>
</tfoot>
<thead>
<tr>
<th>BILL NO.</th>
<th style="text-align: right;">TOTAL</th>
</tr>
</thead>

</table>

Thanks,<br>
{{ config('app.name') }}

</div>



{{--@component('mail::button', ['url' => ''])--}}
{{--Button Text--}}
{{--@endcomponent--}}


@endcomponent
