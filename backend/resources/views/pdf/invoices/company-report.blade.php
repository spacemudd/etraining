<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="stylesheet" href="{{ url('pdf.css') }}" media="screen">
    <link rel="stylesheet" href="{{ public_path('pdf.css') }}" media="screen">
    <title>{{ $company->resource_label }}</title>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-6" style="text-align:left;">
            <img src="{{ public_path('/img/ptc_invoice_logo.png')}}" alt="logo" width="200"/>
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
                <th>#</th>
                <th>{{ __('words.name') }}</th>
                <th>{{ __('words.invoice-no') }}</th>
                <th>{{ __('words.subtotal') }}</th>
                <th>{{ __('words.vat') }}</th>
                <th>{{ __('words.grand-total') }}</th>
                <th>{{ __('words.is-paid') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($invoices as $index => $invoice)
                <tr style="page-break-inside: avoid;border:1px solid black;">
                    <td style="border:1px solid black">{{ ++$index }}</td>
                    <td style="border:1px solid black">{{ optional($invoice->trainee)->name }}</td>
                    <td style="border:1px solid black">{{ $invoice->number_formatted }}</td>
                    <td style="border:1px solid black">{{ number_format($invoice->sub_total, 2) }}</td>
                    <td style="border:1px solid black">{{ number_format($invoice->tax, 2) }}</td>
                    <td style="border:1px solid black">{{ number_format($invoice->grand_total, 2) }}</td>
                    <td style="border:1px solid black">{{ $invoice->id_paid ? __('words.paid') : __('words.not-paid') }}</td>
                </tr>
            @endforeach
                <tr style="border:1px solid black;">
                    <td style="border:none;"></td>
                    <td style="border:none;"></td>
                    <td style="border:none;"></td>
                    <td style="border:1px solid black;">{{ number_format($invoices->sum('sub_total'), 2) }}</td>
                    <td style="border:1px solid black;">{{ number_format($invoices->sum('tax'), 2) }}</td>
                    <td style="border:1px solid black;">{{ number_format($invoices->sum('grand_total'), 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>



{{-- each invoice --}}


@foreach ($invoices as $invoice)
<div class="container" style="page-break-before: always;">
    <div class="row">
        <div class="col-7">
            <h1>فاتورة ضريبية</h1>
        </div>
        <div class="col-5" style="text-align:right;">
            <img src="{{ public_path('/img/logo-lg.png')}}" alt="logo" width="200"/>
        </div>
    </div>

    <div class="row">
        <table class="table" style="width:100%;margin-top:5rem;">
            <colgroup>
                <col style="width:140px;">
                <col style="width:400px;">
                <col style="width:100px;">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <td style="background-color:#dcdcdc;padding:0 10px;">{{ __('words.company') }}</td>
                <td style="padding:0 10px;">شركة مركز احترافية التدريب</td>
                <td style="background-color:#dcdcdc;padding:0 10px;">العميل</td>
                <td style="padding:0 10px;">
                    {{ $invoice->trainee->name }} - ({{ $invoice->trainee->company->name_ar }})
                </td>
            </tr>
            <tr>
                <td style="background-color:#dcdcdc;padding:0 10px;">العنوان</td>
                <td style="padding:0 10px;">المملكة العربية السعودية - الرياض - حي اليرموك - شارع الصحابة</td>
                <td style="background-color:#dcdcdc;padding:0 10px;">رقم الفاتورة</td>
                <td style="padding:0 10px;">
                    {{ $invoice->number_formatted }}
                </td>
            </tr>
            <tr>
                <td style="background-color:#dcdcdc;padding:0 10px;">رقم الضريبي</td>
                <td style="padding:0 10px;">310172915800003</td>
                <td style="background-color:#dcdcdc;padding:0 10px;">تاريخ الفاتورة</td>
                <td style="padding:0 10px;">
                    {{ $invoice->created_at->format('d-m-Y') }}
                </td>
            </tr>
            <tr>
                <td style="background-color:#dcdcdc;padding:0 10px;">رقم السجل التجاري</td>
                <td style="padding:0 10px;">1010569056</td>
            </tr>
            <tr>
                <td style="background-color:#dcdcdc;padding:0 10px;">للتواصل</td>
                <td dir="ltr" style="text-align:right;padding:0 10px;">11 454 0747</td>
            </tr>
            </tbody>
        </table>

        <hr>

        <table style="width:100%;margin-top:5rem;">
            <colgroup>
                <col style="width:10px;">
                <col style="width:400px;">
                <col>
            </colgroup>
            <thead>
            <tr>
                <th>#</th>
                <th>{{ __('words.description') }}</th>
                <th>{{ __('words.subtotal') }}</th>
                <th>{{ __('words.vat') }}</th>
                <th>{{ __('words.grand-total') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($invoice->items as $index => $item)
                <tr>
                    <td>{{ $index+1 }}</td>
                    <th>{{ $item->name_ar }}</th>
                    <th>{{ number_format($item->sub_total, 2) }}</th>
                    <th>{{ number_format($item->tax, 2) }}</th>
                    <th>{{ number_format($item->grand_total, 2) }}</th>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col-12" style="margin-top:5rem;">
            <hr>
            <table class="table" style="width:100%;font-size: 11px;font-weight: normal">
                <colgroup>
                    <col style="width:200px;">
                    <col>
                    <col style="width:200px;">
                </colgroup>
                <tbody>
                <tr>
                    <td>اسم المستفيد:</td>
                    <td style="text-align:center;">مركز احترافية التدريب للخدمات التجارية</td>
                    <td style="text-align:left;direction:ltr;">Beneficiary name:</td>
                </tr>
                <tr>
                    <td>رقم حساب المستقيد</td>
                    <td style="text-align:center;">2972254319940</td>
                    <td style="text-align:left;direction:ltr;">Beneficiary account no.:</td>
                </tr>
                <tr>
                    <td>رقم الايبان:</td>
                    <td style="text-align:center;">SA4720000002972254319940</td>
                    <td style="text-align:left;direction:ltr;">IBAN number:</td>
                </tr>
                <tr>
                    <td>اسم البنك:</td>
                    <td style="text-align:center;">
                        بنك الرياض - Riyadh Bank
                    </td>
                    <td style="text-align:left;direction:ltr;">Bank name:</td>
                </tr>
                <tr>
                    <td>فرع البنك:</td>
                    <td style="text-align:center;">
                        الرياض - الملز - شارع الأحساء - AL IHSA Street - AL Malaz - Riyadh
                    </td>
                    <td style="text-align:left;direction:ltr;">Bank branch:</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{--<div class="row" style="margin-top:30px;">--}}
    {{--    <div class="col-4"><p style="text-align: center;font-weight: bold;font-size: 20px;">المدير العام</p></div>--}}
    {{--    <div class="col-4"><p style="text-align: center;font-weight: bold;font-size: 20px;">مسؤول المبيعات</p></div>--}}
    {{--    <div class="col-4"><p style="text-align: center;font-weight: bold;font-size: 20px;">الادارة المالية</p></div>--}}
    {{--</div>--}}
</div>
@endforeach



</body>
</html>
