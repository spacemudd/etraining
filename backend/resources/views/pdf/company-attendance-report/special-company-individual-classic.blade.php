<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>تقرير الحضور والانصراف الفردي</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            background: white;
            color: #000;
            line-height: 1.6;
            padding: 20px;
        }
        
        .container {
            background: white;
            border: 2px solid #000;
            padding: 20px;
            margin: 0 auto;
            max-width: 1400px;
        }
        
        .header {
            text-align: center;
            background: #000;
            color: white;
            padding: 25px;
            margin-bottom: 20px;
            border: 3px solid #333;
            page-break-after: avoid;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 8px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        .header p {
            font-size: 14px;
        }
        
        .company-info {
            background: #f5f5f5;
            padding: 20px;
            margin-bottom: 20px;
            border: 2px solid #333;
            border-right: 5px solid #000;
            page-break-after: avoid;
        }
        
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #000;
            margin-bottom: 8px;
            text-decoration: underline;
        }
        
        .company-subtitle {
            color: #666;
            font-size: 14px;
            font-style: italic;
        }
        
        .report-details {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
            page-break-after: avoid;
        }
        
        .detail-card {
            background: white;
            border: 2px solid #333;
            padding: 15px;
            text-align: center;
        }
        
        .detail-label {
            font-weight: bold;
            color: #000;
            font-size: 12px;
            margin-bottom: 8px;
            text-decoration: underline;
        }
        
        .detail-value {
            font-size: 18px;
            font-weight: bold;
            color: #000;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            border: 2px solid #000;
        }
        
        .attendance-table th {
            background: #000;
            color: white;
            padding: 12px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            border: 1px solid #000;
            page-break-after: avoid;
        }
        
        .attendance-table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #333;
            background: white;
            font-size: 11px;
            page-break-inside: avoid;
        }
        
        .attendance-table tr {
            page-break-inside: avoid;
            border-bottom: 1px solid #ccc;
        }
        
        .attendance-table tr:nth-child(even) td {
            background: #f9f9f9;
        }
        
        .attendance-mark {
            display: inline-block;
            width: 25px;
            height: 25px;
            border: 2px solid #333;
            line-height: 21px;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
        }
        
        .present {
            background: white;
            color: #000;
            border: 2px solid #000;
        }
        
        .absent {
            background: #666;
            color: white;
            border: 2px solid #000;
        }
        
        .vacation {
            background: #ccc;
            color: #000;
            border: 2px solid #000;
        }
        
        .vacation-day {
            background: #333 !important;
            color: white !important;
        }
        
        .logo {
            max-width: 70px;
            height: auto;
            border: 2px solid #000;
        }
        
        /* تحسينات الطباعة */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            
            .container {
                border: 2px solid #000;
                margin: 0;
                padding: 15px;
                max-width: none;
            }
            
            .header {
                page-break-after: avoid;
                margin-bottom: 15px;
                background: #000 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .company-info {
                page-break-after: avoid;
                margin-bottom: 15px;
                border: 2px solid #000;
            }
            
            .report-details {
                page-break-after: avoid;
                margin-bottom: 15px;
            }
            
            .attendance-table {
                page-break-inside: auto;
                border: 2px solid #000 !important;
            }
            
            .attendance-table thead {
                display: table-header-group;
                page-break-after: avoid;
                background: #000 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .attendance-table tbody {
                display: table-row-group;
            }
            
            .attendance-table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            .attendance-table th {
                page-break-after: avoid;
                background: #000 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                border: 1px solid #000 !important;
            }
            
            .attendance-table td {
                page-break-inside: avoid;
                background: white !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                border: 1px solid #000 !important;
            }
            
            .attendance-table tr:nth-child(even) td {
                background: #f9f9f9 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .present, .absent, .vacation {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                border: 2px solid #000 !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>تقرير الحضور والانصراف - {{ $record->trainee->name }}</h1>
            <p>Individual Attendance & Absence Report</p>
        </div>
        
        <!-- Company Info -->
        <div class="company-info">
            <div class="company-name">{{ $record->company->name_ar }}</div>
            <div class="company-subtitle">{{ $record->company->name_en }}</div>
            @if ($base64logo)
                <img src="{{ $base64logo }}" alt="Company Logo" class="logo" style="float: left; margin-top: -40px; border: 2px solid #000;">
            @endif
        </div>
        
        <!-- Report Details -->
        <div class="report-details">
            <div class="detail-card">
                <div class="detail-label">رقم التقرير</div>
                <div class="detail-value">{{ str_replace('ATR-', '', $record->report->number) }}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">فترة التقرير</div>
                <div class="detail-value">{{ $record->report->date_from->format('m/d') }} - {{ $record->report->date_to->format('m/d') }}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">اسم الموظف</div>
                <div class="detail-value">{{ $record->trainee->name }}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">أيام العمل</div>
                <div class="detail-value">{{ count($days) }}</div>
            </div>
        </div>
        
        <!-- Attendance Table -->
        <table class="attendance-table">
            <thead>
                <tr>
                    <th style="width: 15%;">التاريخ</th>
                    <th style="width: 20%;">اليوم</th>
                    <th style="width: 20%;">الحضور</th>
                    <th style="width: 45%;">ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($days as $day)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($day['date'])->format('Y-m-d') }}</td>
                        <td>
                            {{ $day['name'] }}
                            @if ($day['vacation_day'])
                                <span class="vacation-day">(عطلة)</span>
                            @endif
                        </td>
                        <td>
                            @if ($day['vacation_day'])
                                <span class="attendance-mark vacation">X</span>
                                <small style="display: block; margin-top: 2px;">عطلة</small>
                            @else
                                @if ($record->start_date)
                                    @if ($day['date_carbon']->isBetween($record->start_date, $record->end_date))
                                        <span class="attendance-mark present">✓</span>
                                        <small style="display: block; margin-top: 2px;">حاضر</small>
                                    @else
                                        @if ($record->status === 'new_registration')
                                            <span class="attendance-mark absent">✗</span>
                                            <small style="display: block; margin-top: 2px;">غائب</small>
                                        @else
                                            <span style="color: #999;">-</span>
                                        @endif
                                    @endif
                                @else
                                    <span class="attendance-mark present">✓</span>
                                    <small style="display: block; margin-top: 2px;">حاضر</small>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if (!$day['vacation_day'])
                                @if ($record->start_date)
                                    @if ($day['date_carbon']->isBetween($record->start_date, $record->end_date))
                                        <span style="color: #000;">حضور منتظم</span>
                                    @else
                                        <span style="color: #666;">فترة غير نشطة</span>
                                    @endif
                                @else
                                    <span style="color: #000;">حضور منتظم</span>
                                @endif
                            @else
                                <span style="color: #666;">عطلة نهاية الأسبوع</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
    </div>
</body>
</html>

