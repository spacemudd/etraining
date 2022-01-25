<!DOCTYPE html>
<html dir="ltr">
<head>
    <meta
        http-equiv="Content-Type"
        content="text/html;charset=UTF-8"
    >
    <title>{{ $company->resource_label }}</title>
</head>
<body class="font-sans antialiased  bg-gray-50 dark:bg-gray-900">
<div>
    <h1>{{ $company->resource_label }}</h1>

    <table class="table">
        <colgroup>
            <col style="width:200px;">
        </colgroup>
        <tbody>
        <tr>
            <td>{{__('words.created-by')}}</td>
            <td>{{ optional($invoice_group->created_by)->name }}</td>
        </tr>
        <tr>
            <td>{{ __('words.date-period') }}</td>
            <td>
                {{ $invoice_group->from_date->toDateString() }}
                <br>
                {{ $invoice_group->to_date->toDateString() }}
            </td>
        </tr>
        <tr>
            <td>{{__('words.grand-total')}}</td>
            <td>{{ $invoice_group->grand_total }}</td>
        </tr>
        </tbody>
    </table>

    <hr>

    <h3>Invoices</h3>

    <table style="width:100%;">
        <colgroup>
            <col>
            <col style="width:200px;">
            <col style="width:200px;">
        </colgroup>
        <thead>
        <tr>
            <th style="text-align: left">{{ __('words.name') }}</th>
            <th>{{ __('words.invoice-no') }}</th>
            <th>{{ __('words.grand-total') }}</th>
            <th style="text-align: right">{{ __('words.is-paid') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($invoices as $invoice)
            <tr style="page-break-inside: avoid;">
                <td>{{ optional($invoice->trainee)->name }}</td>
                <td style="text-align: center;">{{ $invoice->number_formatted }}</td>
                <td style="text-align: center;">{{ $invoice->total_amount }}</td>
                <td style="text-align: right;">{{ $invoice->id_paid ? __('words.paid') : __('words.not-paid') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
