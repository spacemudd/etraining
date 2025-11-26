<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>تقرير الحضور الفردي - التصميم الملكي</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        body {
            font-family: 'Arial', 'Tahoma', sans-serif;
            background: #0C2D66;
            color: #FFFFFF;
            line-height: 1.4;
            padding: 20px;
        }
        
        .container {
            background: #0C2D66;
            border: 3px solid #E2C044;
            border-radius: 12px;
            padding: 25px;
            margin: 0 auto;
            max-width: 1400px;
            box-shadow: 0 8px 24px rgba(12, 45, 102, 0.5);
        }
        
        .header {
            text-align: center;
            background: linear-gradient(135deg, #0C2D66 0%, #1a4a8a 100%);
            color: #E2C044;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 3px solid #E2C044;
            box-shadow: 0 4px 12px rgba(12, 45, 102, 0.4);
            position: relative;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #E2C044 0%, #f4d76e 50%, #E2C044 100%);
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 8px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            color: #FFFFFF !important;
        }
        
        .header p {
            font-size: 16px;
            color: #FFFFFF !important;
            font-weight: 300;
        }
        
        .employee-card {
            background: linear-gradient(135deg, #0C2D66 0%, #1a4a8a 100%);
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 2px solid #E2C044;
            box-shadow: 0 4px 8px rgba(226, 192, 68, 0.3);
            position: relative;
        }
        
        .employee-card::before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 5px;
            background: linear-gradient(180deg, #E2C044 0%, #f4d76e 100%);
        }
        
        .employee-name {
            font-size: 26px;
            font-weight: bold;
            color: #FFFFFF !important;
            margin-bottom: 15px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .employee-details {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        
        .employee-detail {
            background: rgba(26, 74, 138, 0.5);
            padding: 12px 15px;
            border-radius: 6px;
            font-size: 14px;
            border: 2px solid #E2C044;
            color: #FFFFFF !important;
            font-weight: 500;
        }
        
        .company-info {
            background: linear-gradient(135deg, #0C2D66 0%, #1a4a8a 100%);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 2px solid #E2C044;
            box-shadow: 0 4px 8px rgba(226, 192, 68, 0.3);
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #FFFFFF !important;
            margin-bottom: 8px;
        }
        
        .company-subtitle {
            color: #E2C044 !important;
            font-size: 16px;
            font-weight: 500;
        }
        
        .report-details {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .detail-card {
            background: linear-gradient(135deg, #0C2D66 0%, #1a4a8a 100%);
            border: 2px solid #E2C044;
            padding: 18px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 3px 8px rgba(226, 192, 68, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .detail-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #E2C044 0%, #f4d76e 50%, #E2C044 100%);
        }
        
        .detail-label {
            font-weight: bold;
            color: #FFFFFF !important;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-value {
            font-size: 20px;
            font-weight: bold;
            color: #E2C044 !important;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            border: 2px solid #E2C044;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(12, 45, 102, 0.2);
        }
        
        .attendance-table th {
            background: linear-gradient(135deg, #0C2D66 0%, #1a4a8a 100%) !important;
            color: #E2C044 !important;
            padding: 14px 10px;
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            border: 1px solid #E2C044;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .attendance-table td {
            padding: 14px 10px;
            text-align: center;
            border: 1px solid #E2C044;
            background: #1a4a8a;
            font-size: 13px;
            color: #FFFFFF !important;
        }
        
        .attendance-table tr:nth-child(even) td {
            background: #0C2D66;
        }
        
        .attendance-mark {
            display: inline-block;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            line-height: 28px;
            font-weight: bold;
            font-size: 13px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            border: 1px solid rgba(12, 45, 102, 0.3);
        }
        
        .present {
            background: #E2C044 !important;
            color: #0C2D66 !important;
            font-weight: bold;
        }
        
        .absent {
            background: #c0392b !important;
            color: #ffffff !important;
        }
        
        .vacation {
            background: #d68910 !important;
            color: #ffffff !important;
        }
        
        .day-header {
            background: linear-gradient(135deg, #0C2D66 0%, #1a4a8a 100%) !important;
            color: #E2C044 !important;
            border: 1px solid #E2C044 !important;
        }
        
        .vacation-day {
            background: #8b6914 !important;
            color: #E2C044 !important;
            border: 1px solid #E2C044 !important;
        }
        
        .summary-section {
            margin-top: 25px;
            background: linear-gradient(135deg, #0C2D66 0%, #1a4a8a 100%);
            padding: 25px;
            border-radius: 8px;
            border: 2px solid #E2C044;
            box-shadow: 0 4px 8px rgba(226, 192, 68, 0.3);
        }
        
        .summary-title {
            font-size: 20px;
            font-weight: bold;
            color: #FFFFFF !important;
            margin-bottom: 20px;
            text-align: center;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        
        .stat-item {
            background: rgba(26, 74, 138, 0.5);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border: 2px solid #E2C044;
            box-shadow: 0 3px 6px rgba(226, 192, 68, 0.2);
        }
        
        .stat-number {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .stat-present { color: #E2C044 !important; }
        .stat-absent { color: #ff6b6b !important; }
        .stat-vacation { color: #feca57 !important; }
        .stat-total { color: #E2C044 !important; }
        
        .stat-label {
            font-size: 13px;
            color: #FFFFFF !important;
            font-weight: bold;
        }
        
        .logo {
            max-width: 80px;
            height: auto;
            border-radius: 6px;
            border: 2px solid #E2C044;
            box-shadow: 0 3px 6px rgba(12, 45, 102, 0.2);
        }
        
        .footer {
            margin-top: 25px;
            text-align: center;
            padding: 18px;
            background: linear-gradient(135deg, #0C2D66 0%, #1a4a8a 100%);
            border-radius: 8px;
            border-top: 3px solid #E2C044;
        }
        
        .footer p {
            color: #FFFFFF !important;
            font-size: 12px;
            margin: 0;
            font-weight: 500;
        }
        
        /* تحسينات الطباعة */
        @media print {
            body {
                background: #0C2D66;
                padding: 0;
            }
            
            .container {
                box-shadow: none;
                border: 2px solid #E2C044;
                margin: 0;
                padding: 15px;
                background: #0C2D66 !important;
            }
            
            .present, .absent, .vacation {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .header, .employee-card, .company-info, .detail-card, .summary-section, .footer {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .attendance-table th {
                background: linear-gradient(135deg, #0C2D66 0%, #1a4a8a 100%) !important;
                color: #E2C044 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .attendance-table td {
                background: #1a4a8a !important;
                color: #FFFFFF !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .attendance-table tr:nth-child(even) td {
                background: #0C2D66 !important;
                color: #FFFFFF !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
        
        /* أعمدة الجدول */
        .col-day { width: 60px; }
        .col-date { width: 100px; }
        .col-day-name { width: 100px; }
        .col-status { width: 80px; }
        .col-notes { width: 150px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>تقرير الحضور الفردي</h1>
            <p>Individual Attendance Report - Royal Design</p>
        </div>
        
        <!-- Employee Card -->
        <div class="employee-card">
            <div class="employee-name">{{ $record->trainee->name }}</div>
            <div class="employee-details">
                <div class="employee-detail">
                    <strong>الهوية:</strong> {{ $record->trainee->clean_identity_number }}
                </div>
                @if ($record->trainee->job_number)
                <div class="employee-detail">
                    <strong>الرقم الوظيفي:</strong> {{ $record->trainee->job_number }}
                </div>
                @endif
                <div class="employee-detail">
                    <strong>المعرف:</strong> {{ $record->trainee->id }}
                </div>
            </div>
        </div>
        
        <!-- Company Info -->
        <div class="company-info">
            <div class="company-name">{{ $record->company->name_ar }}</div>
            <div class="company-subtitle">{{ $record->company->name_en }}</div>
            @if ($base64logo)
                <img src="{{ $base64logo }}" alt="Company Logo" class="logo" style="float: left; margin-top: -40px;">
            @endif
        </div>
        
        <!-- Report Details -->
        <div class="report-details">
            <div class="detail-card">
                <div class="detail-label">من تاريخ</div>
                <div class="detail-value">{{ $report->date_from->format('Y/m/d') }}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">إلى تاريخ</div>
                <div class="detail-value">{{ $report->date_to->format('Y/m/d') }}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">إجمالي الأيام</div>
                <div class="detail-value">{{ count($days) }}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">أيام العمل</div>
                <div class="detail-value">
                    @php
                        $workDays = 0;
                        foreach ($days as $day) {
                            if ($record->start_date) {
                                if ($day['date_carbon']->isBetween($record->start_date, $record->end_date)) {
                                    $workDays++;
                                }
                            } else {
                                $workDays++;
                            }
                        }
                    @endphp
                    {{ $workDays }}
                </div>
            </div>
        </div>
        
        <!-- Attendance Table -->
        <table class="attendance-table">
            <thead>
                <tr>
                    <th class="col-day">اليوم</th>
                    <th class="col-date">التاريخ</th>
                    <th class="col-day-name">اسم اليوم</th>
                    <th class="col-status">الحالة</th>
                    <th class="col-notes">ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @php $dayNumber = 1; @endphp
                @foreach ($days as $day)
                    <tr>
                        <td class="col-day"><strong>{{ $dayNumber++ }}</strong></td>
                        <td class="col-date">{{ $day['date'] }}</td>
                        <td class="col-day-name {{ $day['vacation_day'] ? 'vacation-day' : 'day-header' }}">
                            {{ $day['name'] }}
                        </td>
                        <td class="col-status">
                            @if ($day['vacation_day'])
                                @if ($record->start_date && $day['date_carbon']->isAfter($record->end_date))
                                    <span style="color: #E2C044;">-</span>
                                @else
                                    <span class="attendance-mark vacation">X</span>
                                @endif
                            @else
                                @if ($record->start_date)
                                    @if ($day['date_carbon']->isBetween($record->start_date, $record->end_date))
                                        <span class="attendance-mark present">✓</span>
                                    @else
                                        @if ($record->status === 'new_registration')
                                            <span class="attendance-mark absent">✗</span>
                                        @else
                                            <span style="color: #E2C044;">-</span>
                                        @endif
                                    @endif
                                @else
                                    <span class="attendance-mark present">✓</span>
                                @endif
                            @endif
                        </td>
                        <td class="col-notes">
                            @if ($day['vacation_day'])
                                @if ($record->start_date && $day['date_carbon']->isAfter($record->end_date))
                                    <span style="color: #E2C044;">بعد الاستقالة</span>
                                @else
                                    <span style="color: #feca57; font-weight: bold;">عطلة أسبوعية</span>
                                @endif
                            @else
                                @if ($record->start_date)
                                    @if ($day['date_carbon']->isBetween($record->start_date, $record->end_date))
                                        <span style="color: #E2C044; font-weight: bold;">حضور</span>
                                    @else
                                        @if ($record->status === 'new_registration')
                                            <span style="color: #ff6b6b; font-weight: bold;">غياب</span>
                                        @else
                                            <span style="color: #E2C044;">قبل الالتحاق</span>
                                        @endif
                                    @endif
                                @else
                                    <span style="color: #E2C044; font-weight: bold;">حضور</span>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-title">ملخص الحضور والانصراف</div>
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-number stat-present">
                        @php
                            $presentDays = 0;
                            foreach ($days as $day) {
                                if (!$day['vacation_day']) {
                                    if ($record->start_date) {
                                        if ($day['date_carbon']->isBetween($record->start_date, $record->end_date)) {
                                            $presentDays++;
                                        }
                                    } else {
                                        $presentDays++;
                                    }
                                }
                            }
                        @endphp
                        {{ $presentDays }}
                    </div>
                    <div class="stat-label">أيام الحضور</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number stat-absent">0</div>
                    <div class="stat-label">أيام الغياب</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number stat-vacation">
                        @php
                            $vacationDays = 0;
                            foreach ($days as $day) {
                                if ($day['vacation_day']) {
                                    if ($record->start_date) {
                                        if ($day['date_carbon']->isBetween($record->start_date, $record->end_date)) {
                                            $vacationDays++;
                                        }
                                    } else {
                                        $vacationDays++;
                                    }
                                }
                            }
                        @endphp
                        {{ $vacationDays }}
                    </div>
                    <div class="stat-label">أيام العطل</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number stat-total">{{ count($days) }}</div>
                    <div class="stat-label">إجمالي الأيام</div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>تقرير فردي - تم إنشاؤه تلقائياً في {{ now()->format('Y-m-d H:i:s') }} - التصميم الملكي</p>
        </div>
    </div>
</body>
</html>

