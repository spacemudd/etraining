<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    {{--<link rel="stylesheet" href="{{ url('pdf.css') }}" media="screen">--}}
    <link rel="stylesheet" href="{{ public_path('pdf.css') }}" media="screen">
    <title>{{ $company->resource_label }}</title>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-6" style="text-align:left;">
            <img src="{{ public_path('/img/logo-lg.png')}}" alt="logo" width="200"/>
        </div>
        <div class="col-6">
            <h1>{{ __('words.account-statement') }}</h1>
        </div>
    </div>

    <div class="row">
        <table class="table">
            <colgroup>
                <col style="width:200px;">
                <col style="width:300px;">
            </colgroup>
            <tbody>
            <tr>
                <td>{{ __('words.company') }}</td>
                <td>{{ $company->resource_label }}</td>
            </tr>
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
                <td>{{ number_format($invoice_group->grand_total, 2) }}</td>
            </tr>
            </tbody>
        </table>

        <p dir="ltr" class="right">{{ __('words.print-date') }}: {{ now()->toDateTimeString() }}</p>

        <hr>

        <h3>{{ __('words.invoices') }}</h3>

        <table style="width:100%;">
            <colgroup>
                <col>
                <col style="width:200px;">
                <col style="width:200px;">
            </colgroup>
            <thead>
            <tr>
                <th>{{ __('words.name') }}</th>
                <th>{{ __('words.invoice-no') }}</th>
                <th>{{ __('words.grand-total') }}</th>
                <th>{{ __('words.is-paid') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($invoices as $invoice)
                <tr style="page-break-inside: avoid;">
                    <td>{{ optional($invoice->trainee)->name }}</td>
                    <td>{{ $invoice->number_formatted }}</td>
                    <td>{{ number_format($invoice->total_amount, 2) }}</td>
                    <td>{{ $invoice->id_paid ? __('words.paid') : __('words.not-paid') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
