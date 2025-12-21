<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>تقرير الحضور الفردي - التصميم الأنيق</title>
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
            font-family: 'Arial', 'Tahoma', 'Segoe UI', sans-serif;
            background: #f8f9fa;
            color: #212529;
            line-height: 1.6;
            padding: 20px;
        }
        
        .container {
            background: #ffffff;
            border: 2px solid #8b5cf6;
            border-radius: 12px;
            padding: 25px;
            margin: 0 auto;
            max-width: 1400px;
            box-shadow: 0 4px 20px rgba(139, 92, 246, 0.1);
        }
        
        .header {
            text-align: center;
            background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
            color: #ffffff;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(139, 92, 246, 0.2);
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 8px;
            font-weight: 700;
            color: #ffffff !important;
            letter-spacing: 0.5px;
        }
        
        .header p {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 400;
            letter-spacing: 0.3px;
        }
        
        .employee-card {
            background: #f8f9fa;
            padding: 22px;
            border-radius: 10px;
            margin-bottom: 25px;
            border: 2px solid #e9ecef;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .employee-name {
            font-size: 24px;
            font-weight: 700;
            color: #212529 !important;
            margin-bottom: 15px;
            letter-spacing: 0.3px;
        }
        
        .employee-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
        }
        
        .employee-detail {
            background: #ffffff;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            border: 1px solid #e9ecef;
            color: #212529 !important;
            font-weight: 500;
        }
        
        .company-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            border: 2px solid #e9ecef;
            text-align: center;
        }
        
        .company-name {
            font-size: 22px;
            font-weight: 700;
            color: #212529 !important;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }
        
        .company-subtitle {
            color: #6c757d !important;
            font-size: 15px;
            font-weight: 500;
        }
        
        .report-details {
            width: 100% !important;
            margin-bottom: 25px !important;
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            justify-content: space-between !important;
            align-items: stretch !important;
            gap: 15px !important;
            clear: both !important;
        }
        
        body .container .report-details,
        html body .container .report-details,
        .container .report-details {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
        }
        
        .detail-card {
            background: #ffffff !important;
            border: 2px solid #8b5cf6 !important;
            padding: 20px 15px !important;
            border-radius: 12px !important;
            text-align: center !important;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.12) !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
            position: relative !important;
            overflow: hidden !important;
            min-height: 100px !important;
            flex: 1 1 auto !important;
            flex-basis: 0 !important;
            min-width: 0 !important;
            width: auto !important;
            max-width: none !important;
            box-sizing: border-box !important;
            float: none !important;
        }
        
        body .container .report-details .detail-card,
        html body .container .report-details .detail-card,
        .container .report-details .detail-card {
            flex: 1 1 auto !important;
            flex-basis: 0 !important;
            width: auto !important;
            max-width: none !important;
            float: none !important;
            display: flex !important;
        }
        
        .detail-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #8b5cf6 0%, #a78bfa 50%, #8b5cf6 100%);
        }
        
        .detail-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: rgba(139, 92, 246, 0.1);
        }
        
        .detail-label {
            font-weight: 600;
            color: #6c757d !important;
            font-size: 11px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            line-height: 1.4;
        }
        
        .detail-value {
            font-size: 26px;
            font-weight: 700;
            color: #8b5cf6 !important;
            line-height: 1.2;
            margin-top: 4px;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            border: 2px solid #8b5cf6;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(139, 92, 246, 0.1);
        }
        
        .attendance-table th {
            background: #8b5cf6 !important;
            color: #ffffff !important;
            padding: 14px 12px;
            text-align: center;
            font-weight: 700;
            font-size: 13px;
            border: 1px solid #7c3aed;
            letter-spacing: 0.3px;
        }
        
        .attendance-table td {
            padding: 12px 10px;
            text-align: center;
            border: 1px solid #e9ecef;
            background: #ffffff;
            font-size: 13px;
            color: #212529 !important;
        }
        
        .attendance-table tr:nth-child(even) td {
            background: #f8f9fa;
        }
        
        .attendance-mark {
            display: inline-block;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            line-height: 28px;
            font-weight: 700;
            font-size: 13px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }
        
        .present {
            background: #8b5cf6 !important;
            color: #ffffff !important;
            border: 1px solid #7c3aed !important;
        }
        
        .absent {
            background: #ffffff !important;
            color: #dc3545 !important;
            border: 2px solid #dc3545 !important;
        }
        
        .attendance-mark.absent,
        span.attendance-mark.absent {
            background: #ffffff !important;
            color: #dc3545 !important;
            border: 2px solid #dc3545 !important;
        }
        
        .vacation {
            background: #ffffff !important;
            color: #f59e0b !important;
            border: 2px solid #f59e0b !important;
        }
        
        .attendance-mark.vacation,
        span.attendance-mark.vacation {
            background: #ffffff !important;
            color: #f59e0b !important;
            border: 2px solid #f59e0b !important;
        }
        
        .day-header {
            background: #8b5cf6 !important;
            color: #ffffff !important;
        }
        
        .vacation-day {
            background: #fef3c7 !important;
            color: #92400e !important;
        }
        
        .summary-section {
            margin-top: 25px;
            background: #f8f9fa;
            padding: 22px;
            border-radius: 10px;
            border: 2px solid #e9ecef;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .summary-title {
            font-size: 20px;
            font-weight: 700;
            color: #212529 !important;
            margin-bottom: 18px;
            text-align: center;
            letter-spacing: 0.3px;
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .stat-item {
            background: #ffffff;
            padding: 18px;
            border-radius: 8px;
            text-align: center;
            border: 2px solid #8b5cf6;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.1);
        }
        
        .stat-number {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .stat-present { color: #8b5cf6 !important; }
        .stat-absent { color: #dc3545 !important; }
        .stat-vacation { color: #f59e0b !important; }
        .stat-total { color: #8b5cf6 !important; }
        
        .stat-label {
            font-size: 13px;
            color: #6c757d !important;
            font-weight: 600;
        }
        
        .logo {
            max-width: 90px;
            height: auto;
            border-radius: 6px;
            border: 2px solid #8b5cf6;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.2);
        }
        
        .footer {
            margin-top: 25px;
            text-align: center;
            padding: 18px;
            background: #f8f9fa;
            border-radius: 8px;
            border-top: 2px solid #8b5cf6;
        }
        
        .footer p {
            color: #6c757d !important;
            font-size: 12px;
            margin: 0;
            font-weight: 400;
        }
        
        /* أعمدة الجدول */
        .col-day { width: 60px; }
        .col-date { width: 120px; }
        .col-day-name { width: 120px; }
        .col-status { width: 100px; }
        .col-notes { width: auto; }
        
        /* تحسينات الطباعة */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
                margin: 0;
            }
            
            .container {
                box-shadow: none;
                border: 2px solid #8b5cf6;
                margin: 0;
                padding: 12px;
                background: #ffffff !important;
            }
            
            .header {
                page-break-after: avoid;
                margin-bottom: 12px;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .employee-card, .company-info {
                page-break-after: avoid;
                margin-bottom: 12px;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .report-details {
                page-break-after: avoid;
                margin-bottom: 12px;
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                justify-content: space-between !important;
                gap: 10px;
                width: 100%;
                clear: both !important;
            }
            
            .detail-card {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
                page-break-inside: avoid;
                flex: 1 1 auto !important;
                flex-basis: 0 !important;
                min-width: 0;
                max-width: none !important;
                width: auto !important;
                box-sizing: border-box;
                float: none !important;
            }
            
            .attendance-table {
                page-break-inside: auto;
            }
            
            .attendance-table thead {
                display: table-header-group;
            }
            
            .attendance-table tr {
                page-break-inside: avoid;
            }
            
            .present, .absent, .vacation {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .absent, .attendance-mark.absent, span.attendance-mark.absent {
                background: #ffffff !important;
                color: #dc3545 !important;
                border: 2px solid #dc3545 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .vacation, .attendance-mark.vacation, span.attendance-mark.vacation {
                background: #ffffff !important;
                color: #f59e0b !important;
                border: 2px solid #f59e0b !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .header, .employee-card, .company-info, .detail-card, .summary-section, .footer {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .attendance-table th {
                background: #8b5cf6 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .attendance-table td {
                background: #ffffff !important;
                color: #212529 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .attendance-table tr:nth-child(even) td {
                background: #f8f9fa !important;
                color: #212529 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>تقرير الحضور الفردي</h1>
            <p>Individual Attendance Report - Elegant Design</p>
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
                                    <span class="attendance-mark vacation" style="background: #ffffff !important; color: #dc3545 !important; border: 2px solid #dc3545 !important;">X</span>
                                @endif
                            @else
                                @if ($record->start_date)
                                    @if ($day['date_carbon']->isBetween($record->start_date, $record->end_date))
                                        <span class="attendance-mark present">✓</span>
                                    @else
                                        @if ($record->status === 'new_registration')
                                            <span class="attendance-mark absent" style="background: #ffffff !important; color: #dc3545 !important; border: 2px solid #dc3545 !important;">✗</span>
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
        </div>
    </div>
</body>
</html>

