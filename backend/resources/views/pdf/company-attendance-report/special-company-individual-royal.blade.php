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
            background: #f5f5f5;
            color: #2c3e50;
            line-height: 1.5;
            padding: 20px;
        }
        
        .container {
            background: #ffffff;
            border: 4px solid #d4af37;
            border-radius: 16px;
            padding: 30px;
            margin: 0 auto;
            max-width: 1400px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15), inset 0 0 60px rgba(212, 175, 55, 0.02);
        }
        
        .header {
            text-align: center;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%);
            color: #2c3e50;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 3px solid #d4af37;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #d4af37 0%, #f4d76e 30%, #d4af37 70%, #f4d76e 100%);
            box-shadow: 0 2px 8px rgba(212, 175, 55, 0.6);
        }
        
        .header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, rgba(212, 175, 55, 0.5) 50%, transparent 100%);
        }
        
        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: 700;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
            color: #2c3e50 !important;
            letter-spacing: 1px;
        }
        
        .header p {
            font-size: 17px;
            color: #d4af37 !important;
            font-weight: 500;
            text-shadow: none;
            letter-spacing: 0.5px;
        }
        
        .employee-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%);
            padding: 28px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 3px solid #d4af37;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
        }
        
        .employee-card::before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 6px;
            background: linear-gradient(180deg, #d4af37 0%, #f4d76e 50%, #d4af37 100%);
            box-shadow: -2px 0 8px rgba(212, 175, 55, 0.5);
        }
        
        .employee-card::after {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(180deg, transparent 0%, rgba(212, 175, 55, 0.3) 50%, transparent 100%);
        }
        
        .employee-name {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50 !important;
            margin-bottom: 18px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
            letter-spacing: 0.5px;
        }
        
        .employee-details {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }
        
        .employee-detail {
            background: #ffffff;
            padding: 15px 18px;
            border-radius: 10px;
            font-size: 15px;
            border: 2px solid #d4af37;
            color: #2c3e50 !important;
            font-weight: 500;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }
        
        .company-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 3px solid #d4af37;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }
        
        .company-name {
            font-size: 22px;
            font-weight: 700;
            color: #2c3e50 !important;
            margin-bottom: 10px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
            letter-spacing: 0.5px;
        }
        
        .company-subtitle {
            color: #d4af37 !important;
            font-size: 17px;
            font-weight: 600;
            text-shadow: none;
        }
        
        .report-details {
            width: 100%;
            margin-bottom: 25px;
            overflow: hidden;
            display: block;
        }
        
        .detail-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 50%, #ffffff 100%);
            border: 3px solid #d4af37;
            padding: 18px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
            float: right;
            width: 23%;
            margin-left: 2.66%;
            box-sizing: border-box;
        }
        
        .detail-card:first-child {
            margin-left: 0;
        }
        
        .report-details::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .detail-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #d4af37 0%, #f4d76e 40%, #d4af37 80%, #f4d76e 100%);
            box-shadow: 0 2px 6px rgba(212, 175, 55, 0.5);
        }
        
        .detail-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, rgba(212, 175, 55, 0.3) 50%, transparent 100%);
        }
        
        .detail-label {
            font-weight: 600;
            color: #6c757d !important;
            font-size: 13px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: none;
        }
        
        .detail-value {
            font-size: 24px;
            font-weight: 700;
            color: #d4af37 !important;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            border: 3px solid #d4af37;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5), inset 0 0 40px rgba(212, 175, 55, 0.05);
        }
        
        .attendance-table th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%) !important;
            color: #2c3e50 !important;
            padding: 16px 10px;
            text-align: center;
            font-weight: 700;
            font-size: 14px;
            border: 2px solid #d4af37;
            text-shadow: none;
            letter-spacing: 0.5px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }
        
        .attendance-table td {
            padding: 16px 10px;
            text-align: center;
            border: 1px solid rgba(212, 175, 55, 0.3);
            background: #ffffff;
            font-size: 14px;
            color: #2c3e50 !important;
        }
        
        .attendance-table tr:nth-child(even) td {
            background: #f8f9fa;
        }
        
        .attendance-mark {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            line-height: 30px;
            font-weight: 700;
            font-size: 14px;
            text-align: center;
            box-shadow: 0 3px 8px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(0, 0, 0, 0.2);
        }
        
        .present {
            background: linear-gradient(135deg, #d4af37 0%, #f4d76e 100%) !important;
            color: #0a1f3d !important;
            font-weight: 700;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.3);
        }
        
        .absent {
            background: #ffffff !important;
            color: #dc3545 !important;
            border: 2px solid #dc3545 !important;
            font-weight: 700 !important;
            text-shadow: none;
        }
        
        .attendance-mark.absent {
            background: #ffffff !important;
            color: #dc3545 !important;
            border: 2px solid #dc3545 !important;
            font-weight: 700 !important;
            text-shadow: none !important;
        }
        
        span.attendance-mark.absent {
            background: #ffffff !important;
            color: #dc3545 !important;
            border: 2px solid #dc3545 !important;
            font-weight: 700 !important;
            text-shadow: none !important;
        }
        
        .vacation {
            background: #ffffff !important;
            color: #dc3545 !important;
            border: 2px solid #dc3545 !important;
            font-weight: 700 !important;
            text-shadow: none;
        }
        
        .attendance-mark.vacation {
            background: #ffffff !important;
            color: #dc3545 !important;
            border: 2px solid #dc3545 !important;
            font-weight: 700 !important;
            text-shadow: none !important;
        }
        
        span.attendance-mark.vacation {
            background: #ffffff !important;
            color: #dc3545 !important;
            border: 2px solid #dc3545 !important;
            font-weight: 700 !important;
            text-shadow: none !important;
        }
        
        .day-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%) !important;
            color: #2c3e50 !important;
            border: 2px solid #d4af37 !important;
        }
        
        .vacation-day {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%) !important;
            color: #856404 !important;
            border: 2px solid #d4af37 !important;
        }
        
        .summary-section {
            margin-top: 30px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%);
            padding: 28px;
            border-radius: 12px;
            border: 3px solid #d4af37;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }
        
        .summary-title {
            font-size: 22px;
            font-weight: 700;
            color: #2c3e50 !important;
            margin-bottom: 22px;
            text-align: center;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
            letter-spacing: 0.5px;
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
        }
        
        .stat-item {
            background: #ffffff;
            padding: 22px;
            border-radius: 12px;
            text-align: center;
            border: 3px solid #d4af37;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        
        .stat-present { color: #d4af37 !important; }
        .stat-absent { color: #dc3545 !important; }
        .stat-vacation { color: #f39c12 !important; }
        .stat-total { color: #d4af37 !important; }
        
        .stat-label {
            font-size: 14px;
            color: #6c757d !important;
            font-weight: 600;
            text-shadow: none;
        }
        
        .logo {
            max-width: 100px;
            height: auto;
            border-radius: 8px;
            border: 3px solid #d4af37;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4), 0 0 20px rgba(212, 175, 55, 0.3);
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            padding: 22px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%);
            border-radius: 12px;
            border-top: 4px solid #d4af37;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.8);
            position: relative;
        }
        
        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #d4af37 0%, #f4d76e 50%, #d4af37 100%);
        }
        
        .footer p {
            color: #6c757d !important;
            font-size: 13px;
            margin: 0;
            font-weight: 500;
            text-shadow: none;
            letter-spacing: 0.3px;
        }
        
        /* تحسينات الطباعة */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            
            .container {
                box-shadow: none;
                border: 3px solid #d4af37;
                margin: 0;
                padding: 15px;
                background: #ffffff !important;
            }
            
            .present, .absent, .vacation {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .absent, .attendance-mark.absent, span.attendance-mark.absent {
                background: #ffffff !important;
                color: #dc3545 !important;
                border: 2px solid #dc3545 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .vacation, .attendance-mark.vacation, span.attendance-mark.vacation {
                background: #ffffff !important;
                color: #dc3545 !important;
                border: 2px solid #dc3545 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .header, .employee-card, .company-info, .detail-card, .summary-section, .footer {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .attendance-table th {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%) !important;
                color: #2c3e50 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .attendance-table td {
                background: #ffffff !important;
                color: #2c3e50 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .attendance-table tr:nth-child(even) td {
                background: #f8f9fa !important;
                color: #2c3e50 !important;
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

