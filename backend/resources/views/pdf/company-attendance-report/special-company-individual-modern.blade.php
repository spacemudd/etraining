<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>تقرير الحضور الفردي - التصميم الحديث</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            line-height: 1.5;
            padding: 15px;
            min-height: 100vh;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 30px;
            margin: 0 auto;
            max-width: 1400px;
            min-width: 1200px;
            position: relative;
            overflow: hidden;
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1, #f9ca24, #f0932b);
        }
        
        .header {
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .header h1 {
            font-size: 32px;
            margin-bottom: 8px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
            font-weight: 300;
        }
        
        .employee-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 8px 25px rgba(240, 147, 251, 0.3);
        }
        
        .employee-info h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .employee-details {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 15px;
        }
        
        .employee-detail {
            background: rgba(255,255,255,0.2);
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .company-info {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 6px 20px rgba(168, 237, 234, 0.3);
        }
        
        .company-details h3 {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .company-details p {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .logo {
            max-width: 80px;
            height: auto;
            border-radius: 8px;
            border: 2px solid rgba(44,62,80,0.1);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .report-details {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .detail-card {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 6px 20px rgba(116, 185, 255, 0.3);
        }
        
        .detail-label {
            font-weight: 600;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.9;
        }
        
        .detail-value {
            font-size: 24px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .attendance-table th {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 15px 8px;
            text-align: center;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
        }
        
        .attendance-table th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #3498db, #9b59b6, #e74c3c, #f39c12);
        }
        
        .attendance-table td {
            padding: 15px 8px;
            text-align: center;
            border-bottom: 1px solid #ecf0f1;
            background: white;
            font-size: 12px;
            transition: background-color 0.3s ease;
        }
        
        .attendance-table tr:nth-child(even) td {
            background: #f8f9fa;
        }
        
        .attendance-table tr:hover td {
            background: #e3f2fd;
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
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: transform 0.2s ease;
        }
        
        .attendance-mark:hover {
            transform: scale(1.1);
        }
        
        .present {
            background: linear-gradient(135deg, #00b894, #00cec9);
            color: white;
        }
        
        .absent {
            background: linear-gradient(135deg, #e17055, #d63031);
            color: white;
        }
        
        .vacation {
            background: linear-gradient(135deg, #fdcb6e, #e17055);
            color: white;
        }
        
        .day-header {
            background: linear-gradient(135deg, #74b9ff, #0984e3) !important;
            color: white !important;
        }
        
        .vacation-day {
            background: linear-gradient(135deg, #fab1a0, #e17055) !important;
            color: white !important;
        }
        
        .summary-section {
            margin-top: 30px;
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(255, 236, 210, 0.4);
        }
        
        .summary-title {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        
        .stat-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-present { color: #00b894; }
        .stat-absent { color: #e17055; }
        .stat-vacation { color: #fdcb6e; }
        .stat-total { color: #74b9ff; }
        
        .stat-label {
            font-size: 12px;
            color: #7f8c8d;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 12px;
            border-top: 3px solid #3498db;
        }
        
        .footer p {
            color: #6c757d;
            font-size: 12px;
            margin: 0;
            font-style: italic;
        }
        
        /* تحسينات الطباعة */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .container {
                box-shadow: none;
                border: 1px solid #ddd;
                margin: 0;
                padding: 20px;
            }
            
            .header, .employee-card, .company-info, .detail-card, .summary-section {
                background: #f8f9fa !important;
                color: #333 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .attendance-table th {
                background: #2c3e50 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .present, .absent, .vacation {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
        
        /* أعمدة الجدول */
        .col-day { width: 25px; }
        .col-date { width: 80px; }
        .col-day-name { width: 80px; }
        .col-status { width: 60px; }
        .col-notes { width: 150px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>��� تقرير الحضور الفردي المتقدم</h1>
            <p>Advanced Individual Attendance Report</p>
        </div>
        
        <!-- Employee Card -->
        <div class="employee-card">
            <div class="employee-info">
                <h2>{{ $record->trainee->name }}</h2>
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
        </div>
        
        <!-- Company Info -->
        <div class="company-info">
            <div class="company-details">
                <h3>{{ $record->company->name_ar }}</h3>
                <p>{{ $record->company->name_en }}</p>
            </div>
            @if ($base64logo)
                <img src="{{ $base64logo }}" alt="Company Logo" class="logo">
            @endif
        </div>
        
        <!-- Report Details -->
        <div class="report-details">
            <div class="detail-card">
                <div class="detail-label">��� من تاريخ</div>
                <div class="detail-value">{{ $report->date_from->format('Y/m/d') }}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">��� إلى تاريخ</div>
                <div class="detail-value">{{ $report->date_to->format('Y/m/d') }}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">���️ إجمالي الأيام</div>
                <div class="detail-value">{{ count($days) }}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">��� أيام العمل</div>
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
                                    <span style="color: #bdc3c7;">-</span>
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
                                            <span style="color: #bdc3c7;">-</span>
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
                                    <span style="color: #7f8c8d;">بعد الاستقالة</span>
                                @else
                                    <span style="color: #f39c12;">عطلة أسبوعية</span>
                                @endif
                            @else
                                @if ($record->start_date)
                                    @if ($day['date_carbon']->isBetween($record->start_date, $record->end_date))
                                        <span style="color: #00b894;">حضور</span>
                                    @else
                                        @if ($record->status === 'new_registration')
                                            <span style="color: #e74c3c;">غياب</span>
                                        @else
                                            <span style="color: #7f8c8d;">قبل الالتحاق</span>
                                        @endif
                                    @endif
                                @else
                                    <span style="color: #00b894;">حضور</span>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-title">��� ملخص الحضور والانصراف</div>
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
            <p>��� تقرير فردي سري - تم إنشاؤه تلقائياً في {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
