<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>تقرير الحضور الفردي</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.4;
            padding: 20px;
        }
        
        .container {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 25px;
            margin: 0 auto;
            max-width: 1400px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            background: #007bff;
            color: white;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .employee-card {
            background: #e9ecef;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-right: 4px solid #007bff;
        }
        
        .employee-name {
            font-size: 22px;
            font-weight: bold;
            color: #495057;
            margin-bottom: 10px;
        }
        
        .employee-details {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        
        .employee-detail {
            background: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
            border: 1px solid #dee2e6;
        }
        
        .company-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
        }
        
        .company-subtitle {
            color: #6c757d;
            font-size: 14px;
        }
        
        .report-details {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .detail-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
        }
        
        .detail-label {
            font-weight: bold;
            color: #495057;
            font-size: 12px;
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            overflow: hidden;
        }
        
        .attendance-table th {
            background: #495057;
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            border: 1px solid #6c757d;
        }
        
        .attendance-table td {
            padding: 12px 8px;
            text-align: center;
            border: 1px solid #dee2e6;
            background: white;
            font-size: 12px;
        }
        
        .attendance-table tr:nth-child(even) td {
            background: #f8f9fa;
        }
        
        .attendance-mark {
            display: inline-block;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            line-height: 25px;
            font-weight: bold;
            font-size: 12px;
            text-align: center;
        }
        
        .present {
            background: #28a745;
            color: white;
        }
        
        .absent {
            background: #dc3545;
            color: white;
        }
        
        .vacation {
            background: #ffc107;
            color: #212529;
        }
        
        .day-header {
            background: #007bff !important;
            color: white !important;
        }
        
        .vacation-day {
            background: #6c757d !important;
            color: white !important;
        }
        
        .summary-section {
            margin-top: 20px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }
        
        .summary-title {
            font-size: 18px;
            font-weight: bold;
            color: #495057;
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
            border-radius: 6px;
            text-align: center;
            border: 1px solid #dee2e6;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-present { color: #28a745; }
        .stat-absent { color: #dc3545; }
        .stat-vacation { color: #ffc107; }
        .stat-total { color: #007bff; }
        
        .stat-label {
            font-size: 12px;
            color: #6c757d;
            font-weight: bold;
        }
        
        .logo {
            max-width: 70px;
            height: auto;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        
        /* تحسينات الطباعة */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .container {
                box-shadow: none;
                border: 1px solid #000;
                margin: 0;
                padding: 15px;
            }
            
            .present, .absent, .vacation {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
        
        /* أعمدة الجدول */
        .col-day { width: 60px; }
        .col-date { width: 80px; }
        .col-day-name { width: 80px; }
        .col-status { width: 60px; }
        .col-notes { width: 120px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>تقرير الحضور الفردي</h1>
            <p>Individual Attendance Report</p>
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
                <img src="{{ $base64logo }}" alt="Company Logo" class="logo" style="float: left; margin-top: -30px;">
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
                                    <span style="color: #6c757d;">-</span>
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
                                            <span style="color: #6c757d;">-</span>
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
                                    <span style="color: #6c757d;">بعد الاستقالة</span>
                                @else
                                    <span style="color: #ffc107;">عطلة أسبوعية</span>
                                @endif
                            @else
                                @if ($record->start_date)
                                    @if ($day['date_carbon']->isBetween($record->start_date, $record->end_date))
                                        <span style="color: #28a745;">حضور</span>
                                    @else
                                        @if ($record->status === 'new_registration')
                                            <span style="color: #dc3545;">غياب</span>
                                        @else
                                            <span style="color: #6c757d;">قبل الالتحاق</span>
                                        @endif
                                    @endif
                                @else
                                    <span style="color: #28a745;">حضور</span>
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
        <div style="margin-top: 20px; text-align: center; padding: 15px; background: #f8f9fa; border-radius: 6px; border-top: 2px solid #007bff;">
            <p style="color: #6c757d; font-size: 12px; margin: 0;">تقرير فردي - تم إنشاؤه تلقائياً في {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
